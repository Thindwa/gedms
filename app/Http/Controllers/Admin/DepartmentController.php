<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Ministry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-department');
    }

    public function index(): \Illuminate\Http\RedirectResponse|View
    {
        $user = auth()->user();

        // Department Administrators don't see global departments list — redirect to their department dashboard
        if ($user->can('manage-department') && !$user->can('manage-system') && $user->department_id) {
            return redirect()->route('admin.department.index');
        }

        $departments = Department::with(['ministry'])->withCount(['users'])->orderBy('name')->get();
        return view('admin.departments.index', compact('departments'));
    }

    /** Create department — System Admin only (manage-ministry) */
    public function create(): View
    {
        abort_unless(auth()->user()->can('manage-ministry'), 403, 'Only System Administrators can create departments.');
        $ministries = Ministry::where('is_active', true)->orderBy('name')->get();
        return view('admin.departments.create', compact('ministries'));
    }

    /** Store department — System Admin only */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage-ministry'), 403, 'Only System Administrators can create departments.');
        $request->validate([
            'name' => 'required|string|max:255',
            'ministry_id' => 'required|exists:ministries,id',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);
        Department::create([
            'name' => $request->name,
            'ministry_id' => $request->ministry_id,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => true,
        ]);
        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully.');
    }
}
