<?php

namespace App\Models;

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

    public const TYPES = [
        self::TYPE_PERSONAL,
        self::TYPE_BUSINESS,
        self::TYPE_EMERGENCY,
        self::TYPE_EDUCATION,
    ];

    protected $fillable = [
        'user_id',
        'loan_type',
        'amount',
        'term_months',
        'interest_rate',
        'purpose',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'approved_amount',
        'outstanding_balance',
        'total_paid',
        'disbursement_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'approved_amount' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'reviewed_at' => 'datetime',
            'disbursement_date' => 'date',
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
     * Get payments for this loan.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
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
}
