<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use App\Models\Document;
use App\Models\AuditLog;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Dashboard with key metrics
     */
    public function dashboard()
    {
        AuthService::requireOfficerOrAdmin();

        // Loan Statistics
        $loanStats = [
            'total' => Loan::count(),
            'pending' => Loan::where('status', Loan::STATUS_PENDING)->count(),
            'under_review' => Loan::where('status', Loan::STATUS_UNDER_REVIEW)->count(),
            'approved' => Loan::where('status', Loan::STATUS_APPROVED)->count(),
            'active' => Loan::where('status', Loan::STATUS_ACTIVE)->count(),
            'completed' => Loan::where('status', Loan::STATUS_COMPLETED)->count(),
            'defaulted' => Loan::where('status', Loan::STATUS_DEFAULTED)->count(),
        ];

        // Financial Statistics
        $totalDisbursed = Loan::whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_COMPLETED])
            ->sum('amount');
        $totalCollected = Payment::where('status', 'confirmed')->sum('amount');
        $totalOutstanding = Loan::where('status', Loan::STATUS_ACTIVE)->sum('outstanding_balance');

        // Recent Activity
        $recentLoans = Loan::with('user')
            ->latest()
            ->take(5)
            ->get();
        
        $recentPayments = Payment::with(['loan.user', 'recorder'])
            ->where('status', 'confirmed')
            ->latest('payment_date')
            ->take(5)
            ->get();

        // Overdue Payments
        $overduePayments = Payment::with(['loan.user'])
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();

        // Documents Pending Verification (using is_verified boolean)
        $pendingDocuments = Document::where('is_verified', false)->count();

        // User Statistics
        $userStats = [
            'total' => User::count(),
            'admins' => User::where('role', User::ROLE_ADMIN)->count(),
            'officers' => User::where('role', User::ROLE_OFFICER)->count(),
            'members' => User::where('role', User::ROLE_MEMBER)->count(),
        ];

        return view('reports.dashboard', compact(
            'loanStats',
            'totalDisbursed',
            'totalCollected',
            'totalOutstanding',
            'recentLoans',
            'recentPayments',
            'overduePayments',
            'pendingDocuments',
            'userStats'
        ));
    }

    /**
     * Loan summary report
     */
    public function loanSummary(Request $request)
    {
        AuthService::requireOfficerOrAdmin();

        $query = Loan::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $loans = $query->latest()->paginate(20);

        // Summary statistics for filtered results
        $summary = [
            'total_count' => $query->count(),
            'total_amount' => Loan::whereIn('id', $query->pluck('id'))->sum('amount'),
            'total_outstanding' => Loan::whereIn('id', $query->pluck('id'))->sum('outstanding_balance'),
        ];

        $statuses = [
            Loan::STATUS_PENDING => 'Pending',
            Loan::STATUS_UNDER_REVIEW => 'Under Review',
            Loan::STATUS_APPROVED => 'Approved',
            Loan::STATUS_REJECTED => 'Rejected',
            Loan::STATUS_ACTIVE => 'Active',
            Loan::STATUS_COMPLETED => 'Completed',
            Loan::STATUS_DEFAULTED => 'Defaulted',
        ];

        return view('reports.loan-summary', compact('loans', 'summary', 'statuses'));
    }

    /**
     * Payment collection report
     */
    public function paymentCollection(Request $request)
    {
        AuthService::requireOfficerOrAdmin();

        $query = Payment::with(['loan.user', 'recorder']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'confirmed');
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $payments = $query->latest('payment_date')->paginate(20);

        // Summary statistics
        $baseQuery = Payment::query();
        if ($request->filled('from_date')) {
            $baseQuery->whereDate('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $baseQuery->whereDate('payment_date', '<=', $request->to_date);
        }

        $summary = [
            'total_collected' => (clone $baseQuery)->where('status', 'confirmed')->sum('amount'),
            'total_pending' => (clone $baseQuery)->where('status', 'pending')->sum('amount'),
            'principal_collected' => (clone $baseQuery)->where('status', 'confirmed')->sum('principal_amount'),
            'interest_collected' => (clone $baseQuery)->where('status', 'confirmed')->sum('interest_amount'),
        ];

        return view('reports.payment-collection', compact('payments', 'summary'));
    }

    /**
     * Delinquency report - overdue payments
     */
    public function delinquency(Request $request)
    {
        AuthService::requireOfficerOrAdmin();

        $query = Payment::with(['loan.user'])
            ->where('status', 'pending')
            ->where('due_date', '<', now());

        // Filter by days overdue
        if ($request->filled('min_days')) {
            $query->where('due_date', '<=', now()->subDays($request->min_days));
        }

        $overduePayments = $query->orderBy('due_date')->paginate(20);

        // Group by loan for summary
        $loanIds = Payment::where('status', 'pending')
            ->where('due_date', '<', now())
            ->distinct()
            ->pluck('loan_id');

        $delinquentLoans = Loan::with('user')
            ->whereIn('id', $loanIds)
            ->get()
            ->map(function ($loan) {
                $loan->overdue_count = $loan->payments()
                    ->where('status', 'pending')
                    ->where('due_date', '<', now())
                    ->count();
                $loan->overdue_amount = $loan->payments()
                    ->where('status', 'pending')
                    ->where('due_date', '<', now())
                    ->sum('amount');
                $loan->oldest_overdue = $loan->payments()
                    ->where('status', 'pending')
                    ->where('due_date', '<', now())
                    ->min('due_date');
                return $loan;
            });

        $summary = [
            'total_delinquent_loans' => $loanIds->count(),
            'total_overdue_payments' => Payment::where('status', 'pending')->where('due_date', '<', now())->count(),
            'total_overdue_amount' => Payment::where('status', 'pending')->where('due_date', '<', now())->sum('amount'),
        ];

        return view('reports.delinquency', compact('overduePayments', 'delinquentLoans', 'summary'));
    }

    /**
     * Audit log viewer (admin only)
     */
    public function auditLog(Request $request)
    {
        AuthService::requireAdmin();

        $query = AuditLog::with('user')->latest();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(50);

        // Get unique actions for filter dropdown
        $actions = AuditLog::distinct()->pluck('action')->sort();
        $users = User::whereIn('id', AuditLog::distinct()->pluck('user_id'))->get();

        return view('reports.audit-log', compact('logs', 'actions', 'users'));
    }
}
