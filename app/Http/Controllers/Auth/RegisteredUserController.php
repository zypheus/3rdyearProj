<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * 
     * CRITICAL: New users are ALWAYS assigned 'member' role.
     * Only Admin can change roles after registration.
     * Only 3 roles exist: admin, officer, member
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // CRITICAL: Always assign 'member' role on registration
        // Never allow role selection during registration
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_MEMBER, // Always member - enforced here
        ]);

        event(new Registered($user));

        // Log user registration for audit trail
        AuditService::log(
            AuditService::ACTION_USER_CREATED,
            'User',
            $user->id,
            null,
            ['email' => $user->email, 'role' => $user->role]
        );

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
