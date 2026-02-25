<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Document;
use App\Models\Ministry;
use App\Models\RetentionRule;
use App\Models\User;
use App\Models\Section;
use App\Models\DocumentType;
use App\Models\WorkflowStepInstance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): \Illuminate\Http\RedirectResponse|View
    {
        $user = auth()->user();

        // Department Administrators see only their department dashboard
        if ($user->can('manage-department') && !$user->can('manage-system') && $user->department_id) {
            return redirect()->route('admin.department.index');
        }

        $links = [];

        if ($user->can('manage-document-types')) {
            $links[] = ['name' => 'Document Types', 'route' => 'admin.document-types.index'];
        }
        if ($user->can('manage-sensitivity-levels')) {
            $links[] = ['name' => 'Sensitivity Levels', 'route' => 'admin.sensitivity-levels.index'];
        }
        if ($user->can('manage-workflows')) {
            $links[] = ['name' => 'Workflows', 'route' => 'admin.workflows.index'];
        }
        if ($user->can('manage-retention')) {
            $links[] = ['name' => 'Retention Rules', 'route' => 'admin.retention-rules.index'];
        }
        if ($user->can('manage-roles')) {
            $links[] = ['name' => 'Roles & Permissions', 'route' => 'admin.roles.index'];
        }
        if ($user->can('view-audit-logs') || $user->can('view-audit-only')) {
            $links[] = ['name' => 'Audit Logs', 'route' => 'admin.audit-logs.index'];
        }

        if (empty($links)) {
            abort(403, 'No access.');
        }

        $ministriesCount = Ministry::where('is_active', true)->count();
        $departmentsCount = Department::whereHas('ministry', fn ($q) => $q->where('is_active', true))->count();
        $sectionsCount = Section::whereHas('department', fn ($q) => $q->whereHas('ministry', fn ($mq) => $mq->where('is_active', true)))->count();
        $activeUsersCount = User::where('is_active', true)->count();
        $activeDocumentsCount = Document::whereIn('status', ['draft', 'under_review', 'approved'])->count();

        $recentActivity = AuditLog::with('user')->latest('created_at')->limit(10)->get();
        $documentTypes = DocumentType::with('ministry')->where('is_active', true)->limit(5)->get();
        $users = User::with(['ministry', 'department', 'roles'])->where('is_active', true)->limit(5)->get();
        $retentionRules = RetentionRule::with('documentType')->where('is_active', true)->limit(5)->get();

        return view('admin.index', compact(
            'links', 'ministriesCount', 'departmentsCount', 'sectionsCount',
            'activeUsersCount', 'activeDocumentsCount', 'recentActivity',
            'documentTypes', 'users', 'retentionRules'
        ));
    }

    public function department(): View
    {
        $user = auth()->user();
        $user->department_id || abort(403, 'You must belong to a department.');

        $dept = Department::with('ministry')->findOrFail($user->department_id);
        $sectionsCount = $dept->sections()->count();
        $activeUsersCount = User::where('department_id', $dept->id)->where('is_active', true)->count();
        $pendingApprovalsCount = WorkflowStepInstance::whereHas('workflowInstance.document', fn ($q) => $q->where('department_id', $dept->id))
            ->where('status', 'pending')
            ->count();
        $activeDocumentsCount = Document::where('department_id', $dept->id)->whereIn('status', ['draft', 'under_review', 'approved'])->count();

        return view('admin.department', compact(
            'dept', 'sectionsCount', 'activeUsersCount', 'pendingApprovalsCount',
            'activeDocumentsCount'
        ));
    }

    public function departmentSettings(): View
    {
        $user = auth()->user();
        $user->department_id || abort(403, 'You must belong to a department.');

        $dept = Department::with('ministry')->findOrFail($user->department_id);

        return view('admin.department-settings', compact('dept'));
    }

    public function updateDriveStyle(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $user->department_id || abort(403, 'You must belong to a department.');

        $dept = Department::findOrFail($user->department_id);

        $request->validate([
            'drive_style' => 'required|in:drive,sharepoint,dropbox,nextcloud',
        ]);

        $dept->update(['drive_style' => $request->drive_style]);

        return redirect()->route('admin.department.settings')
            ->with('success', 'Drive style updated. Users in your department will see the new layout.');
    }

    public function updateMandatoryFolders(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $user->department_id || abort(403, 'You must belong to a department.');

        $dept = Department::findOrFail($user->department_id);

        $normalize = fn (array $arr) => array_slice(array_unique(array_values(array_filter(array_map('trim', $arr)))), 0, 50);
        $raw = $request->input('mandatory_folders', []) ?: [];

        foreach ($dept->sections as $section) {
            $arr = $raw[$section->id] ?? [];
            $section->update(['mandatory_folders' => $normalize(is_array($arr) ? $arr : [])]);
        }

        return redirect()->route('admin.department.settings')
            ->with('success', 'Mandatory folders updated for each section.');
    }
}
