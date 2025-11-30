<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Loan Controller
 * 
 * Manages loan applications.
 * 
 * RBAC Permissions:
 * - Member: Create, view own loans
 * - Officer: View all loans, review, approve, reject
 * - Admin: Full access
 */
class LoanController extends Controller
{
    /**
     * Display a listing of loans.
     * - Members see only their own loans
     * - Officers and Admins see all loans
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Loan::with(['user', 'reviewer']);

        // Members can only see their own loans
        if ($user->isMember()) {
            $query->where('user_id', $user->id);
        }

        // Filter by status
        if ($request->has('status') && in_array($request->status, Loan::STATUSES)) {
            $query->where('status', $request->status);
        }

        // Filter by loan type
        if ($request->has('type') && in_array($request->type, Loan::TYPES)) {
            $query->where('loan_type', $request->type);
        }

        // Search by user name/email (for officers/admins)
        if (!$user->isMember() && $request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('loans.index', [
            'loans' => $loans,
            'statuses' => Loan::STATUSES,
            'types' => Loan::TYPES,
            'currentStatus' => $request->status,
            'currentType' => $request->type,
            'search' => $request->search,
            'isMember' => $user->isMember(),
        ]);
    }

    /**
     * Show the form for creating a new loan.
     * Only Members can create loan applications.
     */
    public function create()
    {
        $user = Auth::user();

        // Only members can apply for loans
        if (!$user->isMember()) {
            return redirect()->route('loans.index')
                ->with('error', 'Only members can apply for loans.');
        }

        return view('loans.create', [
            'loanTypes' => Loan::TYPES,
        ]);
    }

    /**
     * Store a newly created loan.
     * Only Members can submit loan applications.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Only members can apply for loans
        if (!$user->isMember()) {
            return redirect()->route('loans.index')
                ->with('error', 'Only members can apply for loans.');
        }

        $validated = $request->validate([
            'loan_type' => ['required', 'in:' . implode(',', Loan::TYPES)],
            'amount' => ['required', 'numeric', 'min:1000', 'max:1000000'],
            'term_months' => ['required', 'integer', 'min:1', 'max:60'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:50'],
            'purpose' => ['nullable', 'string', 'max:1000'],
        ]);

        $loan = Loan::create([
            'user_id' => $user->id,
            'loan_type' => $validated['loan_type'],
            'amount' => $validated['amount'],
            'term_months' => $validated['term_months'],
            'interest_rate' => $validated['interest_rate'],
            'purpose' => $validated['purpose'] ?? null,
            'status' => Loan::STATUS_PENDING,
        ]);

        // Log loan creation
        AuditService::logLoanAction(
            AuditService::ACTION_LOAN_CREATED,
            $loan->id,
            ['amount' => $loan->amount, 'type' => $loan->loan_type]
        );

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan application submitted successfully.');
    }

    /**
     * Display the specified loan.
     * Members can only view their own loans.
     */
    public function show(Loan $loan)
    {
        $user = Auth::user();

        // Members can only view their own loans
        if ($user->isMember() && $loan->user_id !== $user->id) {
            abort(403, 'You can only view your own loans.');
        }

        $loan->load(['user', 'reviewer', 'documents', 'payments']);

        return view('loans.show', [
            'loan' => $loan,
            'canReview' => $user->isAdminOrOfficer() && $loan->canBeReviewed(),
        ]);
    }

    /**
     * Show the form for editing the specified loan.
     * Only pending loans can be edited, and only by the owner.
     */
    public function edit(Loan $loan)
    {
        $user = Auth::user();

        // Only the owner can edit their pending loans
        if ($loan->user_id !== $user->id) {
            abort(403, 'You can only edit your own loans.');
        }

        if (!$loan->isPending()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'Only pending loans can be edited.');
        }

