<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\File;
use App\Models\FileVersion;
use App\Models\SensitivityLevel;
use App\Models\User;
use App\Models\WorkflowInstance;
use Illuminate\Support\Facades\DB;

class DocumentService
{
    public function __construct(
        protected AuditService $auditService,
        protected WorkflowService $workflowService
    ) {}

    public function promote(File $file, array $metadata, User $user): Document
    {
        $file->document()->exists() && abort(400, 'File has already been promoted to an official document');
        $file->storageSpace || abort(400, 'File must belong to a storage space');
        $user->ministry_id || abort(400, 'User must belong to a ministry');
        $user->department_id || abort(400, 'User must belong to a department');

        $documentType = DocumentType::findOrFail($metadata['document_type_id']);
        $sensitivityLevel = SensitivityLevel::findOrFail($metadata['sensitivity_level_id']);

        $ministryId = $metadata['ministry_id'] ?? $user->ministry_id;
        $departmentId = $metadata['department_id'] ?? $user->department_id;
        $ownerId = $metadata['owner_id'] ?? $user->id;

        if ($documentType->ministry_id && $documentType->ministry_id !== (int) $ministryId) {
            abort(400, 'Document type is not available for this ministry');
        }

        $fileVersion = $file->versions()->where('version', $file->version)->first();
        $fileVersion || abort(400, 'File has no content');

        $requiresWorkflow = $metadata['requires_workflow'] ?? true;

        return DB::transaction(function () use ($file, $metadata, $user, $documentType, $sensitivityLevel, $ministryId, $departmentId, $ownerId, $fileVersion, $requiresWorkflow) {
            $document = Document::create([
                'file_id' => $file->id,
                'document_type_id' => $documentType->id,
                'ministry_id' => $ministryId,
                'department_id' => $departmentId,
                'owner_id' => $ownerId,
                'sensitivity_level_id' => $sensitivityLevel->id,
                'title' => $metadata['title'],
                'status' => Document::STATUS_DRAFT,
                'requires_workflow' => $requiresWorkflow,
                'current_version' => 1,
            ]);

            DocumentVersion::create([
                'document_id' => $document->id,
                'file_version_id' => $fileVersion->id,
                'version' => 1,
                'created_by' => $user->id,
                'comment' => 'Initial version from promotion',
            ]);

            $this->auditService->log('document.promoted', Document::class, $document->id, null, [
                'file_id' => $file->id,
                'title' => $document->title,
            ]);

            return $document->fresh(['file', 'documentType', 'ministry', 'department', 'owner', 'sensitivityLevel']);
        });
    }

    public function submitForReview(Document $document, User $user): Document
    {
        $document->owner_id === $user->id || $user->can('approve-documents') || abort(403);
        $document->status === Document::STATUS_DRAFT || abort(400, 'Only draft documents can be submitted');
        $document->isCheckedOut() && abort(400, 'Check in before submitting');

        $document->update(['status' => Document::STATUS_UNDER_REVIEW]);
        if ($document->requires_workflow !== false) {
            $this->workflowService->startWorkflow($document);
        }

        $this->auditService->log('document.submitted', Document::class, $document->id, null, ['status' => Document::STATUS_UNDER_REVIEW]);
        return $document->fresh();
    }

    public function promoteToApproved(Document $document, User $user): Document
    {
        ($document->owner_id === $user->id || $user->can('approve-documents')) || abort(403);
        $document->status === Document::STATUS_DRAFT || abort(400, 'Only draft documents can be promoted');
        ($document->requires_workflow ?? true) && abort(400, 'Use Submit for Review for workflow documents');

        DB::transaction(function () use ($document, $user) {
            $docVersion = $document->versions()->where('version', $document->current_version)->first();
            if ($docVersion) {
                $docVersion->update(['approved_by' => $user->id, 'approved_at' => now()]);
            }
            $document->update(['status' => Document::STATUS_APPROVED]);
        });

        $this->auditService->log('document.approved', Document::class, $document->id, null, ['approved_by' => $user->id, 'direct_promote' => true]);
        return $document->fresh();
    }

