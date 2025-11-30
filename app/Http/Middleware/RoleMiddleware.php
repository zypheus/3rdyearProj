<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

/**
 * Role-Based Access Control Middleware
 * 
 * CRITICAL: This system uses ONLY 3 roles - admin, officer, member
 * Never add additional roles beyond these three.
 * 
 * Usage in routes:
 * - Single role:    Route::middleware('role:admin')
 * - Multiple roles: Route::middleware('role:admin,officer')
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Allowed roles (admin, officer, member)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = auth()->user();

        // Validate that only allowed roles are being checked
        $validRoles = User::ROLES; // ['admin', 'officer', 'member']
        foreach ($roles as $role) {
            if (!in_array($role, $validRoles)) {
                abort(500, "Invalid role '{$role}' specified. Only admin, officer, member are allowed.");
            }
        }

        // Check if user has one of the required roles
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized. You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
