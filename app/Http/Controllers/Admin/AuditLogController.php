<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()?->can('view-audit-logs') || auth()->user()?->can('view-audit-only')) {
                return $next($request);
            }
            abort(403);
        });
    }

    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%' . $request->subject_type . '%');
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $user = auth()->user();
        if (!$user->can('manage-system')) {
            $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
            $departmentId = $user->department_id;
            if ($departmentId) {
                $query->where('department_id_scope', (string) $departmentId);
            } elseif ($ministryId) {
                $query->where('ministry_id_scope', (string) $ministryId);
            }
        }

        $logs = $query->limit(2000)->get();

        $actions = AuditLog::distinct()->pluck('action')->sort()->values();

        return view('admin.audit-logs.index', compact('logs', 'actions'));
    }
}
