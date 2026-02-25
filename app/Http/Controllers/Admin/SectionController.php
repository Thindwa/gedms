<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-department');
    }

    public function index(): RedirectResponse|View
    {
        $user = auth()->user();

        // Sections are managed by Department Administrators only — System Admin redirects to dashboard
        if ($user->can('manage-system')) {
            return redirect()->route('admin.index')->with('error', 'Sections are managed by Department Administrators.');
        }

        $query = Section::with(['department.ministry'])->orderBy('name');

        // Department Administrators see only sections in their department
        if ($user->department_id) {
            $query->where('department_id', $user->department_id);
        }

        $sections = $query->get();
        return view('admin.sections.index', compact('sections'));
    }

    public function create(): RedirectResponse|View
    {
        $user = auth()->user();

        if ($user->can('manage-system')) {
            return redirect()->route('admin.index')->with('error', 'Sections are managed by Department Administrators.');
        }

        $departments = Department::with('ministry')->where('is_active', true)->orderBy('name')->get();

        if ($user->department_id) {
            $departments = $departments->where('id', $user->department_id)->values();
        }

        return view('admin.sections.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->can('manage-system')) {
            return redirect()->route('admin.index')->with('error', 'Sections are managed by Department Administrators.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ];
        $request->validate($rules);

        $departmentId = $request->department_id;
        if ($user->can('manage-department') && !$user->can('manage-system') && $user->department_id) {
            if ((int) $departmentId !== (int) $user->department_id) {
                abort(403, 'You can only create sections in your department.');
            }
        }

        Section::create([
            'name' => $request->name,
            'department_id' => $departmentId,
        ]);

        return redirect()->route('admin.sections.index')->with('success', 'Section created.');
    }
}
