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
     * Officer/Admin for regular payments, Member for advance payments.
     */
    public function create(Loan $loan)
    {
        $user = Auth::user();
        $simulation = config('payments.simulation_mode');
        $isAdvanceRequest = request('advance') == 1;

        // Member advance payment path
        if ($isAdvanceRequest && $user->isMember() && $loan->user_id === $user->id) {
            if (!$loan->isActive()) {
                return back()->with('error', 'Advance payments can only be submitted for active loans.');
            }
            return view('payments.create', [
                'loan' => $loan,
                'paymentMethods' => [Payment::METHOD_ONLINE],
                'simulationMode' => false,
                'isSimulationMember' => false,
            ]);
        }

        // Simulation path: member can access their own active loan
        if ($simulation && $user->isMember() && $loan->user_id === $user->id) {
            if (!$loan->isActive()) {
                return back()->with('error', 'Payments can only be recorded for active loans.');
            }
            return view('payments.create', [
                'loan' => $loan,
                // In simulation restrict to online method for clarity
                'paymentMethods' => [Payment::METHOD_ONLINE],
                'simulationMode' => true,
                'isSimulationMember' => true,
            ]);
        }

        // Normal officer/admin path
        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can record payments.');
        }
        if (!$loan->isActive()) {
            return back()->with('error', 'Payments can only be recorded for active loans.');
        }
        return view('payments.create', [
            'loan' => $loan,
            'paymentMethods' => Payment::METHODS,
            'simulationMode' => $simulation,
            'isSimulationMember' => false,
        ]);
    }

    /**
     * Store a newly recorded payment.
     * Officer/Admin only.
     */
    public function store(Request $request, Loan $loan)
    {
        $user = Auth::user();
        $simulation = config('payments.simulation_mode');

        // Simulation path: member creating their own pending payment
        if ($simulation && $user->isMember() && $loan->user_id === $user->id) {
            if (!$loan->isActive()) {
                return back()->with('error', 'Payments can only be recorded for active loans.');
            }
            $validated = $request->validate([
                'amount' => ['required', 'numeric', 'min:1'],
                'payment_date' => ['required', 'date', 'before_or_equal:today'],
                // restrict to online method for simulation clarity
                'payment_method' => ['required', 'in:' . Payment::METHOD_ONLINE],
                'notes' => ['nullable', 'string', 'max:500'],
            ]);

            $amount = $validated['amount'];
            // For simulation we treat full amount as principal; interest stays 0
            $payment = Payment::create([
                'loan_id' => $loan->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'principal_amount' => $amount,
                'interest_amount' => 0,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'status' => Payment::STATUS_PENDING,
                'recorded_by' => null,
                'notes' => $validated['notes'] ?? null,
            ]);

            AuditService::log(
                AuditService::ACTION_PAYMENT_SIMULATION_STARTED,
                'Payment',
                $payment->id,
                null,
                [
                    'loan_id' => $loan->id,
                    'amount' => $payment->amount,
                    'member_id' => $user->id,
                ]
            );

            return redirect()->route('payments.show', $payment)
                ->with('success', 'Simulated payment created. You may now confirm or reject it.');
        }

        // Officer/Admin normal path
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
            'status' => Payment::STATUS_CONFIRMED,
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

        $this->updateLoanBalance($loan);
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

        $simulation = config('payments.simulation_mode');
        $simulationDelay = config('payments.simulation_delay_ms');

        return view('payments.show', [
            'payment' => $payment,
            'simulationMode' => $simulation,
            'simulationDelayMs' => $simulationDelay,
        ]);
    }

    /**
     * Update payment status (confirm/reject pending payments).
     * Officer/Admin only.
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $user = Auth::user();
        $simulation = config('payments.simulation_mode');

        $memberSimulationAllowed = $simulation && $user->isMember() && $payment->user_id === $user->id && $payment->isPending();
        $officerPathAllowed = $user->isAdminOrOfficer() && $payment->isPending();

        if (!$memberSimulationAllowed && !$officerPathAllowed) {
            abort(403, 'You are not allowed to update this payment status.');
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
            // Only update balances when confirmed
            $this->updateLoanBalance($payment->loan);
            $this->checkLoanCompletion($payment->loan);
            // If linked to schedule, mark entry paid
            if ($payment->payment_schedule_id) {
                $payment->schedule?->update(['status' => \App\Models\PaymentSchedule::STATUS_PAID]);
            }
            AuditService::log(
                $memberSimulationAllowed ? AuditService::ACTION_PAYMENT_SIMULATION_CONFIRMED : AuditService::ACTION_PAYMENT_RECORDED,
                'Payment',
                $payment->id,
                null,
                ['confirmed_by' => $user->id]
            );
        } elseif ($memberSimulationAllowed && $validated['status'] === Payment::STATUS_REJECTED) {
            AuditService::log(
                AuditService::ACTION_PAYMENT_SIMULATION_FAILED,
                'Payment',
                $payment->id,
                null,
                ['rejected_by' => $user->id]
            );
        }

        return back()->with('success', 'Payment status updated to ' . $validated['status'] . '.');
    }

    /**
     * Member submits an advance payment (pending; requires officer confirmation).
     */
    public function storeAdvance(Request $request, Loan $loan)
    {
        $user = Auth::user();

        if (!$user->isMember() || $loan->user_id !== $user->id) {
            abort(403, 'You can only submit advance payments for your own loans.');
        }
        if (!$loan->isActive()) {
            return back()->with('error', 'Advance payments are allowed only for active loans.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', 'in:' . Payment::METHOD_ONLINE],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Optionally link to nearest future schedule entry
        $nextSchedule = \App\Models\PaymentSchedule::where('loan_id', $loan->id)
            ->whereIn('status', [\App\Models\PaymentSchedule::STATUS_PLANNED, \App\Models\PaymentSchedule::STATUS_CONFIRMED])
            ->whereDate('due_date', '>=', now()->toDateString())
            ->orderBy('sequence')
            ->first();

        $payment = Payment::create([
            'loan_id' => $loan->id,
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'principal_amount' => $validated['amount'],
            'interest_amount' => 0,
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'payment_schedule_id' => $nextSchedule?->id,
            'is_advance' => true,
            'status' => Payment::STATUS_PENDING,
            'recorded_by' => null,
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditService::log(
            AuditService::ACTION_PAYMENT_SUBMITTED_PENDING,
            'Payment',
            $payment->id,
            null,
            [
                'loan_id' => $loan->id,
                'amount' => $payment->amount,
                'member_id' => $user->id,
                'linked_schedule' => $nextSchedule?->id,
            ]
        );

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Advance payment submitted. Awaiting officer confirmation.');
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
     * Show pending payments queue for officers/admins.
     */
    public function queue()
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can access the payment verification queue.');
        }

        $payments = Payment::with(['loan.user', 'user'])
            ->where('status', Payment::STATUS_PENDING)
            ->whereHas('loan', function ($q) {
                // Only show payments for active loans
                $q->where('status', 'active');
            })
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('payments.queue', [
            'payments' => $payments,
        ]);
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
     * Generate amortization schedule using FLAT RATE method.
     */
    protected function generatePaymentSchedule(Loan $loan): array
    {
        $principal = $loan->approved_amount ?? $loan->amount;
        $termMonths = $loan->term_months;
        $annualRate = $loan->interest_rate;
        
        // Use flat rate calculation method
        $calculationMethod = config('loans.defaults.interest_calculation_method', 'flat');
        
        $schedule = [];

        if ($calculationMethod === 'flat') {
            // FLAT RATE: Interest computed on original principal for entire term
            $termYears = $termMonths / 12;
            $totalInterest = $principal * ($annualRate / 100) * $termYears;
            
            // Interest and principal per month (evenly distributed)
            $interestPerMonth = $totalInterest / $termMonths;
            $principalPerMonth = $principal / $termMonths;
            
            $balance = $principal;
            $totalPrincipalPaid = 0;
            $totalInterestPaid = 0;

            for ($month = 1; $month <= $termMonths; $month++) {
                // For all months except the last, use rounded values
                if ($month < $termMonths) {
                    $principalPayment = round($principalPerMonth, 2);
                    $interestPayment = round($interestPerMonth, 2);
                    $payment = $principalPayment + $interestPayment;
                } else {
                    // Last month: adjust to match exact totals (eliminates rounding errors)
                    $principalPayment = $principal - $totalPrincipalPaid;
                    $interestPayment = $totalInterest - $totalInterestPaid;
                    $payment = $principalPayment + $interestPayment;
                }
                
                $totalPrincipalPaid += $principalPayment;
                $totalInterestPaid += $interestPayment;
                $balance -= $principalPayment;

                $schedule[] = [
                    'month' => $month,
                    'payment' => round($payment, 2),
                    'principal' => round($principalPayment, 2),
                    'interest' => round($interestPayment, 2),
                    'balance' => round(max(0, $balance), 2),
                ];
            }
        } else {
            // DIMINISHING BALANCE: Interest computed on remaining balance
            $monthlyRate = $annualRate / 100 / 12;
            
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
        }

        return $schedule;
    }
}
