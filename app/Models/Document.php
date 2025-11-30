<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Document Model
 * 
 * Stores uploaded documents for loan applications.
 * Documents can be verified by officers/admins.
 * 
 * Relationships:
 * - belongsTo Loan
 * - belongsTo User (uploader)
 * - belongsTo User (verifier - officer/admin)
 */
class Document extends Model
{
    use HasFactory;

    /**
     * Document type constants
     */
    public const TYPE_ID = 'id';
    public const TYPE_INCOME_PROOF = 'income_proof';
    public const TYPE_BANK_STATEMENT = 'bank_statement';
    public const TYPE_EMPLOYMENT = 'employment';
    public const TYPE_COLLATERAL = 'collateral';
    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_ID,
        self::TYPE_INCOME_PROOF,
        self::TYPE_BANK_STATEMENT,
        self::TYPE_EMPLOYMENT,
        self::TYPE_COLLATERAL,
        self::TYPE_OTHER,
    ];

    protected $fillable = [
        'loan_id',
        'user_id',
        'document_type',
        'filename',
        'file_path',
        'file_size',
        'mime_type',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the loan this document belongs to.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the user who uploaded this document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user - the uploader.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the officer/admin who verified this document.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if document is verified.
     */
    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
