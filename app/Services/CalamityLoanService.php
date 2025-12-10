<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Calamity Loan Service
 * 
 * Handles calamity loan-specific business logic based on Pag-IBIG guidelines:
 * - 5.95% fixed annual interest rate (FLAT RATE calculation)
 * - 2 or 3 year terms with 2-month grace period
 * - Penalty rate: 1/20 of 1% per day of delay
 * - Loanable amount: 80% of eligible amount
 * - Payment priority: Penalties → Interest → Principal
 * - Interest calculation: Flat rate (interest computed on original principal for entire term)
 */
class CalamityLoanService
{
    /**
     * Calculate the loanable amount based on eligible amount.
     * 
     * @param float $eligibleAmount The total eligible amount
     * @param float|null $percentage The loanable percentage (default 80%)
     * @return float The loanable amount
     */
    public function calculateLoanableAmount(float $eligibleAmount, ?float $percentage = null): float
    {
        $percentage = $percentage ?? config('loans.calamity.loanable_percentage', 80);
        return round($eligibleAmount * ($percentage / 100), 2);
    }

    /**
     * Get the calamity loan configuration for a new loan.
     * 
     * @return array Calamity loan default settings
     */
    public function getCalamityLoanDefaults(): array
    {
        return [
            'interest_rate' => config('loans.calamity.interest_rate', 10.5),
            'grace_period_months' => config('loans.calamity.grace_period_months', 2),
            'penalty_rate' => config('loans.calamity.penalty_rate_per_day', 0.05),
            'loanable_percentage' => config('loans.calamity.loanable_percentage', 80),
            'term_options' => config('loans.calamity.term_options', [24, 36]),
        ];
    }

    /**
     * Validate calamity loan specific requirements.
     * 
     * @param array $data The loan application data
     * @return array Validation errors (empty if valid)
     */
    public function validateCalamityLoan(array $data): array
    {
        $errors = [];
        $config = config('loans.calamity');

        // Validate term options
        $termOptions = $config['term_options'] ?? [24, 36];
        if (!in_array($data['term_months'] ?? 0, $termOptions)) {
            $errors['term_months'] = 'Calamity loans must be for ' . implode(' or ', array_map(fn($m) => ($m / 12) . ' years', $termOptions)) . '.';
        }

        // Validate eligible amount
        $minEligible = $config['min_eligible_amount'] ?? 5000;
        $maxEligible = $config['max_eligible_amount'] ?? 500000;
        $eligibleAmount = $data['eligible_amount'] ?? 0;

        if ($eligibleAmount < $minEligible) {
            $errors['eligible_amount'] = "Eligible amount must be at least ₱" . number_format($minEligible, 2);
        }

        if ($eligibleAmount > $maxEligible) {
            $errors['eligible_amount'] = "Eligible amount cannot exceed ₱" . number_format($maxEligible, 2);
        }

        return $errors;
    }

    /**
     * Prepare calamity loan data for storage.
     * Auto-sets interest rate, penalty rate, grace period, and calculates loanable amount.
     * 
     * @param array $data The validated input data
     * @return array The prepared loan data
     */
    public function prepareCalamityLoanData(array $data): array
    {
        $config = config('loans.calamity');
        $eligibleAmount = $data['eligible_amount'];
        $loanablePercentage = $config['loanable_percentage'] ?? 80;
        $loanableAmount = $this->calculateLoanableAmount($eligibleAmount, $loanablePercentage);

        return array_merge($data, [
            'interest_rate' => $config['interest_rate'] ?? 10.5,
            'grace_period_months' => $config['grace_period_months'] ?? 2,
            'penalty_rate' => $config['penalty_rate_per_day'] ?? 0.05,
            'loanable_percentage' => $loanablePercentage,
            'amount' => $loanableAmount, // The loanable amount becomes the loan amount
        ]);
    }

