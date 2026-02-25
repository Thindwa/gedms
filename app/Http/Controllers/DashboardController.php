<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\WorkflowStepInstance;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Role-based dashboard per final-check.txt mockups.
 * Officer: My Files, Drafts, Tasks
 * Chief Officer: Section Files, Pending Approvals
 * Director: Department Files, Approval Queue
 * Principal Secretary: Ministry Overview, Dept Approvals
 * Minister: Cross-department docs, Final approvals
 * Records Officer: Retention, Legal Hold, Archiving
 * Auditor: Read-only logs
 */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $tabs = [];
        $primaryContent = [];

        if ($user->hasRole('Officer') || $user->hasRole('Clerk')) {
            $tabs = ['My Files', 'Drafts', 'My Tasks'];
            $tabMap = ['files' => 0, 'drafts' => 1, 'tasks' => 2];
            $primaryContent = [
                'files' => $user->ministry_id
                    ? \App\Models\File::whereHas('storageSpace', fn ($q) => $q->where('owner_user_id', $user->id))
                        ->with('storageSpace')
                        ->latest()
                        ->limit(10)
                        ->get()
                    : collect(),
                'drafts' => Document::where('owner_id', $user->id)->where('status', 'draft')->latest()->limit(5)->get(),
                'tasks' => Document::where('owner_id', $user->id)->whereIn('status', ['draft', 'under_review'])->latest()->limit(5)->get(),
                'memos' => \App\Models\Memo::where('from_user_id', $user->id)->latest()->limit(5)->get(),
            ];
        } elseif ($user->hasRole('Chief Officer') || $user->hasRole('Director') || $user->hasRole('Deputy Director') || $user->hasRole('Principal Secretary') || $user->hasRole('Minister') || $user->hasRole('Deputy Minister')) {
            $approvalSteps = WorkflowStepInstance::with([
                'workflowInstance.document.file',
                'workflowInstance.document.documentType',
                'workflowStep',
            ])
                ->where('status', 'pending')
                ->whereHas('workflowInstance', fn ($q) => $q->where('status', 'in_progress'))
                ->get()
                ->filter(fn ($s) => app(WorkflowService::class)->canApproveStep($s, $user))
                ->values();

            $tabMap = ['scopeFiles' => 0, 'approvals' => 1];
            if ($user->hasRole('Chief Officer')) {
                $tabs = ['Section Files', 'Pending Approvals'];
                $primaryContent = ['approvals' => $approvalSteps->take(10), 'scope' => 'section'];
            } elseif ($user->hasRole('Director') || $user->hasRole('Deputy Director')) {
                $tabs = ['Department Files', 'Approval Queue'];
                $primaryContent = ['approvals' => $approvalSteps->take(10), 'scope' => 'department'];
            } elseif ($user->hasRole('Principal Secretary')) {
                $tabs = ['Ministry Overview', 'Dept Approvals'];
                $primaryContent = ['approvals' => $approvalSteps->take(10), 'scope' => 'ministry'];
            } else {
                $tabs = ['Cross-Ministry Docs', 'Final Approvals'];
                $primaryContent = ['approvals' => $approvalSteps->take(10), 'scope' => 'ministry'];
            }
        } elseif ($user->hasRole('Records Officer')) {
            $tabs = ['Retention', 'Legal Hold', 'Archiving'];
            $tabMap = ['retention' => 0, 'legalHold' => 1, 'archived' => 2];
            $primaryContent = [
                'legalHold' => Document::where('legal_hold', true)->where('ministry_id', $user->ministry_id)->latest()->limit(5)->get(),
                'archived' => Document::where('status', 'archived')->where('ministry_id', $user->ministry_id)->latest()->limit(5)->get(),
            ];
        } elseif ($user->hasRole('Auditor')) {
            $tabs = ['Audit Logs', 'Approvals'];
            $tabMap = ['auditOnly' => 0, 'auditorApprovals' => 1];
            $primaryContent = ['auditOnly' => true];
        }

        if (empty($tabs)) {
            $tabs = ['Overview'];
            $tabMap = ['overview' => 0];
            $primaryContent = [
                'files' => $user->ministry_id
                    ? \App\Models\File::whereHas('storageSpace', fn ($q) => $q->where('owner_user_id', $user->id))
                        ->latest()
                        ->limit(5)
                        ->get()
                    : collect(),
                'drafts' => Document::where('owner_id', $user->id)->where('status', 'draft')->latest()->limit(5)->get(),
                'memos' => \App\Models\Memo::where('from_user_id', $user->id)->latest()->limit(5)->get(),
            ];
        }

        return view('dashboard', [
            'tabs' => $tabs,
            'tabMap' => $tabMap ?? [],
            'content' => $primaryContent,
        ]);
    }
}
