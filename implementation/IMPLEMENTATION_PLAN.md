# LoanEase - Implementation Plan

**Version:** 1.0  
**Date:** November 30, 2025  
**Total Estimated Duration:** 8-10 Weeks

---

## Overview

This implementation plan follows a **Milestone-Based Approach** with **Vertical Slice Methodology**, suitable for LLM-assisted coding. Each phase delivers a complete, testable feature from database to UI.

**CRITICAL CONSTRAINT:** This system uses **exactly three user roles**: Admin, Officer, and Member. No other roles should be added.

---

## Phase Summary

| Phase | Duration | Focus Area | Key Deliverables |
|-------|----------|------------|------------------|
| 1 | Week 1-2 | Core Setup & Authentication | Laravel setup, auth, RBAC |
| 2 | Week 2-3 | User Management | User CRUD, role assignment |
| 3 | Week 3-4 | Loan Application | Application form, submission |
| 4 | Week 4-5 | Document Management | File upload, verification |
| 5 | Week 5-6 | Loan Processing | Review, approve/reject workflow |
| 6 | Week 6-7 | Payment Tracking | Payment schedule, recording |
| 7 | Week 7-8 | Reporting & Audit | Reports, audit logs |
| 8 | Week 8-10 | Testing & Refinement | Security review, bug fixes |

---

## Phase 1: Core Setup & Authentication

**Goal:** Establish project foundation with authentication and role-based access control.

**Duration:** Week 1-2

### Tasks

#### 1.1 Project Setup
- [ ] **1.1.1** Verify Laravel 12 installation
- [ ] **1.1.2** Configure MySQL database connection in `.env`
- [ ] **1.1.3** Install and configure Vite for frontend assets
- [ ] **1.1.4** Install Tailwind CSS
- [ ] **1.1.5** Set up base layout with Blade templates

**Commands:**
```bash
# Verify installation
php artisan --version

# Install frontend dependencies
yarn install

# Start development servers
php artisan serve
yarn dev
```

#### 1.2 Database Foundation
- [ ] **1.2.1** Create users table migration with role column
- [ ] **1.2.2** Add role enum: `admin`, `officer`, `member`
- [ ] **1.2.3** Run migrations
- [ ] **1.2.4** Create database seeder for test users

**Migration Example:**
```php
// database/migrations/xxxx_add_role_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->enum('role', ['admin', 'officer', 'member'])->default('member');
});
```

#### 1.3 Authentication Setup
- [ ] **1.3.1** Install Laravel Breeze
- [ ] **1.3.2** Customize registration to exclude role selection
- [ ] **1.3.3** Create login/logout functionality
- [ ] **1.3.4** Add "Remember Me" feature
- [ ] **1.3.5** Implement password reset

