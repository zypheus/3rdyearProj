<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

/**
 * User Controller - Admin Only
 * 
 * Manages all users in the system.
 * RBAC: Only Admin can access these methods.
 * 
 * Routes protected by `role:admin` middleware.
 */
class UserController extends Controller
{
    /**
     * Display a listing of all users.
     * Admin only - shows all users with role badges.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role if specified
        if ($request->has('role') && in_array($request->role, User::ROLES)) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('users.index', [
            'users' => $users,
            'roles' => User::ROLES,
            'currentRole' => $request->role,
            'search' => $request->search,
        ]);
    }

    /**
     * Show the form for creating a new user.
     * Admin can create users with any role.
     */
    public function create()
    {
        return view('users.create', [
            'roles' => User::ROLES,
        ]);
    }

    /**
     * Store a newly created user.
     * Admin can assign any role during creation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:' . implode(',', User::ROLES)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        // Log user creation
        AuditService::log(
            AuditService::ACTION_USER_CREATED,
            'User',
            $user->id,
            null,
            ['email' => $user->email, 'role' => $user->role]
        );

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} created successfully.");
    }

    /**
     * Display the specified user.
     * Shows user details including their activity.
     */
    public function show(User $user)
    {
        $user->load(['loans', 'documents', 'payments']);

        return view('users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => User::ROLES,
        ]);
    }

    /**
     * Update the specified user.
     * Admin can update any field including role.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Log user update
        AuditService::log(
            AuditService::ACTION_USER_UPDATED,
            'User',
            $user->id,
            null,
            ['email' => $user->email]
        );

        return redirect()->route('users.show', $user)
            ->with('success', "User {$user->name} updated successfully.");
    }

    /**
     * Update user's role.
     * Separate method for role changes to ensure proper audit logging.
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:' . implode(',', User::ROLES)],
        ]);

        $oldRole = $user->role;
        $newRole = $validated['role'];

        // Don't allow admin to demote themselves
        if ($user->id === auth()->id() && $newRole !== User::ROLE_ADMIN) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->role = $newRole;
        $user->save();

        // Log role change
        AuditService::logRoleChange($user->id, $oldRole, $newRole);

        return redirect()->route('users.show', $user)
            ->with('success', "Role updated from {$oldRole} to {$newRole}.");
    }

    /**
     * Remove the specified user.
     * Cannot delete self.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $userId = $user->id;

        $user->delete();

        // Log user deletion
        AuditService::log(
            AuditService::ACTION_USER_DELETED,
            'User',
            $userId,
            ['email' => $userEmail],
            null
        );

        return redirect()->route('users.index')
            ->with('success', "User {$userName} deleted successfully.");
    }
}
