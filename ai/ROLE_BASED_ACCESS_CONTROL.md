# LoanEase - Role-Based Access Control Reference

## Overview

LoanEase uses exactly **THREE user roles**. No other roles should be added to the system.

## Role Definitions

### 1. ADMIN (Full System Access)

**Primary Responsibility:** System management and oversight

**Permissions:**
- ✅ Create, read, update, delete users
- ✅ Assign and change user roles
- ✅ View all loan applications (regardless of status)
- ✅ View all documents and submissions
- ✅ Generate system-wide reports
- ✅ Configure system settings
- ✅ Access audit logs
- ✅ Manage payments and reconciliation
- ✅ Create and modify loan types/policies

**Cannot Do:**
- ❌ Be restricted from any system feature
- ❌ Have their role changed by Officer or Member

**Database Role Value:** `admin`

### 2. OFFICER (Loan Processing & Verification)

**Primary Responsibility:** Process loan applications and verify documents

**Permissions:**
- ✅ View assigned loan applications
- ✅ Review and verify documents submitted by members
- ✅ Approve loan applications (change status to Approved)
- ✅ Reject loan applications (change status to Rejected)
- ✅ Add comments and notes to applications
- ✅ View payment history for assigned loans
- ✅ Generate reports for their processed loans
- ✅ Update application status and information
- ✅ View member profiles (limited to submitted data)

**Cannot Do:**
- ❌ Delete users or applications
- ❌ Change their own role
- ❌ Access system settings
- ❌ View loans outside their jurisdiction/assignment
- ❌ Access other officer's applications (if jurisdiction-based)

**Database Role Value:** `officer`

### 3. MEMBER (Basic User)

**Primary Responsibility:** Submit loan applications and track status

**Permissions:**
- ✅ Submit loan applications
- ✅ Upload required documents
- ✅ View own application status
- ✅ View own submitted documents
- ✅ Update own profile information
- ✅ View payment history for own loans
- ✅ Receive notifications about application status

**Cannot Do:**
- ❌ View other members' applications
- ❌ Approve or reject applications
- ❌ Modify application status
- ❌ Access system settings
- ❌ View audit logs
- ❌ Generate reports
- ❌ Delete their own applications (after submission)

**Database Role Value:** `member`

## Role-Based Access Control Implementation

### Database Schema

```sql
-- Users table with role column
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'officer', 'member') DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Laravel User Model

```php
// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role'];
    
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }
    
    public function isOfficer(): bool {
        return $this->role === 'officer';
    }
    
    public function isMember(): bool {
        return $this->role === 'member';
    }
}
```

### Route Protection (Middleware)

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    // Member routes
    Route::post('/loans', [LoanController::class, 'store'])->middleware('role:member');
    Route::get('/loans', [LoanController::class, 'index'])->middleware('role:member,officer,admin');
    
    // Officer routes
    Route::put('/loans/{id}/approve', [LoanController::class, 'approve'])->middleware('role:officer,admin');
    Route::put('/loans/{id}/reject', [LoanController::class, 'reject'])->middleware('role:officer,admin');
    
    // Admin routes
    Route::get('/users', [UserController::class, 'index'])->middleware('role:admin');
    Route::post('/users/{id}/role', [UserController::class, 'updateRole'])->middleware('role:admin');
});
```

### Authorization Service

```php
// app/Services/AuthService.php
namespace App\Services;

use App\Models\User;

class AuthService
{
    public static function requireAdmin(User $user): void {
        if ($user->role !== 'admin') {
            abort(403, 'Admin access required');
        }
    }
    
    public static function requireOfficerOrAdmin(User $user): void {
        if ($user->role !== 'admin' && $user->role !== 'officer') {
            abort(403, 'Officer or Admin access required');
        }
    }
    
    public static function requireAuthenticated(User $user): void {
        // User is already authenticated if passed here
    }
}
```

### Controller Example

```php
// app/Http/Controllers/LoanController.php
namespace App\Http\Controllers;

use App\Models\Loan;
use App\Services\AuthService;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function store(Request $request)
    {
        // Members can submit loans
        $loan = Loan::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            // ... other fields
        ]);
        
        return redirect()->back()->with('success', 'Loan application submitted');
    }
    
    public function approve(Request $request, $id)
    {
        // Only Officer and Admin can approve
        AuthService::requireOfficerOrAdmin(auth()->user());
        
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'approved']);
        
        return redirect()->back()->with('success', 'Loan approved');
    }
    
    public function index(Request $request)
    {
        // Each role sees different loans
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $loans = Loan::all();
        } elseif ($user->isOfficer()) {
            // Officer sees assigned loans
            $loans = $user->assignedLoans;
        } else { // Member
            $loans = $user->loans;
        }
        
        return view('loans.index', ['loans' => $loans]);
    }
}
```

## Important Rules

### ✅ DO:
- ✅ Use exactly these three roles: admin, officer, member
- ✅ Implement role checks in middleware and controllers
- ✅ Log all sensitive actions (approvals, rejections, role changes)
- ✅ Restrict data access based on roles
- ✅ Test with all three roles during development
- ✅ Document role requirements for each feature
- ✅ Use authorization services for consistent checks

### ❌ DON'T:
- ❌ Add roles like "LGU Verifier", "Employer", "Manager", etc.
- ❌ Use roles that aren't one of the three defined roles
- ❌ Trust role information from client-side only (always verify in server)
- ❌ Hardcode permissions in controllers (use middleware or services)
- ❌ Allow users to change their own roles
- ❌ Skip authorization checks "just for now"
- ❌ Mix role checking with other business logic

## Features by Role

### Loan Application Features
- **Member**: Can submit, view own status ✅
- **Officer**: Can review, approve/reject, add comments ✅
- **Admin**: Can do everything, view all ✅

### Document Management
- **Member**: Can upload, view own ✅
- **Officer**: Can view, verify ✅
- **Admin**: Can view all, delete if needed ✅

### User Management
- **Member**: Can update own profile ✅
- **Officer**: Cannot access ❌
- **Admin**: Can manage all users, assign roles ✅

### Reporting
- **Member**: Cannot access ❌
- **Officer**: Can view reports for own loans ✅
- **Admin**: Can view all reports ✅

### System Configuration
- **Member**: Cannot access ❌
- **Officer**: Cannot access ❌
- **Admin**: Can configure ✅

## Testing Checklist

For each feature, test with all three roles:

- [ ] Admin user can access (if feature requires admin)
- [ ] Officer user can access (if feature requires officer)
- [ ] Member user can access (if feature requires member)
- [ ] Unauthorized users are blocked with 403 Forbidden
- [ ] Data is properly filtered by role
- [ ] Sensitive actions are logged to AuditLog
- [ ] Role transitions work correctly
- [ ] No user can elevate their own role

## Migration Guide

If migrating from a system with different roles:

1. Map old roles to the three new roles
2. Create migration to update role column
3. Audit all permissions in code
4. Update route middleware to use new roles
5. Test thoroughly with all three roles
6. Document any role consolidations made

Example:

```
Old System          →  LoanEase
LGU Verifier        →  Officer
Employer            →  Officer (or create custom integration)
Loan Officer        →  Officer
Member              →  Member
Admin               →  Admin
```

## Support & Questions

For role-related questions:
1. Check this file first
2. Reference `ai/example-prompts-loanease.md` for implementation examples
3. See `.cursor/rules/4-authentication.mdc` for detailed guidelines
4. Review existing code in `app/Http/Controllers/` and `routes/web.php`
