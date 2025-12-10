<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Loan Model
 * 
 * Core entity for loan applications and tracking.
 * Status workflow: pending → under_review → approved/rejected → active → completed/defaulted
 * 
 * Loan Types:
 * - Personal, Business, Emergency, Education: Standard loans
 * - Calamity: Special loan based on Pag-IBIG guidelines with grace period and penalty system
 * 
 * Relationships:
 * - belongsTo User (applicant)
 * - belongsTo User (reviewer - officer/admin)
 * - hasMany Documents
 * - hasMany Payments
 */
class Loan extends Model
{
    use HasFactory;

    /**
     * Loan status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_DEFAULTED = 'defaulted';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_UNDER_REVIEW,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_ACTIVE,
        self::STATUS_COMPLETED,
        self::STATUS_DEFAULTED,
    ];

    /**
     * Loan type constants
     */
    public const TYPE_PERSONAL = 'personal';
    public const TYPE_BUSINESS = 'business';
    public const TYPE_EMERGENCY = 'emergency';
    public const TYPE_EDUCATION = 'education';
    public const TYPE_CALAMITY = 'calamity';

    public const TYPES = [
        self::TYPE_PERSONAL,
        self::TYPE_BUSINESS,
        self::TYPE_EMERGENCY,
        self::TYPE_EDUCATION,
        self::TYPE_CALAMITY,
    ];

    /**
     * Calamity loan term options (in months)
     * Based on Pag-IBIG guidelines: 2 or 3 years only
     */
    public const CALAMITY_TERM_OPTIONS = [24, 36];

    protected $fillable = [
        'user_id',
        'loan_type',
        'amount',
        'eligible_amount',
        'loanable_percentage',
        'term_months',
        'grace_period_months',
        'interest_rate',
        'penalty_rate',
        'purpose',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'approved_amount',
        'outstanding_balance',
        'total_paid',
        'total_penalties',
        'disbursement_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'eligible_amount' => 'decimal:2',
            'loanable_percentage' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'penalty_rate' => 'decimal:4',
            'approved_amount' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'total_penalties' => 'decimal:2',
            'reviewed_at' => 'datetime',
            'disbursement_date' => 'date',
            'grace_period_months' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user (member) who submitted this loan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user - the applicant.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the officer/admin who reviewed this loan.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get documents attached to this loan.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the payments for this loan.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get disbursements for this loan.
     */
    public function disbursements(): HasMany
    {
        return $this->hasMany(Disbursement::class);
    }

    /**
     * Get payment schedule entries for this loan.
     */
    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isUnderReview(): bool
    {
        return $this->status === self::STATUS_UNDER_REVIEW;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isDefaulted(): bool
    {
        return $this->status === self::STATUS_DEFAULTED;
    }

    /**
     * Check if loan can be approved/rejected.
     */
    public function canBeReviewed(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_UNDER_REVIEW]);
    }

    /**
     * Check if loan can receive payments.
     */
    public function canReceivePayments(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /*
    |--------------------------------------------------------------------------
    | Loan Type Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this is a calamity loan.
     */
    public function isCalamityLoan(): bool
    {
        return $this->loan_type === self::TYPE_CALAMITY;
    }

    /**
     * Get the grace period end date for this loan.
     * Returns null if no grace period or no disbursement date.
     */
    public function getGracePeriodEndDate(): ?Carbon
    {
        if (!$this->disbursement_date || $this->grace_period_months <= 0) {
            return null;
        }

        return Carbon::parse($this->disbursement_date)
            ->addMonths($this->grace_period_months);
    }

    /**
     * Get the first payment due date (after grace period).
     */
    public function getFirstPaymentDueDate(): ?Carbon
    {
        if (!$this->disbursement_date) {
            return null;
        }

        $gracePeriodEnd = $this->getGracePeriodEndDate();
        
        if ($gracePeriodEnd) {
            // First payment is due one month after grace period ends
            return $gracePeriodEnd->copy()->addMonth();
        }

        // No grace period - first payment is one month after disbursement
        return Carbon::parse($this->disbursement_date)->addMonth();
    }

    /**
     * Check if the loan is still in grace period.
     */
    public function isInGracePeriod(): bool
    {
        $gracePeriodEnd = $this->getGracePeriodEndDate();
        
        if (!$gracePeriodEnd) {
            return false;
        }

        return now()->lt($gracePeriodEnd);
    }

    /**
     * Calculate penalty for a late payment.
     * 
     * @param Carbon $dueDate The original due date
     * @param Carbon|null $paymentDate The actual payment date (defaults to now)
     * @param float|null $principalAmount The principal amount to calculate penalty on
     * @return float The penalty amount
     */
    public function calculatePenalty(Carbon $dueDate, ?Carbon $paymentDate = null, ?float $principalAmount = null): float
    {
        $paymentDate = $paymentDate ?? now();
        $principal = $principalAmount ?? ($this->approved_amount ?? $this->amount);

        // No penalty if paid on or before due date
        if ($paymentDate->lte($dueDate)) {
            return 0;
        }

        // Calculate days delayed
        $daysDelayed = $dueDate->diffInDays($paymentDate);

        // Penalty = principal × penalty_rate × days_delayed
        // penalty_rate is stored as percentage (e.g., 0.05 = 0.05%)
        $penaltyRate = $this->penalty_rate / 100;
        
        return round($principal * $penaltyRate * $daysDelayed, 2);
    }

    /**
     * Get the effective loanable amount for calamity loans.
     * Returns the requested amount for non-calamity loans.
     */
    public function getLoanableAmount(): float
    {
        if (!$this->isCalamityLoan() || !$this->eligible_amount) {
            return (float) $this->amount;
        }

        $percentage = $this->loanable_percentage ?? config('loans.calamity.loanable_percentage', 80);
        return round($this->eligible_amount * ($percentage / 100), 2);
    }

    /**
     * Get human-readable loan type label.
     */
    public function getLoanTypeLabel(): string
    {
        return match($this->loan_type) {
            self::TYPE_PERSONAL => 'Personal Loan',
            self::TYPE_BUSINESS => 'Business Loan',
            self::TYPE_EMERGENCY => 'Emergency Loan',
            self::TYPE_EDUCATION => 'Education Loan',
            self::TYPE_CALAMITY => 'Calamity Loan',
            default => ucfirst($this->loan_type) . ' Loan',
        };
    }

    /**
     * Get remaining balance including any unpaid penalties.
     */
    public function getTotalOutstanding(): float
    {
        $outstanding = (float) ($this->outstanding_balance ?? 0);
        $penalties = (float) ($this->total_penalties ?? 0);
        
        return round($outstanding + $penalties, 2);
    }
}
