<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Redirect admin/officer to reports dashboard, members to regular dashboard
    if (auth()->user()->isAdminOrOfficer()) {
        return redirect()->route('reports.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| These routes are protected by the 'role:admin' middleware.
| Only Admin users can access user management.
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management (Admin only)
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
});

// Also expose users at root level for convenience
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
});

/*
|--------------------------------------------------------------------------
| Loan Routes
|--------------------------------------------------------------------------
| All authenticated users can access loans, but permissions vary by role:
| - Member: Create and view own loans
| - Officer/Admin: View all, review, approve, reject
*/
Route::middleware(['auth'])->group(function () {
    Route::resource('loans', LoanController::class);
    
    // Loan processing actions (Officer/Admin only - enforced in controller)
    Route::patch('loans/{loan}/review', [LoanController::class, 'review'])->name('loans.review');
    Route::patch('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::patch('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::patch('loans/{loan}/activate', [LoanController::class, 'activate'])->name('loans.activate');
});

/*
|--------------------------------------------------------------------------
| Document Routes
|--------------------------------------------------------------------------
| Documents are nested under loans:
| - Member: Upload and view own documents
| - Officer/Admin: View all, verify, reject
*/
Route::middleware(['auth'])->group(function () {
    // Verification queue (Officer/Admin only - enforced in controller)
    // Must be before the wildcard route
    Route::get('documents/queue', [DocumentController::class, 'queue'])->name('documents.queue');
    
    // Documents for a specific loan
    Route::get('loans/{loan}/documents', [DocumentController::class, 'index'])->name('loans.documents.index');
    Route::get('loans/{loan}/documents/create', [DocumentController::class, 'create'])->name('loans.documents.create');
    Route::post('loans/{loan}/documents', [DocumentController::class, 'store'])->name('loans.documents.store');
    
    // Individual document actions
    Route::get('documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::patch('documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
    Route::patch('documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');
});

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
| Payments are nested under loans:
| - Member: View own payment history and schedule
| - Officer/Admin: Record payments, update status
*/
Route::middleware(['auth'])->group(function () {
    // Payments for a specific loan
    Route::get('loans/{loan}/payments', [PaymentController::class, 'index'])->name('loans.payments.index');
    Route::get('loans/{loan}/payments/create', [PaymentController::class, 'create'])->name('loans.payments.create');
    Route::post('loans/{loan}/payments', [PaymentController::class, 'store'])->name('loans.payments.store');
    Route::get('loans/{loan}/schedule', [PaymentController::class, 'schedule'])->name('loans.schedule');
    
    // Individual payment actions
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::patch('payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');
});

/*
|--------------------------------------------------------------------------
| Report Routes
|--------------------------------------------------------------------------
| Reports and analytics:
| - Officer/Admin: Dashboard, loan summary, payment collection, delinquency
| - Admin only: Audit log
*/
Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    // Dashboard (Officer/Admin - enforced in controller)
    Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
    
    // Reports (Officer/Admin - enforced in controller)
    Route::get('/loans', [ReportController::class, 'loanSummary'])->name('loans');
    Route::get('/payments', [ReportController::class, 'paymentCollection'])->name('payments');
    Route::get('/delinquency', [ReportController::class, 'delinquency'])->name('delinquency');
    
    // Audit Log (Admin only - enforced in controller)
    Route::get('/audit', [ReportController::class, 'auditLog'])->name('audit');
});

require __DIR__.'/auth.php';
