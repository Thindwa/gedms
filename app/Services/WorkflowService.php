<?php

namespace App\Services;

use App\Models\Document;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\WorkflowStepInstance;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function startWorkflow(Document $document): ?WorkflowInstance
    {
        $workflow = $document->documentType?->workflowDefinition;
        if (!$workflow || !$workflow->is_active) {
            return null;
        }

        $existing = WorkflowInstance::where('document_id', $document->id)
            ->whereIn('status', [WorkflowInstance::STATUS_IN_PROGRESS])
            ->exists();
        if ($existing) {
            return null;
        }

        return DB::transaction(function () use ($document, $workflow) {
            $instance = WorkflowInstance::create([
                'document_id' => $document->id,
                'workflow_definition_id' => $workflow->id,
                'status' => WorkflowInstance::STATUS_IN_PROGRESS,
                'current_step_order' => 1,
                'started_at' => now(),
            ]);

            foreach ($workflow->steps as $step) {
                WorkflowStepInstance::create([
                    'workflow_instance_id' => $instance->id,
                    'workflow_step_id' => $step->id,
                    'status' => WorkflowStepInstance::STATUS_PENDING,
                ]);
            }

            $this->auditService->log('workflow.started', WorkflowInstance::class, $instance->id, null, [
                'document_id' => $document->id,
                'workflow' => $workflow->name,
            ]);

            return $instance;
        });
    }

    public function getCurrentSteps(WorkflowInstance $instance): \Illuminate\Support\Collection
    {
        $order = $instance->current_step_order;
        $steps = $instance->workflowDefinition->steps()->where('step_order', $order)->get();
        return $steps->map(function ($step) use ($instance) {
            $si = $instance->stepInstances()->where('workflow_step_id', $step->id)->first();
            return [
                'step' => $step,
                'instance' => $si,
            ];
        })->filter(fn ($s) => $s['instance'] && $s['instance']->status === WorkflowStepInstance::STATUS_PENDING);
    }

    public function canApproveStep(WorkflowStepInstance $stepInstance, User $user): bool
    {
        return $user->hasRole($stepInstance->workflowStep->role_name);
    }

    public function approveStep(WorkflowStepInstance $stepInstance, User $user, ?string $comment = null): WorkflowInstance
    {
        $instance = $stepInstance->workflowInstance;
        $instance->status === WorkflowInstance::STATUS_IN_PROGRESS || abort(400, 'Workflow not in progress');
        $stepInstance->status === WorkflowStepInstance::STATUS_PENDING || abort(400, 'Step already processed');
        $this->canApproveStep($stepInstance, $user) || abort(403, 'You do not have permission to approve this step');

        return DB::transaction(function () use ($stepInstance, $user, $comment, $instance) {
            $stepInstance->update([
                'status' => WorkflowStepInstance::STATUS_APPROVED,
                'completed_by_user_id' => $user->id,
                'completed_at' => now(),
                'comment' => $comment,
            ]);

            $this->auditService->log('workflow.step_approved', WorkflowStepInstance::class, $stepInstance->id, null, [
                'step' => $stepInstance->workflowStep->name,
                'user' => $user->id,
            ]);

            return $this->advanceWorkflow($instance, $user);
        });
    }

    public function rejectStep(WorkflowStepInstance $stepInstance, User $user, ?string $comment = null): WorkflowInstance
    {
        $instance = $stepInstance->workflowInstance;
        $instance->status === WorkflowInstance::STATUS_IN_PROGRESS || abort(400, 'Workflow not in progress');
        $stepInstance->status === WorkflowStepInstance::STATUS_PENDING || abort(400, 'Step already processed');
        $this->canApproveStep($stepInstance, $user) || abort(403);

        return DB::transaction(function () use ($stepInstance, $user, $comment, $instance) {
            $stepInstance->update([
                'status' => WorkflowStepInstance::STATUS_REJECTED,
                'completed_by_user_id' => $user->id,
                'completed_at' => now(),
                'comment' => $comment,
            ]);

            $instance->update([
                'status' => WorkflowInstance::STATUS_REJECTED,
                'completed_at' => now(),
            ]);
            $instance->document->update(['status' => \App\Models\Document::STATUS_DRAFT]);

            $this->auditService->log('workflow.step_rejected', WorkflowStepInstance::class, $stepInstance->id, null, [
                'step' => $stepInstance->workflowStep->name,
            ]);

            return $instance->fresh();
        });
    }

    protected function advanceWorkflow(WorkflowInstance $instance, ?User $user = null): WorkflowInstance
    {
        $currentOrder = $instance->current_step_order;
        $pendingAtCurrent = $instance->stepInstances()
            ->whereHas('workflowStep', fn ($q) => $q->where('step_order', $currentOrder))
            ->where('status', WorkflowStepInstance::STATUS_PENDING)
            ->count();

        if ($pendingAtCurrent > 0) {
            return $instance->fresh();
        }

        $maxOrder = $instance->workflowDefinition->steps()->max('step_order');
        if ($currentOrder >= $maxOrder) {
            $instance->update([
                'status' => WorkflowInstance::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
            $approver = $user ?? $instance->stepInstances()->where('status', 'approved')->latest('completed_at')->first()?->completedBy ?? $instance->document->owner;
            app(DocumentService::class)->approveFromWorkflow($instance->document, $approver);
            return $instance->fresh();
        }

        $instance->update(['current_step_order' => $currentOrder + 1]);
        return $instance->fresh();
    }

    public function isWorkflowComplete(WorkflowInstance $instance): bool
    {
        return $instance->status === WorkflowInstance::STATUS_COMPLETED;
    }
}
