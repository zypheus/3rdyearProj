<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Payment Controller
 * 
 * Manages loan payments.
 * 
 * RBAC Permissions:
 * - Member: View own payments
 * - Officer: Record payments, view all
 * - Admin: Full access
 */
class PaymentController extends Controller
{
    /**
     * Display payments for a loan.
     */
    public function index(Loan $loan)
    {
        $user = Auth::user();

        // Members can only see their own loan payments
        if ($user->isMember() && $loan->user_id !== $user->id) {
            abort(403, 'You can only view payments for your own loans.');
        }

        $payments = $loan->payments()
            ->with('recorder')
            ->orderBy('payment_date', 'desc')
            ->get();

        // Calculate totals
        $totalPaid = $payments->where('status', Payment::STATUS_CONFIRMED)->sum('amount');
        $principalPaid = $payments->where('status', Payment::STATUS_CONFIRMED)->sum('principal_amount');
        $interestPaid = $payments->where('status', Payment::STATUS_CONFIRMED)->sum('interest_amount');
        $remainingBalance = ($loan->approved_amount ?? $loan->amount) - $principalPaid;

        return view('payments.index', [
            'loan' => $loan,
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'principalPaid' => $principalPaid,
            'interestPaid' => $interestPaid,
            'remainingBalance' => max(0, $remainingBalance),
            'canRecord' => $user->isAdminOrOfficer() && $loan->isActive(),
        ]);
    }

    /**
     * Show the form for recording a new payment.
     * Officer/Admin only.
     */
    public function create(Loan $loan)
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can record payments.');
        }

        if (!$loan->isActive()) {
            return back()->with('error', 'Payments can only be recorded for active loans.');
        }

        return view('payments.create', [
            'loan' => $loan,
            'paymentMethods' => Payment::METHODS,
        ]);
    }

    /**
     * Store a newly recorded payment.
     * Officer/Admin only.
     */
    public function store(Request $request, Loan $loan)
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can record payments.');
        }

        if (!$loan->isActive()) {
            return back()->with('error', 'Payments can only be recorded for active loans.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'principal_amount' => ['nullable', 'numeric', 'min:0'],
            'interest_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', 'in:' . implode(',', Payment::METHODS)],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Default split if not provided
        $principalAmount = $validated['principal_amount'] ?? $validated['amount'];
        $interestAmount = $validated['interest_amount'] ?? 0;

        $payment = Payment::create([
            'loan_id' => $loan->id,
            'user_id' => $loan->user_id,
            'amount' => $validated['amount'],
            'principal_amount' => $principalAmount,
            'interest_amount' => $interestAmount,
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => Payment::STATUS_CONFIRMED, // Auto-confirm when officer records
            'recorded_by' => $user->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditService::log(
            AuditService::ACTION_PAYMENT_RECORDED,
            'Payment',
            $payment->id,
            null,
            [
                'loan_id' => $loan->id,
                'amount' => $payment->amount,
                'recorded_by' => $user->id,
            ]
        );

        // Update loan outstanding balance and total paid
        $this->updateLoanBalance($loan);

        // Check if loan should be marked as completed
        $this->checkLoanCompletion($loan);

        return redirect()->route('loans.payments.index', $loan)
            ->with('success', 'Payment of ₱' . number_format($payment->amount, 2) . ' recorded successfully.');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $user = Auth::user();

        // Members can only view their own payments
        if ($user->isMember() && $payment->user_id !== $user->id) {
            abort(403, 'You can only view your own payments.');
        }

        $payment->load(['loan', 'recorder']);

        return view('payments.show', [
            'payment' => $payment,
        ]);
    }

    /**
     * Update payment status (confirm/reject pending payments).
     * Officer/Admin only.
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can update payment status.');
        }

        if (!$payment->isPending()) {
            return back()->with('error', 'Only pending payments can be updated.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,rejected'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $payment->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $payment->notes,
        ]);

        if ($validated['status'] === Payment::STATUS_CONFIRMED) {
            $this->updateLoanBalance($payment->loan);
            $this->checkLoanCompletion($payment->loan);
        }

        return back()->with('success', 'Payment status updated to ' . $validated['status'] . '.');
    }

    /**
     * Update loan outstanding balance and total paid.
     */
    protected function updateLoanBalance(Loan $loan): void
    {
        $totalPaid = $loan->payments()
            ->where('status', Payment::STATUS_CONFIRMED)
            ->sum('amount');

        $totalPrincipalPaid = $loan->payments()
            ->where('status', Payment::STATUS_CONFIRMED)
            ->sum('principal_amount');

        $loanAmount = $loan->approved_amount ?? $loan->amount;
        $outstandingBalance = max(0, $loanAmount - $totalPrincipalPaid);

        $loan->update([
            'total_paid' => $totalPaid,
            'outstanding_balance' => $outstandingBalance,
        ]);
    }

    /**
     * Check if a loan should be marked as completed.
     */
    protected function checkLoanCompletion(Loan $loan): void
    {
        $totalPrincipalPaid = $loan->payments()
            ->where('status', Payment::STATUS_CONFIRMED)
            ->sum('principal_amount');

        $loanAmount = $loan->approved_amount ?? $loan->amount;

        // If principal is fully paid, mark loan as completed
        if ($totalPrincipalPaid >= $loanAmount) {
            $loan->update(['status' => 'completed']);

            AuditService::logLoanAction(
                'loan_completed',
                $loan->id,
                ['completed_by_system' => true]
            );
        }
    }

    /**
     * Show payment schedule for a loan.
     */
    public function schedule(Loan $loan)
    {
        $user = Auth::user();

        // Members can only see their own loan schedule
        if ($user->isMember() && $loan->user_id !== $user->id) {
            abort(403, 'You can only view the schedule for your own loans.');
        }

        if (!$loan->isActive() && !$loan->isApproved() && !$loan->isCompleted()) {
            return back()->with('error', 'Payment schedule is only available for approved or active loans.');
        }

        $schedule = $this->generatePaymentSchedule($loan);

        return view('payments.schedule', [
            'loan' => $loan,
            'schedule' => $schedule,
        ]);
    }

    /**
     * Generate amortization schedule.
     */
    protected function generatePaymentSchedule(Loan $loan): array
    {
        $principal = $loan->approved_amount ?? $loan->amount;
        $termMonths = $loan->term_months;
        $annualRate = $loan->interest_rate;
        $monthlyRate = $annualRate / 100 / 12;

        $schedule = [];

        if ($monthlyRate == 0) {
            $monthlyPayment = $principal / $termMonths;
            $balance = $principal;

            for ($month = 1; $month <= $termMonths; $month++) {
                $principalPayment = $monthlyPayment;
                $interestPayment = 0;
                $balance -= $principalPayment;

                $schedule[] = [
                    'month' => $month,
                    'payment' => round($monthlyPayment, 2),
                    'principal' => round($principalPayment, 2),
                    'interest' => 0,
                    'balance' => round(max(0, $balance), 2),
                ];
            }
        } else {
            $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / 
                              (pow(1 + $monthlyRate, $termMonths) - 1);
            $balance = $principal;

            for ($month = 1; $month <= $termMonths; $month++) {
                $interestPayment = $balance * $monthlyRate;
                $principalPayment = $monthlyPayment - $interestPayment;
                $balance -= $principalPayment;

                $schedule[] = [
                    'month' => $month,
                    'payment' => round($monthlyPayment, 2),
                    'principal' => round($principalPayment, 2),
                    'interest' => round($interestPayment, 2),
                    'balance' => round(max(0, $balance), 2),
                ];
            }
        }

        return $schedule;
    }
}
