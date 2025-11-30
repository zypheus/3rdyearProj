<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Loan;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Document Controller
 * 
 * Manages document uploads for loan applications.
 * 
 * RBAC Permissions:
 * - Member: Upload documents for own loans, view own documents
 * - Officer: View all documents, verify/reject documents
 * - Admin: Full access
 */
class DocumentController extends Controller
{
    /**
     * Display a listing of documents for a loan.
     */
    public function index(Loan $loan)
    {
        $user = Auth::user();

        // Members can only see their own loan documents
        if ($user->isMember() && $loan->user_id !== $user->id) {
            abort(403, 'You can only view documents for your own loans.');
        }

        $documents = $loan->documents()->with('verifier')->get();

        return view('documents.index', [
            'loan' => $loan,
            'documents' => $documents,
            'canUpload' => $loan->user_id === $user->id && $loan->canBeReviewed(),
            'canVerify' => $user->isAdminOrOfficer(),
        ]);
    }

    /**
     * Show the form for uploading a document.
     */
    public function create(Loan $loan)
    {
        $user = Auth::user();

        // Only loan owner can upload documents
        if ($loan->user_id !== $user->id) {
            abort(403, 'You can only upload documents for your own loans.');
        }

        if (!$loan->canBeReviewed()) {
            return back()->with('error', 'Documents can only be uploaded for pending or under review loans.');
        }

        return view('documents.create', [
            'loan' => $loan,
            'documentTypes' => Document::TYPES,
        ]);
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(Request $request, Loan $loan)
    {
        $user = Auth::user();

        // Only loan owner can upload documents
        if ($loan->user_id !== $user->id) {
            abort(403, 'You can only upload documents for your own loans.');
        }

        if (!$loan->canBeReviewed()) {
            return back()->with('error', 'Documents can only be uploaded for pending or under review loans.');
        }

        $validated = $request->validate([
            'document_type' => ['required', 'in:' . implode(',', Document::TYPES)],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'], // 10MB max
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/' . $loan->id, 'local');

        $document = Document::create([
            'loan_id' => $loan->id,
            'user_id' => $user->id,
            'document_type' => $validated['document_type'],
            'filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'notes' => $validated['notes'] ?? null,
            'is_verified' => false,
        ]);

        AuditService::logDocumentAction(
            AuditService::ACTION_DOCUMENT_UPLOADED,
            $document->id,
            ['loan_id' => $loan->id, 'type' => $document->document_type]
        );

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display/download the specified document.
     */
    public function show(Document $document)
    {
        $user = Auth::user();

        // Members can only view their own documents
        if ($user->isMember() && $document->user_id !== $user->id) {
            abort(403, 'You can only view your own documents.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Document file not found.');
        }

        return Storage::disk('local')->download($document->file_path, $document->filename);
    }

    /**
     * Remove the specified document.
     * Only unverified documents can be deleted by owner.
     */
    public function destroy(Document $document)
    {
        $user = Auth::user();

        // Only owner or admin can delete
        if ($document->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'You can only delete your own documents.');
        }

        if ($document->is_verified) {
            return back()->with('error', 'Verified documents cannot be deleted.');
        }

        // Check if loan is still in reviewable state
        if (!$document->loan->canBeReviewed()) {
            return back()->with('error', 'Documents cannot be deleted for processed loans.');
        }

        // Delete the file
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $documentId = $document->id;
        $document->delete();

        AuditService::logDocumentAction(
            'document_deleted',
            $documentId,
            ['deleted_by' => $user->id]
        );

        return back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Verify a document.
     * Officer/Admin only.
     */
    public function verify(Request $request, Document $document)
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can verify documents.');
        }

        if ($document->is_verified) {
            return back()->with('error', 'Document is already verified.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $document->update([
            'is_verified' => true,
            'verified_by' => $user->id,
            'verified_at' => now(),
            'notes' => $validated['notes'] ?? $document->notes,
        ]);

        AuditService::logDocumentAction(
            AuditService::ACTION_DOCUMENT_VERIFIED,
            $document->id,
            ['verified_by' => $user->id]
        );

        return back()->with('success', 'Document verified successfully.');
    }

    /**
     * Reject a document.
     * Officer/Admin only.
     */
    public function reject(Request $request, Document $document)
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can reject documents.');
        }

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ]);

        // Mark as not verified with rejection notes
        $document->update([
            'is_verified' => false,
            'verified_by' => $user->id,
            'verified_at' => now(),
            'notes' => 'REJECTED: ' . $validated['notes'],
        ]);

        AuditService::logDocumentAction(
            AuditService::ACTION_DOCUMENT_REJECTED,
            $document->id,
            ['rejected_by' => $user->id, 'reason' => $validated['notes']]
        );

        return back()->with('success', 'Document rejected.');
    }

    /**
     * Show verification queue for officers/admins.
     */
    public function queue()
    {
        $user = Auth::user();

        if (!$user->isAdminOrOfficer()) {
            abort(403, 'Only officers and admins can access the verification queue.');
        }

        $documents = Document::with(['loan.user', 'uploader'])
            ->where('is_verified', false)
            ->whereDoesntHave('loan', function ($q) {
                $q->whereIn('status', ['rejected', 'completed', 'defaulted']);
            })
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('documents.queue', [
            'documents' => $documents,
        ]);
    }
}