    /**
     * Generate payment schedule with grace period.
     * 
     * @param Loan $loan The loan to generate schedule for
     * @param Carbon|null $disbursementDate Override disbursement date (defaults to loan's date)
     * @return Collection Collection of payment schedule entries
     */
    public function generateScheduleWithGracePeriod(Loan $loan, ?Carbon $disbursementDate = null): Collection
    {
        $disbursementDate = $disbursementDate ?? Carbon::parse($loan->disbursement_date) ?? now();
        $gracePeriod = $loan->grace_period_months ?? 0;
        $termMonths = $loan->term_months;
        $principal = $loan->approved_amount ?? $loan->amount;
        $annualRate = $loan->interest_rate;
        
        // Use flat rate calculation method
        $calculationMethod = config('loans.calamity.interest_calculation_method', 'flat');

        // First payment is after grace period + 1 month
        $firstDueDate = $disbursementDate->copy()->addMonths($gracePeriod + 1);

        if ($calculationMethod === 'flat') {
            // Flat rate: Interest computed on original principal for entire term
            $termYears = $termMonths / 12;
            $totalInterest = $principal * ($annualRate / 100) * $termYears;
            
            // Interest per month is distributed evenly
            $interestPerMonth = $totalInterest / $termMonths;
            $principalPerMonth = $principal / $termMonths;
            
            $schedules = collect();
            $remainingPrincipal = $principal;
            $totalPrincipalPaid = 0;
            $totalInterestPaid = 0;

            for ($i = 1; $i <= $termMonths; $i++) {
                $dueDate = $firstDueDate->copy()->addMonths($i - 1);
                
                // For all months except the last, use rounded values
                if ($i < $termMonths) {
                    $principalComponent = round($principalPerMonth, 2);
                    $interestComponent = round($interestPerMonth, 2);
                    $monthlyPayment = $principalComponent + $interestComponent;
                } else {
                    // Last payment: adjust to match exact totals (eliminates rounding errors)
                    $principalComponent = $principal - $totalPrincipalPaid;
                    $interestComponent = $totalInterest - $totalInterestPaid;
                    $monthlyPayment = $principalComponent + $interestComponent;
                }
                
                $totalPrincipalPaid += $principalComponent;
                $totalInterestPaid += $interestComponent;

                $schedules->push([
                    'loan_id' => $loan->id,
                    'sequence' => $i,
                    'due_date' => $dueDate->toDateString(),
                    'amount' => round($monthlyPayment, 2),
                    'principal_component' => round($principalComponent, 2),
                    'interest_component' => round($interestComponent, 2),
                    'penalty_amount' => 0,
                    'days_delayed' => 0,
                    'status' => PaymentSchedule::STATUS_PLANNED,
                ]);

                $remainingPrincipal -= $principalComponent;
            }
        } else {
            // Diminishing balance method (original amortization)
            $monthlyRate = $annualRate / 100 / 12;
            $monthlyPayment = $this->calculateMonthlyPayment($principal, $monthlyRate, $termMonths);

            $schedules = collect();
            $remainingBalance = $principal;

            for ($i = 1; $i <= $termMonths; $i++) {
                $dueDate = $firstDueDate->copy()->addMonths($i - 1);
                
                // Calculate interest and principal components
                $interestComponent = round($remainingBalance * $monthlyRate, 2);
                $principalComponent = round($monthlyPayment - $interestComponent, 2);

                // Adjust last payment for rounding differences
                if ($i === $termMonths) {
                    $principalComponent = $remainingBalance;
                    $monthlyPayment = $principalComponent + $interestComponent;
                }

                $schedules->push([
                    'loan_id' => $loan->id,
                    'sequence' => $i,
                    'due_date' => $dueDate->toDateString(),
                    'amount' => round($monthlyPayment, 2),
                    'principal_component' => $principalComponent,
                    'interest_component' => $interestComponent,
                    'penalty_amount' => 0,
                    'days_delayed' => 0,
                    'status' => PaymentSchedule::STATUS_PLANNED,
                ]);

                $remainingBalance -= $principalComponent;
            }
        }

        return $schedules;
    }

    /**
     * Calculate monthly payment using standard amortization formula.
     * 
     * @param float $principal The loan principal
     * @param float $monthlyRate The monthly interest rate (as decimal, e.g., 0.00875 for 10.5%/12)
     * @param int $termMonths The loan term in months
     * @return float The monthly payment amount
     */
    public function calculateMonthlyPayment(float $principal, float $monthlyRate, int $termMonths): float
    {
        if ($monthlyRate == 0) {
            return round($principal / $termMonths, 2);
        }

        $payment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) 
                   / (pow(1 + $monthlyRate, $termMonths) - 1);

