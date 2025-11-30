<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Authorization Service
 * 
 * Centralized authorization logic for role-based access control.
 * CRITICAL: This system uses ONLY 3 roles - admin, officer, member
 * 
 * Usage:
 *   AuthService::requireAdmin();           // Aborts if not admin
 *   AuthService::requireOfficerOrAdmin();  // Aborts if not officer or admin
 *   AuthService::canAccessLoan($loan);     // Returns bool
 */
class AuthService
{
    /**
     * Require the current user to be authenticated.
     * Aborts with 401 if not authenticated.
     */
    public static function requireAuthenticated(): void
    {
        if (!Auth::check()) {
            abort(401, 'Authentication required');
        }
    }

    /**
     * Require the current user to be an Admin.
     * Aborts with 403 if not admin.
     */
    public static function requireAdmin(): void
    {
        self::requireAuthenticated();
        
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Admin access required');
        }
    }

    /**
     * Require the current user to be an Officer.
     * Aborts with 403 if not officer.
     */
    public static function requireOfficer(): void
    {
        self::requireAuthenticated();
        
        if (!Auth::user()->isOfficer()) {
            abort(403, 'Officer access required');
        }
    }

    /**
     * Require the current user to be an Officer or Admin.
     * Aborts with 403 if neither.
     */
    public static function requireOfficerOrAdmin(): void
    {
        self::requireAuthenticated();
        
        if (!Auth::user()->isAdminOrOfficer()) {
            abort(403, 'Officer or Admin access required');
        }
    }

    /**
     * Alias for requireOfficerOrAdmin for consistency.
     */
    public static function requireAdminOrOfficer(): void
    {
        self::requireOfficerOrAdmin();
    }

    /**
     * Require the current user to be a Member.
     * Aborts with 403 if not member.
     */
    public static function requireMember(): void
    {
        self::requireAuthenticated();
        
        if (!Auth::user()->isMember()) {
            abort(403, 'Member access required');
        }
    }

    /**
     * Check if the current user can access a specific resource.
     * 
     * @param mixed $resource Resource with user_id property
     * @return bool
     */
    public static function canAccessResource($resource): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Admin can access everything
        if ($user->isAdmin()) {
            return true;
        }

        // Officer can access all resources (for processing)
        if ($user->isOfficer()) {
            return true;
        }

        // Member can only access their own resources
        if (isset($resource->user_id)) {
            return $resource->user_id === $user->id;
        }

        return false;
    }

    /**
     * Check if current user owns the resource.
     * 
     * @param mixed $resource Resource with user_id property
     * @return bool
     */
    public static function ownsResource($resource): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return isset($resource->user_id) && $resource->user_id === Auth::id();
    }

    /**
     * Get the current authenticated user.
     * 
     * @return User|null
     */
    public static function user(): ?User
    {
        return Auth::user();
    }

    /**
     * Get current user's role.
     * 
     * @return string|null
     */
    public static function role(): ?string
    {
        return Auth::user()?->role;
    }

    /**
     * Check if current user has a specific role.
     * 
     * @param string $role Role to check (admin, officer, member)
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        return Auth::user()?->role === $role;
    }

    /**
     * Check if current user has any of the specified roles.
     * 
     * @param array $roles Roles to check
     * @return bool
     */
    public static function hasAnyRole(array $roles): bool
    {
        return in_array(Auth::user()?->role, $roles);
    }
}
