<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * User Model
 * 
 * RBAC: Uses exactly 3 roles - admin, officer, member
 * CRITICAL: Never add additional roles beyond these three
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Valid roles for the system.
     * CRITICAL: Only these 3 roles are allowed - never add more
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_OFFICER = 'officer';
    public const ROLE_MEMBER = 'member';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_OFFICER,
        self::ROLE_MEMBER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helper Methods
    |--------------------------------------------------------------------------
    | CRITICAL: Only 3 roles exist - Admin, Officer, Member
    | These methods provide convenient role checking throughout the application
    */

    /**
     * Check if user is an Admin.
     * Admin has full system access.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is an Officer.
     * Officer can process loans, verify documents, approve/reject.
     */
    public function isOfficer(): bool
    {
        return $this->role === self::ROLE_OFFICER;
    }

    /**
     * Check if user is a Member.
     * Member can submit applications, upload documents, view own status.
     */
    public function isMember(): bool
    {
        return $this->role === self::ROLE_MEMBER;
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the specified roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is Admin or Officer.
     * Used for actions that both Admin and Officer can perform.
     */
    public function isAdminOrOfficer(): bool
    {
        return $this->isAdmin() || $this->isOfficer();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get loans submitted by this user.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get loans reviewed by this user (as Officer/Admin).
     */
    public function reviewedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'reviewed_by');
    }

    /**
     * Get documents uploaded by this user.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get documents verified by this user (as Officer/Admin).
     */
    public function verifiedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'verified_by');
    }

    /**
     * Get payments made by this user (as Member).
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get payments recorded by this user (as Officer/Admin).
     */
    public function recordedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'recorded_by');
    }

    /**
     * Get audit logs for this user's actions.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