        return round($payment, 2);
    }
    
    /**
     * Calculate monthly payment using flat rate method.
     * Interest is computed on the original principal for the entire term.
     * 
     * @param float $principal The loan principal
     * @param float $annualRate The annual interest rate (as percentage, e.g., 5.95 for 5.95%)
     * @param int $termMonths The loan term in months
     * @return float The monthly payment amount
     */
    public function calculateMonthlyPaymentFlatRate(float $principal, float $annualRate, int $termMonths): float
    {
        // Calculate total interest for entire term
        $termYears = $termMonths / 12;
        $totalInterest = $principal * ($annualRate / 100) * $termYears;
        
        // Total amount to repay
        $totalAmount = $principal + $totalInterest;
        
        // Monthly payment
        $monthlyPayment = $totalAmount / $termMonths;
        
        return round($monthlyPayment, 2);
    }

    /**
     * Calculate penalty for a late payment.
     * 
     * @param float $principalAmount The outstanding principal
     * @param Carbon $dueDate The payment due date
     * @param Carbon|null $paymentDate The actual payment date (defaults to now)
     * @param float|null $penaltyRatePerDay The daily penalty rate as percentage
     * @return array ['amount' => float, 'days_delayed' => int]
     */
    public function calculatePenalty(
        float $principalAmount,
        Carbon $dueDate,
        ?Carbon $paymentDate = null,
        ?float $penaltyRatePerDay = null
    ): array {
        $paymentDate = $paymentDate ?? now();
        $penaltyRate = $penaltyRatePerDay ?? config('loans.calamity.penalty_rate_per_day', 0.05);

        // No penalty if paid on or before due date
        if ($paymentDate->lte($dueDate)) {
            return ['amount' => 0, 'days_delayed' => 0];
        }

        $daysDelayed = $dueDate->diffInDays($paymentDate);
        
        // Penalty = principal × (rate/100) × days
        $penaltyAmount = round($principalAmount * ($penaltyRate / 100) * $daysDelayed, 2);

        return [
            'amount' => $penaltyAmount,
            'days_delayed' => $daysDelayed,
        ];
    }

    /**
     * Allocate a payment according to priority: Penalties → Interest → Principal.
     * 
     * @param Loan $loan The loan receiving payment
     * @param float $paymentAmount The payment amount
     * @param float $outstandingPenalties Any outstanding penalties
     * @param float $outstandingInterest Any outstanding interest
     * @return array Payment allocation breakdown
     */
    public function allocatePayment(
        Loan $loan,
        float $paymentAmount,
        float $outstandingPenalties = 0,
        float $outstandingInterest = 0
    ): array {
        $remaining = $paymentAmount;
        $allocation = [
            'penalty_amount' => 0,
            'interest_amount' => 0,
            'principal_amount' => 0,
            'remaining_penalties' => $outstandingPenalties,
            'remaining_interest' => $outstandingInterest,
            'remaining_principal' => (float) $loan->outstanding_balance,
            'is_fully_allocated' => false,
        ];

        // 1. First, pay off penalties
        if ($remaining > 0 && $outstandingPenalties > 0) {
            $penaltyPayment = min($remaining, $outstandingPenalties);
            $allocation['penalty_amount'] = $penaltyPayment;
            $allocation['remaining_penalties'] = round($outstandingPenalties - $penaltyPayment, 2);
            $remaining -= $penaltyPayment;
        }

        // 2. Next, pay off interest
        if ($remaining > 0 && $outstandingInterest > 0) {
            $interestPayment = min($remaining, $outstandingInterest);
            $allocation['interest_amount'] = $interestPayment;
            $allocation['remaining_interest'] = round($outstandingInterest - $interestPayment, 2);
            $remaining -= $interestPayment;
        }

        // 3. Finally, pay off principal
        if ($remaining > 0) {
            $principalPayment = min($remaining, (float) $loan->outstanding_balance);
            $allocation['principal_amount'] = $principalPayment;
            $allocation['remaining_principal'] = round((float) $loan->outstanding_balance - $principalPayment, 2);
            $remaining -= $principalPayment;
        }

        // Check if payment fully covered all dues
        $allocation['is_fully_allocated'] = (
            $allocation['remaining_penalties'] <= 0 &&
            $allocation['remaining_interest'] <= 0 &&
            $remaining >= 0
        );

        return $allocation;
    }

    /**
     * Get summary of calamity loan terms for display.
     * 
     * @param Loan $loan The calamity loan
     * @return array Summary information
     */
    public function getCalamityLoanSummary(Loan $loan): array
    {
        $gracePeriodEnd = $loan->getGracePeriodEndDate();
        $firstPaymentDate = $loan->getFirstPaymentDueDate();
        
        $calculationMethod = config('loans.calamity.interest_calculation_method', 'flat');
        
        if ($calculationMethod === 'flat') {
            $monthlyPayment = $this->calculateMonthlyPaymentFlatRate(
                (float) ($loan->approved_amount ?? $loan->amount),
                $loan->interest_rate,
                $loan->term_months
            );
        } else {
            $monthlyPayment = $this->calculateMonthlyPayment(
                (float) ($loan->approved_amount ?? $loan->amount),
                $loan->interest_rate / 100 / 12,
                $loan->term_months
            );
        }

        return [
            'eligible_amount' => $loan->eligible_amount,
            'loanable_percentage' => $loan->loanable_percentage ?? config('loans.calamity.loanable_percentage', 80),
            'loanable_amount' => $loan->amount,
            'interest_rate' => $loan->interest_rate,
            'term_months' => $loan->term_months,
            'term_years' => $loan->term_months / 12,
            'grace_period_months' => $loan->grace_period_months,
            'grace_period_end' => $gracePeriodEnd?->format('M d, Y'),
            'first_payment_due' => $firstPaymentDate?->format('M d, Y'),
            'penalty_rate' => $loan->penalty_rate,
            'penalty_rate_description' => '1/20 of 1% per day of delay',
            'monthly_payment' => $monthlyPayment,
            'is_in_grace_period' => $loan->isInGracePeriod(),
            'interest_calculation_method' => $calculationMethod,
        ];
    }
}
