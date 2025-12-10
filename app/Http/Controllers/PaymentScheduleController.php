<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\PaymentSchedule;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentScheduleController extends Controller
{
    /**
     * Show proposed schedule for an approved/active loan.
     */
    public function propose(Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can propose schedules.');
        }
        if (!$loan->isApproved() && !$loan->isActive()) {
            return back()->with('error', 'Schedule can be proposed only for approved or active loans.');
        }

        // Generate schedule using flat rate method for all loans
        $principal = $loan->approved_amount ?? $loan->amount;
        $termMonths = $loan->term_months;
        $annualRate = $loan->interest_rate;
        $calculationMethod = config('loans.defaults.interest_calculation_method', 'flat');

        $proposed = [];
        
        if ($calculationMethod === 'flat') {
            // Flat rate: Interest computed on original principal for entire term
            $termYears = $termMonths / 12;
            $totalInterest = $principal * ($annualRate / 100) * $termYears;
            $totalAmount = $principal + $totalInterest;
            $monthlyPayment = $totalAmount / $termMonths;
            
            // Interest and principal per month
            $interestPerMonth = $totalInterest / $termMonths;
            $principalPerMonth = $principal / $termMonths;
            $remainingPrincipal = $principal;
            
            for ($seq = 1; $seq <= $termMonths; $seq++) {
                $principalComponent = round($principalPerMonth, 2);
                $interestComponent = round($interestPerMonth, 2);
                
                // Adjust last payment for rounding
                if ($seq === $termMonths) {
                    $principalComponent = $remainingPrincipal;
                    $monthlyPayment = $principalComponent + $interestComponent;
                }
                
                $proposed[] = [
                    'sequence' => $seq,
                    'due_date' => now()->addMonths($seq)->startOfDay()->toDateString(),
                    'amount' => round($monthlyPayment, 2),
                    'principal_component' => $principalComponent,
                    'interest_component' => $interestComponent,
                ];
                
                $remainingPrincipal -= $principalComponent;
            }
        } else {
            // Diminishing balance (amortization) method
            $monthlyRate = $annualRate / 100 / 12;
            
            if ($monthlyRate == 0) {
                $monthlyPayment = $principal / $termMonths;
                $balance = $principal;
                for ($seq = 1; $seq <= $termMonths; $seq++) {
                    $principalPayment = $monthlyPayment;
                    $proposed[] = [
                        'sequence' => $seq,
                        'due_date' => now()->addMonths($seq)->startOfDay()->toDateString(),
                        'amount' => round($monthlyPayment, 2),
                        'principal_component' => round($principalPayment, 2),
                        'interest_component' => 0,
                    ];
                    $balance -= $principalPayment;
                }
            } else {
                $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) /
                                  (pow(1 + $monthlyRate, $termMonths) - 1);
                $balance = $principal;
                for ($seq = 1; $seq <= $termMonths; $seq++) {
                    $interestPayment = $balance * $monthlyRate;
                    $principalPayment = $monthlyPayment - $interestPayment;
                    $proposed[] = [
                        'sequence' => $seq,
                        'due_date' => now()->addMonths($seq)->startOfDay()->toDateString(),
                        'amount' => round($monthlyPayment, 2),
                        'principal_component' => round($principalPayment, 2),
                        'interest_component' => round($interestPayment, 2),
                    ];
                    $balance -= $principalPayment;
                }
            }
        }

        return view('payments.schedule_propose', [
            'loan' => $loan,
            'entries' => $proposed,
        ]);
    }

    /**
     * Confirm proposed schedule and persist entries.
     */
    public function confirm(Request $request, Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can confirm schedules.');
        }
        if (!$loan->isApproved() && !$loan->isActive()) {
            return back()->with('error', 'Schedule can be confirmed only for approved or active loans.');
        }

        // Prevent double confirmation
        if (PaymentSchedule::where('loan_id', $loan->id)->exists()) {
            return back()->with('warning', 'Schedule already confirmed for this loan.');
        }

        $entries = $request->validate([
            'entries' => ['required', 'array'],
            'entries.*.sequence' => ['required', 'integer', 'min:1'],
            'entries.*.due_date' => ['required', 'date'],
            'entries.*.amount' => ['required', 'numeric', 'min:0'],
            'entries.*.principal_component' => ['required', 'numeric', 'min:0'],
            'entries.*.interest_component' => ['required', 'numeric', 'min:0'],
        ])['entries'];

        foreach ($entries as $e) {
            PaymentSchedule::create([
                'loan_id' => $loan->id,
                'sequence' => $e['sequence'],
                'due_date' => $e['due_date'],
                'amount' => $e['amount'],
                'principal_component' => $e['principal_component'],
                'interest_component' => $e['interest_component'],
                'status' => PaymentSchedule::STATUS_CONFIRMED,
                'confirmed_by' => $user->id,
                'confirmed_at' => now(),
            ]);
        }

        AuditService::logLoanAction(AuditService::ACTION_LOAN_SCHEDULE_CONFIRMED, $loan->id, [
            'entries' => count($entries),
            'confirmed_by' => $user->id,
        ]);

        return redirect()->route('loans.payments.index', $loan)
            ->with('success', 'Payment schedule confirmed and saved.');
    }
}