        return view('loans.edit', [
            'loan' => $loan,
            'loanTypes' => Loan::TYPES,
        ]);
    }

    /**
     * Update the specified loan.
     * Only pending loans can be updated, and only by the owner.
     */
    public function update(Request $request, Loan $loan)
    {
        $user = Auth::user();

        // Only the owner can edit their pending loans
        if ($loan->user_id !== $user->id) {
            abort(403, 'You can only edit your own loans.');
        }

        if (!$loan->isPending()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'Only pending loans can be edited.');
        }

        $validated = $request->validate([
            'loan_type' => ['required', 'in:' . implode(',', Loan::TYPES)],
            'amount' => ['required', 'numeric', 'min:1000', 'max:1000000'],
            'term_months' => ['required', 'integer', 'min:1', 'max:60'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:50'],
            'purpose' => ['nullable', 'string', 'max:1000'],
        ]);

        $loan->update($validated);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan application updated successfully.');
    }

    /**
     * Remove the specified loan.
     * Only pending loans can be deleted, and only by the owner.
     */
    public function destroy(Loan $loan)
    {
        $user = Auth::user();

        // Only the owner or admin can delete pending loans
        if ($loan->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'You can only delete your own loans.');
        }

        if (!$loan->isPending()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'Only pending loans can be deleted.');
        }

        $loanId = $loan->id;
        $loan->delete();

        // Log loan deletion
        AuditService::logLoanAction(
            'loan_deleted',
            $loanId,
            ['deleted_by' => $user->id]
        );

        return redirect()->route('loans.index')
            ->with('success', 'Loan application deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Loan Processing Methods (Officer/Admin only)
    |--------------------------------------------------------------------------
    */

    /**
     * Start reviewing a loan (change status to under_review).
     * Officer/Admin only.
     */
    public function review(Loan $loan)
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can review loans.');
        }

        if (!$loan->isPending()) {
            return back()->with('error', 'This loan has already been reviewed.');
        }

        $loan->update([
            'status' => Loan::STATUS_UNDER_REVIEW,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        AuditService::logLoanAction(
            AuditService::ACTION_LOAN_REVIEWED,
            $loan->id,
            ['reviewer_id' => $user->id]
        );

        return back()->with('success', 'Loan is now under review.');
    }

    /**
     * Approve a loan.
     * Officer/Admin only.
     */
    public function approve(Request $request, Loan $loan)
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can approve loans.');
        }

        if (!$loan->canBeReviewed()) {
            return back()->with('error', 'This loan cannot be approved.');
        }

        $validated = $request->validate([
            'approved_amount' => ['nullable', 'numeric', 'min:1000'],
            'disbursement_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $loan->update([
            'status' => Loan::STATUS_APPROVED,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'approved_amount' => $validated['approved_amount'] ?? $loan->amount,
            'disbursement_date' => $validated['disbursement_date'] ?? null,
        ]);

        AuditService::logLoanAction(
            AuditService::ACTION_LOAN_APPROVED,
            $loan->id,
            [
                'approved_by' => $user->id,
                'approved_amount' => $loan->approved_amount,
            ]
        );

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan approved successfully.');
    }

    /**
     * Reject a loan.
     * Officer/Admin only.
     */
    public function reject(Request $request, Loan $loan)
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can reject loans.');
        }

        if (!$loan->canBeReviewed()) {
            return back()->with('error', 'This loan cannot be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $loan->update([
            'status' => Loan::STATUS_REJECTED,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        AuditService::logLoanAction(
            AuditService::ACTION_LOAN_REJECTED,
            $loan->id,
            [
                'rejected_by' => $user->id,
                'reason' => $validated['rejection_reason'],
            ]
        );

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan rejected.');
    }

    /**
     * Activate a loan (after disbursement).
     * Officer/Admin only.
     */
    public function activate(Loan $loan)
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can activate loans.');
        }

        if (!$loan->isApproved()) {
            return back()->with('error', 'Only approved loans can be activated.');
        }

        // Set outstanding balance to approved amount when activating
        $approvedAmount = $loan->approved_amount ?? $loan->amount;

        $loan->update([
            'status' => Loan::STATUS_ACTIVE,
            'disbursement_date' => $loan->disbursement_date ?? now(),
            'outstanding_balance' => $approvedAmount,
            'total_paid' => 0,
        ]);

        AuditService::logLoanAction(
            AuditService::ACTION_LOAN_DISBURSED,
            $loan->id,
            ['activated_by' => $user->id, 'outstanding_balance' => $approvedAmount]
        );

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan activated and disbursed.');
    }
}
