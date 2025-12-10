<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Audit Logging Service
 * 
 * Logs all sensitive actions for compliance and security tracking.
 * Required for RBAC compliance - logs role changes, loan approvals, etc.
 * 
 * Usage:
 *   AuditService::log('loan_approved', 'Loan', $loanId);
 *   AuditService::log('role_changed', 'User', $userId, ['old' => 'member'], ['new' => 'officer']);
 */
class AuditService
{
    /**
     * Log an action to the audit log.
     *
     * @param string $action Action performed (e.g., 'loan_approved', 'role_changed')
     * @param string $entityType Type of entity (e.g., 'Loan', 'User', 'Document')
     * @param int|null $entityId ID of the affected entity
     * @param array|null $oldValues Previous values (for updates)
     * @param array|null $newValues New values (for updates)
     * @return AuditLog|null
     */
    public static function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ?AuditLog {
        try {
            return AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log to file if database logging fails
            Log::error('Audit logging failed', [
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Log a loan-related action.
     */
    public static function logLoanAction(string $action, int $loanId, ?array $details = null): ?AuditLog
    {
        return self::log($action, 'Loan', $loanId, null, $details);
    }

    /**
     * Log a user-related action.
     */
    public static function logUserAction(string $action, int $userId, ?array $oldValues = null, ?array $newValues = null): ?AuditLog
    {
        return self::log($action, 'User', $userId, $oldValues, $newValues);
    }

    /**
     * Log a document-related action.
     */
    public static function logDocumentAction(string $action, int $documentId, ?array $details = null): ?AuditLog
    {
        return self::log($action, 'Document', $documentId, null, $details);
    }

    /**
     * Log a role change - critical for RBAC compliance.
     */
    public static function logRoleChange(int $userId, string $oldRole, string $newRole): ?AuditLog
    {
        return self::log(
            'role_changed',
            'User',
            $userId,
            ['role' => $oldRole],
            ['role' => $newRole]
        );
    }

    /**
     * Log a login attempt.
     */
    public static function logLogin(int $userId, bool $success = true): ?AuditLog
    {
        return self::log(
            $success ? 'login_success' : 'login_failed',
            'User',
            $userId
        );
    }

    /**
     * Log a logout.
     */
    public static function logLogout(int $userId): ?AuditLog
    {
        return self::log('logout', 'User', $userId);
    }

    /**
     * Common action constants for consistency.
     */
    public const ACTION_LOGIN_SUCCESS = 'login_success';
    public const ACTION_LOGIN_FAILED = 'login_failed';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_ROLE_CHANGED = 'role_changed';
    public const ACTION_USER_CREATED = 'user_created';
    public const ACTION_USER_UPDATED = 'user_updated';
    public const ACTION_USER_DELETED = 'user_deleted';
    public const ACTION_LOAN_CREATED = 'loan_created';
    public const ACTION_LOAN_SUBMITTED = 'loan_submitted';
    public const ACTION_LOAN_REVIEWED = 'loan_reviewed';
    public const ACTION_LOAN_APPROVED = 'loan_approved';
    public const ACTION_LOAN_REJECTED = 'loan_rejected';
    public const ACTION_LOAN_DISBURSED = 'loan_disbursed';
    public const ACTION_DOCUMENT_UPLOADED = 'document_uploaded';
    public const ACTION_DOCUMENT_VERIFIED = 'document_verified';
    public const ACTION_DOCUMENT_REJECTED = 'document_rejected';
    public const ACTION_PAYMENT_RECORDED = 'payment_recorded';
    // Simulation-specific actions (no real money movement)
    public const ACTION_PAYMENT_SIMULATION_STARTED = 'payment_simulation_started';
    public const ACTION_PAYMENT_SIMULATION_CONFIRMED = 'payment_simulation_confirmed';
    public const ACTION_PAYMENT_SIMULATION_FAILED = 'payment_simulation_failed';
    // Disbursement and schedule actions
    public const ACTION_LOAN_SCHEDULE_CONFIRMED = 'loan_schedule_confirmed';
    public const ACTION_PAYMENT_SUBMITTED_PENDING = 'payment_submitted_pending';
}