    public function approve(Document $document, User $user): Document
    {
        $user->can('approve-documents') || abort(403);
        $document->status === Document::STATUS_UNDER_REVIEW || abort(400, 'Only documents under review can be approved');

        $workflow = $document->workflowInstances()->where('status', WorkflowInstance::STATUS_IN_PROGRESS)->first();
        if ($workflow) {
            abort(400, 'Document has an active workflow. Approve workflow steps instead.');
        }

        DB::transaction(function () use ($document, $user) {
            $docVersion = $document->versions()->where('version', $document->current_version)->first();
            if ($docVersion) {
                $docVersion->update([
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);
            }
            $document->update(['status' => Document::STATUS_APPROVED]);
        });

        $this->auditService->log('document.approved', Document::class, $document->id, null, ['approved_by' => $user->id]);
        return $document->fresh();
    }

    public function approveFromWorkflow(Document $document, User $user): Document
    {
        $document->update(['status' => Document::STATUS_APPROVED]);
        $docVersion = $document->versions()->where('version', $document->current_version)->first();
        if ($docVersion) {
            $docVersion->update(['approved_by' => $user->id, 'approved_at' => now()]);
        }
        $this->auditService->log('document.approved', Document::class, $document->id, null, ['approved_by' => $user->id, 'via_workflow' => true]);
        return $document->fresh();
    }

    public function reject(Document $document, string $comment, User $user): Document
    {
        $user->can('approve-documents') || abort(403);
        $document->status === Document::STATUS_UNDER_REVIEW || abort(400, 'Only documents under review can be rejected');

        $document->update(['status' => Document::STATUS_DRAFT]);
        $this->auditService->log('document.rejected', Document::class, $document->id, null, ['comment' => $comment]);
        return $document->fresh();
    }

    public function archive(Document $document, User $user): Document
    {
        $user->can('approve-documents') || abort(403);
        $document->status !== Document::STATUS_APPROVED && abort(400, 'Only approved documents can be archived');

        $document->update(['status' => Document::STATUS_ARCHIVED]);
        $this->auditService->log('document.archived', Document::class, $document->id, null, []);
        return $document->fresh();
    }

    public function checkOut(Document $document, User $user): Document
    {
        $document->canEdit($user) || abort(403);
        $document->isCheckedOut() && $document->checked_out_by !== $user->id && abort(400, 'Document is checked out by another user');
        $document->isApproved() && abort(400, 'Approved documents cannot be edited');

        $document->update([
            'checked_out_by' => $user->id,
            'checked_out_at' => now(),
        ]);
        $this->auditService->log('document.checkout', Document::class, $document->id, null, ['user_id' => $user->id]);
        return $document->fresh();
    }

    public function checkIn(Document $document, File $file, User $user): Document
    {
        $document->checked_out_by === $user->id || abort(403, 'Only the user who checked out can check in');
        $document->file_id !== $file->id && abort(400, 'File does not belong to this document');

        $fileVersion = $file->versions()->where('version', $file->version)->first();
        $latestDocVersion = $document->versions()->orderByDesc('version')->first();
        $latestDocVersion && $fileVersion->id === $latestDocVersion->file_version_id && abort(400, 'No new version to check in');

        return DB::transaction(function () use ($document, $file, $fileVersion, $user) {
            $newVersion = ($document->versions()->max('version') ?? 0) + 1;

            DocumentVersion::create([
                'document_id' => $document->id,
                'file_version_id' => $fileVersion->id,
                'version' => $newVersion,
                'created_by' => $user->id,
                'comment' => 'Check-in',
            ]);

            $document->update([
                'checked_out_by' => null,
                'checked_out_at' => null,
                'current_version' => $newVersion,
            ]);

            $this->auditService->log('document.checkin', Document::class, $document->id, null, ['version' => $newVersion]);
            return $document->fresh();
        });
    }

    public function cancelCheckOut(Document $document, User $user): Document
    {
        ($document->checked_out_by === $user->id || $user->can('approve-documents')) || abort(403);

        $document->update([
            'checked_out_by' => null,
            'checked_out_at' => null,
        ]);
        $this->auditService->log('document.cancel_checkout', Document::class, $document->id, null, []);
        return $document->fresh();
    }
}
