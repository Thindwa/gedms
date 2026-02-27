<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Ministry;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-users');
    }

    public function create(): View
    {
        $user = auth()->user();
        $ministries = Ministry::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $sections = Section::with('department')->orderBy('name')->get();
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();

        // System Administrators: only assign System Administrator or Department Administrator
        if ($user->can('manage-system')) {
            $roles = $roles->filter(fn ($r) => in_array($r->name, ['System Administrator', 'Department Administrator']))->values();
        }
        // Department Administrators: only assign operational roles (Director, Chief Officer, Officer, etc.)
        // and cannot assign System Administrator or Department Administrator
        elseif ($user->can('manage-department') && !$user->can('manage-system') && $user->department_id) {
            $ministries = $ministries->where('id', $user->ministry_id)->values();
            $departments = $departments->where('id', $user->department_id)->values();
            $sections = $sections->where('department_id', $user->department_id)->values();
            $roles = $roles->reject(fn ($r) => in_array($r->name, ['System Administrator', 'Department Administrator']))->values();
        }

        return view('admin.users.create', compact('ministries', 'departments', 'sections', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $authUser = auth()->user();
        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'ministry_id' => ['nullable', 'exists:ministries,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['boolean'],
        ];

        $request->validate($validationRules);

        // System Admin can only assign System Administrator or Department Administrator
        if ($authUser->can('manage-system')) {
            if (!in_array($request->role, ['System Administrator', 'Department Administrator'])) {
                return back()->withInput()->withErrors(['role' => 'System Administrators can only assign System Administrator or Department Administrator.']);
            }
        }
        // Department Admin cannot assign System Administrator or Department Administrator
        elseif ($authUser->can('manage-department') && !$authUser->can('manage-system')) {
            if (in_array($request->role, ['System Administrator', 'Department Administrator'])) {
                return back()->withInput()->withErrors(['role' => 'Only System Administrators can create System or Department Administrators.']);
            }
        }
        $ministryId = $request->ministry_id;
        $departmentId = $request->department_id;

        // Department Administrators can only create users in their department
        if ($authUser->can('manage-department') && !$authUser->can('manage-system') && $authUser->department_id) {
            $ministryId = $authUser->ministry_id;
            $departmentId = $authUser->department_id;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'ministry_id' => $ministryId,
            'department_id' => $departmentId,
            'unit_id' => $request->unit_id,
            'section_id' => $request->section_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $authUser = auth()->user();

        if ($authUser->can('manage-department') && !$authUser->can('manage-system') && $authUser->department_id) {
            if ($user->department_id !== $authUser->department_id) {
                abort(403);
            }
        }

        $ministries = Ministry::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $sections = Section::with('department')->orderBy('name')->get();
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();

        if ($authUser->can('manage-system')) {
            $roles = $roles->filter(fn ($r) => in_array($r->name, ['System Administrator', 'Department Administrator']))->values();
        } elseif ($authUser->can('manage-department') && !$authUser->can('manage-system') && $authUser->department_id) {
            $ministries = $ministries->where('id', $authUser->ministry_id)->values();
            $departments = $departments->where('id', $authUser->department_id)->values();
            $sections = $sections->where('department_id', $authUser->department_id)->values();
            $roles = $roles->reject(fn ($r) => in_array($r->name, ['System Administrator', 'Department Administrator']))->values();
        }

        return view('admin.users.edit', compact('user', 'ministries', 'departments', 'sections', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $authUser = auth()->user();

        if ($authUser->can('manage-department') && !$authUser->can('manage-system') && $authUser->department_id) {
            if ($user->department_id !== $authUser->department_id) {
                abort(403);
            }
        }

        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'ministry_id' => ['nullable', 'exists:ministries,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['boolean'],
        ];

        $request->validate($validationRules);

        if ($authUser->can('manage-system')) {
            if (!in_array($request->role, ['System Administrator', 'Department Administrator'])) {
                return back()->withInput()->withErrors(['role' => 'System Administrators can only assign System Administrator or Department Administrator.']);
            }
        } elseif ($authUser->can('manage-department') && !$authUser->can('manage-system')) {
            if (in_array($request->role, ['System Administrator', 'Department Administrator'])) {
                return back()->withInput()->withErrors(['role' => 'Only System Administrators can create System or Department Administrators.']);
            }
        }

        $ministryId = $request->ministry_id;
        $departmentId = $request->department_id;

        if ($authUser->can('manage-department') && !$authUser->can('manage-system') && $authUser->department_id) {
            $ministryId = $authUser->ministry_id;
            $departmentId = $authUser->department_id;
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'ministry_id' => $ministryId,
            'department_id' => $departmentId,
            'unit_id' => $request->unit_id,
            'section_id' => $request->section_id,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = User::with(['ministry', 'department', 'unit', 'section', 'roles'])
            ->orderBy('name');

        // Department Administrators see only users in their department
        if ($user->can('manage-department') && !$user->can('manage-system') && $user->department_id) {
            $query->where('department_id', $user->department_id);
        } elseif ($request->filled('ministry_id')) {
            $query->where('ministry_id', $request->ministry_id);
        }
        if ($request->filled('role')) {
            $role = $request->role;
            $query->where(function ($q) use ($role) {
                $q->role($role)->orWhere('role', $role);
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->get();
        $ministries = \App\Models\Ministry::orderBy('name')->get();
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'ministries', 'roles'));
    }
}
