<?php

namespace App\Http\Controllers;

use App\Models\WorkflowStepInstance;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Dashboard for documents pending current user's workflow step approval.
 */
class ApprovalDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:approve-documents');
    }

    public function index(Request $request): View
    {
        $user = auth()->user();

        $pendingSteps = WorkflowStepInstance::with([
            'workflowInstance.document.file',
            'workflowInstance.document.documentType',
            'workflowInstance.document.owner',
            'workflowStep',
        ])
            ->where('status', 'pending')
            ->whereHas('workflowInstance', fn ($q) => $q->where('status', 'in_progress'))
            ->get()
            ->filter(fn ($step) => app(\App\Services\WorkflowService::class)->canApproveStep($step, $user));

        return view('approvals.index', compact('pendingSteps'));
    }
}