**Commands:**
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
```

#### 1.4 Role-Based Middleware
- [ ] **1.4.1** Create `RoleMiddleware` class
- [ ] **1.4.2** Register middleware in `app/Http/Kernel.php`
- [ ] **1.4.3** Add role helper methods to User model
- [ ] **1.4.4** Test middleware with test routes

**Middleware Example:**
```php
// app/Http/Middleware/RoleMiddleware.php
public function handle($request, Closure $next, ...$roles)
{
    if (!in_array(auth()->user()->role, $roles)) {
        abort(403, 'Unauthorized access');
    }
    return $next($request);
}
```

#### 1.5 Authorization Service
- [ ] **1.5.1** Create `AuthService` class
- [ ] **1.5.2** Implement `requireAdmin()` method
- [ ] **1.5.3** Implement `requireOfficerOrAdmin()` method
- [ ] **1.5.4** Implement `requireAuthenticated()` method

### Deliverables
- ✅ Working Laravel 12 application
- ✅ User registration and login
- ✅ Role-based middleware protection
- ✅ Three test users (one per role)

### Testing Checklist
- [ ] User can register as member
- [ ] User can log in and log out
- [ ] Password reset works
- [ ] Role middleware blocks unauthorized access
- [ ] Admin can access admin routes
- [ ] Member cannot access admin routes

---

## Phase 2: User Management

**Goal:** Enable Admin to manage users and assign roles.

**Duration:** Week 2-3

### Tasks

#### 2.1 User Model Enhancements
- [ ] **2.1.1** Add fillable fields to User model
- [ ] **2.1.2** Add role accessor/mutator methods
- [ ] **2.1.3** Add relationship methods for future entities
- [ ] **2.1.4** Create User factory for testing

#### 2.2 User Controller
- [ ] **2.2.1** Create `UserController` with CRUD methods
- [ ] **2.2.2** Implement `index()` - list all users
- [ ] **2.2.3** Implement `show()` - view user details
- [ ] **2.2.4** Implement `edit()` / `update()` - update user info
- [ ] **2.2.5** Implement `destroy()` - delete user
- [ ] **2.2.6** Implement `updateRole()` - change user role

#### 2.3 User Views
- [ ] **2.3.1** Create user list view with pagination
- [ ] **2.3.2** Create user detail view
- [ ] **2.3.3** Create user edit form
- [ ] **2.3.4** Add role dropdown (Admin only)
- [ ] **2.3.5** Add confirmation modal for delete

#### 2.4 User Routes
- [ ] **2.4.1** Define resource routes for users
- [ ] **2.4.2** Apply `role:admin` middleware
- [ ] **2.4.3** Add route for role update

**Routes Example:**
```php
// routes/web.php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
});
```

### Deliverables
- ✅ User list page with search/filter
- ✅ User detail page
- ✅ Role assignment functionality
- ✅ User deletion with confirmation

### Testing Checklist
- [ ] Admin can view all users
- [ ] Admin can change user roles
- [ ] Admin can delete users
- [ ] Officer cannot access user management
- [ ] Member cannot access user management
- [ ] Role changes are reflected immediately

---

## Phase 3: Loan Application

**Goal:** Enable Members to submit loan applications.

**Duration:** Week 3-4

### Tasks

#### 3.1 Loan Entity
- [ ] **3.1.1** Create Loan model
- [ ] **3.1.2** Create loans migration
- [ ] **3.1.3** Define relationships (User, Documents, Payments)
- [ ] **3.1.4** Add status enum values
- [ ] **3.1.5** Create Loan factory

**Migration Fields:**
```php
Schema::create('loans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('loan_type');
    $table->decimal('amount', 15, 2);
    $table->integer('term_months');
    $table->decimal('interest_rate', 5, 2);
    $table->text('purpose')->nullable();
    $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'active', 'completed', 'defaulted'])->default('pending');
    $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('reviewed_at')->nullable();
    $table->text('rejection_reason')->nullable();
    $table->decimal('approved_amount', 15, 2)->nullable();
    $table->date('disbursement_date')->nullable();
    $table->timestamps();
});
```

#### 3.2 Loan Controller
- [ ] **3.2.1** Create `LoanController`
- [ ] **3.2.2** Implement `index()` with role-based filtering
- [ ] **3.2.3** Implement `create()` - show application form
- [ ] **3.2.4** Implement `store()` - save application
- [ ] **3.2.5** Implement `show()` - view loan details
- [ ] **3.2.6** Implement authorization checks

#### 3.3 Application Form (Multi-Step)
- [ ] **3.3.1** Create Step 1: Personal Information view
- [ ] **3.3.2** Create Step 2: Loan Details view
- [ ] **3.3.3** Create Step 3: Document Upload view
- [ ] **3.3.4** Create Step 4: Review & Submit view
- [ ] **3.3.5** Implement form validation for each step
- [ ] **3.3.6** Add progress indicator

#### 3.4 Form Requests
- [ ] **3.4.1** Create `StoreLoanRequest` for validation
- [ ] **3.4.2** Define validation rules
- [ ] **3.4.3** Add custom error messages

**Validation Example:**
```php
// app/Http/Requests/StoreLoanRequest.php
public function rules()
{
    return [
        'loan_type' => 'required|string|max:100',
        'amount' => 'required|numeric|min:1000|max:1000000',
        'term_months' => 'required|integer|min:1|max:60',
        'purpose' => 'nullable|string|max:500',
    ];
}
```

#### 3.5 Loan Views
- [ ] **3.5.1** Create loan list view
- [ ] **3.5.2** Create loan detail view
- [ ] **3.5.3** Add status badges with colors
- [ ] **3.5.4** Create member-specific loan history view

### Deliverables
- ✅ Multi-step loan application form
- ✅ Loan list with role-based filtering
- ✅ Loan detail view
- ✅ Form validation with error display

### Testing Checklist
- [ ] Member can create new loan application
- [ ] Form validation works correctly
- [ ] Member can only see own loans
- [ ] Officer can see all loans
- [ ] Admin can see all loans
- [ ] Loan status displays correctly

---

## Phase 4: Document Management

**Goal:** Enable document upload and verification workflow.

**Duration:** Week 4-5

### Tasks

#### 4.1 Document Entity
- [ ] **4.1.1** Create Document model
- [ ] **4.1.2** Create documents migration
- [ ] **4.1.3** Define relationships (Loan, User)
- [ ] **4.1.4** Configure file storage

**Migration Fields:**
```php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('loan_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('document_type');
    $table->string('filename');
    $table->string('file_path');
    $table->integer('file_size');
    $table->string('mime_type');
    $table->boolean('is_verified')->default(false);
    $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('verified_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

#### 4.2 File Storage Configuration
- [ ] **4.2.1** Configure local storage disk
- [ ] **4.2.2** Set file size limits in php.ini
- [ ] **4.2.3** Create upload directory structure
- [ ] **4.2.4** Add `.gitignore` for uploads

#### 4.3 Document Controller
- [ ] **4.3.1** Create `DocumentController`
- [ ] **4.3.2** Implement `store()` - handle file upload
- [ ] **4.3.3** Implement `show()` - download/view file
- [ ] **4.3.4** Implement `destroy()` - delete file
- [ ] **4.3.5** Implement `verify()` - officer verification
- [ ] **4.3.6** Implement `reject()` - reject with notes

#### 4.4 Upload UI
- [ ] **4.4.1** Create file upload component
- [ ] **4.4.2** Add drag-and-drop support
- [ ] **4.4.3** Show upload progress
- [ ] **4.4.4** Display uploaded files list
- [ ] **4.4.5** Add file preview for images/PDFs

#### 4.5 Verification UI (Officer)
- [ ] **4.5.1** Create verification queue view
- [ ] **4.5.2** Add verify/reject buttons
- [ ] **4.5.3** Add notes input for rejection
- [ ] **4.5.4** Show verification status badges

### Deliverables
- ✅ File upload functionality
- ✅ Document verification workflow
- ✅ File preview and download
- ✅ Verification status tracking

### Testing Checklist
- [ ] Member can upload documents
- [ ] File size limits enforced
- [ ] Only allowed file types accepted
- [ ] Officer can verify documents
- [ ] Officer can reject with notes
- [ ] Member sees verification status
- [ ] Files are secure (only owner/officer/admin can access)

---

## Phase 5: Loan Processing

**Goal:** Enable Officers to review and process loan applications.

**Duration:** Week 5-6

### Tasks

#### 5.1 Processing Workflow
- [ ] **5.1.1** Define status transition rules
- [ ] **5.1.2** Create `LoanService` for business logic
- [ ] **5.1.3** Implement status change methods
- [ ] **5.1.4** Add validation for status transitions

**Status Transitions:**
```
pending → under_review (Officer picks up)
under_review → approved (Officer approves)
under_review → rejected (Officer rejects)
approved → active (Loan disbursed)
active → completed (All payments made)
active → defaulted (Multiple missed payments)
```

#### 5.2 Processing Controller Methods
- [ ] **5.2.1** Implement `review()` - start review
- [ ] **5.2.2** Implement `approve()` - approve loan
- [ ] **5.2.3** Implement `reject()` - reject with reason
- [ ] **5.2.4** Implement `disburse()` - mark as disbursed
- [ ] **5.2.5** Add authorization for each action

#### 5.3 Processing Views
- [ ] **5.3.1** Create processing queue view
- [ ] **5.3.2** Create application review page
- [ ] **5.3.3** Add approve/reject forms
- [ ] **5.3.4** Show document checklist
- [ ] **5.3.5** Add notes/comments section

#### 5.4 Notification System (Basic)
- [ ] **5.4.1** Create notification for status change
- [ ] **5.4.2** Display notifications on dashboard
- [ ] **5.4.3** Mark notifications as read

### Deliverables
- ✅ Loan processing queue
- ✅ Approve/reject workflow
- ✅ Status tracking with history
- ✅ Basic notifications

### Testing Checklist
- [ ] Officer can pick up pending applications
- [ ] Officer can approve loans
- [ ] Officer can reject with reason
- [ ] Member is notified of status change
- [ ] Admin can see all processing activity
- [ ] Invalid status transitions are blocked

---

## Phase 6: Payment Tracking

**Goal:** Track loan payments and generate payment schedules.

**Duration:** Week 6-7

### Tasks

#### 6.1 Payment Entity
- [ ] **6.1.1** Create Payment model
- [ ] **6.1.2** Create payments migration
- [ ] **6.1.3** Define relationships (Loan, User)
- [ ] **6.1.4** Add payment status enum

**Migration Fields:**
```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('loan_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 15, 2);
    $table->decimal('principal', 15, 2);
    $table->decimal('interest', 15, 2);
    $table->date('payment_date');
    $table->date('due_date');
    $table->enum('status', ['pending', 'paid', 'overdue', 'partial'])->default('pending');
    $table->string('payment_method')->nullable();
    $table->string('reference_number')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

#### 6.2 Payment Schedule Generator
- [ ] **6.2.1** Create `PaymentService`
- [ ] **6.2.2** Implement amortization calculation
- [ ] **6.2.3** Generate payment schedule on approval
- [ ] **6.2.4** Calculate remaining balance

**Amortization Formula:**
```php
// Monthly Payment = P * [r(1+r)^n] / [(1+r)^n - 1]
// P = Principal, r = monthly interest rate, n = number of payments
```

#### 6.3 Payment Controller
- [ ] **6.3.1** Create `PaymentController`
- [ ] **6.3.2** Implement `index()` - list payments
- [ ] **6.3.3** Implement `show()` - payment details
- [ ] **6.3.4** Implement `record()` - record payment
- [ ] **6.3.5** Implement `markOverdue()` - scheduled job

#### 6.4 Payment Views
- [ ] **6.4.1** Create payment schedule view
- [ ] **6.4.2** Create payment recording form
- [ ] **6.4.3** Show payment history
- [ ] **6.4.4** Display overdue warnings
- [ ] **6.4.5** Add payment summary dashboard widget

### Deliverables
- ✅ Automatic payment schedule generation
- ✅ Payment recording functionality
- ✅ Payment history view
- ✅ Overdue payment tracking

### Testing Checklist
- [ ] Payment schedule generates on loan approval
- [ ] Officer can record payments
- [ ] Partial payments work correctly
- [ ] Overdue status updates correctly
- [ ] Member can view own payment schedule
- [ ] Balance calculation is accurate

---

## Phase 7: Reporting & Audit

**Goal:** Generate reports and maintain audit trail.

**Duration:** Week 7-8

### Tasks

#### 7.1 Audit Log Entity
- [ ] **7.1.1** Create AuditLog model
- [ ] **7.1.2** Create audit_logs migration
- [ ] **7.1.3** Create `AuditService` helper

**Migration Fields:**
```php
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->string('action');
    $table->string('entity_type');
    $table->unsignedBigInteger('entity_id')->nullable();
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamp('created_at');
});
```

#### 7.2 Audit Logging Implementation
- [ ] **7.2.1** Create `logAction()` helper method
- [ ] **7.2.2** Add logging to loan status changes
- [ ] **7.2.3** Add logging to role changes
- [ ] **7.2.4** Add logging to document verification
- [ ] **7.2.5** Add logging to payment recording

**Logging Example:**
```php
// app/Services/AuditService.php
public static function log($action, $entityType, $entityId, $oldValues = null, $newValues = null)
{
    AuditLog::create([
        'user_id' => auth()->id(),
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $entityId,
        'old_values' => $oldValues,
        'new_values' => $newValues,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
}
```

#### 7.3 Reports Module
- [ ] **7.3.1** Create `ReportController`
- [ ] **7.3.2** Implement loan summary report
- [ ] **7.3.3** Implement payment collection report
- [ ] **7.3.4** Implement delinquency report
- [ ] **7.3.5** Implement user activity report
- [ ] **7.3.6** Add date range filtering

#### 7.4 Report Views
- [ ] **7.4.1** Create reports dashboard
- [ ] **7.4.2** Create individual report views
- [ ] **7.4.3** Add charts/graphs with Chart.js
- [ ] **7.4.4** Implement PDF export
- [ ] **7.4.5** Implement CSV export

#### 7.5 Audit Log Viewer
- [ ] **7.5.1** Create audit log list view
- [ ] **7.5.2** Add filtering (by user, action, date)
- [ ] **7.5.3** Add detail view for log entry
- [ ] **7.5.4** Restrict to Admin only

### Deliverables
- ✅ Comprehensive audit logging
- ✅ Multiple report types
- ✅ Report export (PDF, CSV)
- ✅ Audit log viewer for Admin

### Testing Checklist
- [ ] All sensitive actions are logged
- [ ] Admin can view audit logs
- [ ] Officer cannot view audit logs
- [ ] Reports show correct data
- [ ] Date range filtering works
- [ ] Export generates valid files

---

## Phase 8: Testing & Refinement

**Goal:** Comprehensive testing, security review, and bug fixes.

**Duration:** Week 8-10

### Tasks

#### 8.1 Security Review
- [ ] **8.1.1** Review all authorization checks
- [ ] **8.1.2** Test role-based access for all routes
- [ ] **8.1.3** Verify data scoping (users see only their data)
- [ ] **8.1.4** Test for SQL injection vulnerabilities
- [ ] **8.1.5** Test for XSS vulnerabilities
- [ ] **8.1.6** Review file upload security
- [ ] **8.1.7** Run security audit tools

#### 8.2 Role Testing Matrix

| Feature | Admin | Officer | Member | Public |
|---------|-------|---------|--------|--------|
| User Management | ✅ | ❌ | ❌ | ❌ |
| View All Loans | ✅ | ✅ | ❌ | ❌ |
| Submit Application | ✅ | ❌ | ✅ | ❌ |
| Approve/Reject | ✅ | ✅ | ❌ | ❌ |
| View Own Data | ✅ | ✅ | ✅ | ❌ |
| Reports | ✅ | ✅* | ❌ | ❌ |
| Audit Logs | ✅ | ❌ | ❌ | ❌ |
| Settings | ✅ | ❌ | ❌ | ❌ |

*Officer has limited report access

#### 8.3 Functional Testing
- [ ] **8.3.1** Test complete loan lifecycle
- [ ] **8.3.2** Test document upload workflow
- [ ] **8.3.3** Test payment recording
- [ ] **8.3.4** Test all form validations
- [ ] **8.3.5** Test error handling
- [ ] **8.3.6** Test edge cases

#### 8.4 Performance Testing
- [ ] **8.4.1** Test with large datasets
- [ ] **8.4.2** Optimize slow queries
- [ ] **8.4.3** Add database indexes
- [ ] **8.4.4** Test concurrent users

#### 8.5 UI/UX Review
- [ ] **8.5.1** Test responsive design
- [ ] **8.5.2** Test on multiple browsers
- [ ] **8.5.3** Verify accessibility (WCAG)
- [ ] **8.5.4** Fix UI inconsistencies

#### 8.6 Documentation
- [ ] **8.6.1** Update README with setup instructions
- [ ] **8.6.2** Document API endpoints
- [ ] **8.6.3** Create user manual
- [ ] **8.6.4** Document deployment process

### Deliverables
- ✅ Security review report
- ✅ Bug-free application
- ✅ Performance optimizations
- ✅ Complete documentation

### Final Checklist
- [ ] All features working as specified
- [ ] Security vulnerabilities addressed
- [ ] Performance meets requirements
- [ ] Documentation complete
- [ ] Ready for production deployment

---

## Development Commands Reference

### Laravel Commands
```bash
# Start development server
php artisan serve

# Create migration
php artisan make:migration create_loans_table

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Create model with migration and controller
php artisan make:model Loan -mc

# Create controller
php artisan make:controller LoanController --resource

# Create form request
php artisan make:request StoreLoanRequest

# Create middleware
php artisan make:middleware RoleMiddleware

# Create seeder
php artisan make:seeder LoanSeeder

# Run seeders
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Interactive testing
php artisan tinker
```

### Vite/Frontend Commands
```bash
# Install dependencies
yarn install

# Start development server with HMR
yarn dev

# Build for production
yarn build
```

### Git Commands
```bash
# Stage and commit
git add .
git commit -m "Message"

# Push to remote
git push origin main
```

---

## Risk Mitigation

| Risk | Impact | Mitigation |
|------|--------|------------|
| Scope creep | High | Strict phase boundaries, prioritize core features |
| Security vulnerabilities | Critical | Regular security reviews, follow OWASP guidelines |
| Performance issues | Medium | Early optimization, database indexing |
| Integration issues | Medium | Incremental integration, continuous testing |
| Role confusion | High | Enforce 3-role system, clear documentation |

---

## Success Criteria

### Phase Completion Gates

**Phase 1 Complete When:**
- [ ] User can register, login, logout
- [ ] Role middleware protects routes
- [ ] Three test users exist (admin, officer, member)

**Phase 2 Complete When:**
- [ ] Admin can manage all users
- [ ] Role assignment works correctly

**Phase 3 Complete When:**
- [ ] Member can submit loan application
- [ ] Multi-step form validates correctly

**Phase 4 Complete When:**
- [ ] Documents upload successfully
- [ ] Officer can verify documents

**Phase 5 Complete When:**
- [ ] Officer can approve/reject loans
- [ ] Status workflow functions correctly

**Phase 6 Complete When:**
- [ ] Payment schedule generates automatically
- [ ] Payments can be recorded

**Phase 7 Complete When:**
- [ ] All sensitive actions logged
- [ ] Reports generate accurate data

**Phase 8 Complete When:**
- [ ] Security review passed
- [ ] All bugs fixed
- [ ] Documentation complete

---

## Contact & Support

For implementation questions:
- Review `implementation/PRD.md` for specifications
- Check `ai/ROLE_BASED_ACCESS_CONTROL.md` for role definitions
- Reference `.cursor/rules/` for Laravel patterns

---

**Last Updated:** November 30, 2025  
**Status:** Ready for Implementation
