<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-roles');
    }

    public function index(): View
    {
        $roles = Role::with('permissions')->where('guard_name', 'web')->orderBy('name')->get();
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*.id' => 'required|exists:roles,id',
            'roles.*.permissions' => 'nullable|array',
            'roles.*.permissions.*' => 'exists:permissions,name',
        ]);

        foreach ($request->input('roles', []) as $roleData) {
            $role = Role::findOrFail($roleData['id'] ?? 0);
            $permissions = $roleData['permissions'] ?? [];
            $role->syncPermissions(is_array($permissions) ? $permissions : []);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Permissions updated.');
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $request->validate(['role_name' => 'required|string|max:255|unique:roles,name']);

        Role::create(['name' => $request->role_name, 'guard_name' => 'web']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:255|unique:roles,name,' . $role->id]);

        $role->update(['name' => $request->name]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        $protected = ['System Administrator', 'system-administrator'];
        if (in_array($role->name, $protected, true)) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete protected role.');
        }
        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:255|unique:permissions,name']);

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Permission created.');
    }

    public function destroyPermission(Permission $permission): RedirectResponse
    {
        $permission->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Permission deleted.');
    }
}
