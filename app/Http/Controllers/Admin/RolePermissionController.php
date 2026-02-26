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
    protected const PROTECTED_ROLES = [
        'System Administrator',
        'Department Administrator',
        'Minister',
        'Deputy Minister',
        'Principal Secretary',
    ];

    protected const PROTECTED_PERMISSIONS = [
        'manage-system',
        'manage-ministry',
    ];

    public function __construct()
    {
        $this->middleware('can:manage-roles');
    }

    private function isSystemAdmin(): bool
    {
        return auth()->user()?->can('manage-system') ?? false;
    }

    public function index(): View
    {
        $rolesQuery = Role::with('permissions')->where('guard_name', 'web')->orderBy('name');
        $permsQuery = Permission::where('guard_name', 'web')->orderBy('name');

        if (! $this->isSystemAdmin()) {
            $rolesQuery->whereNotIn('name', self::PROTECTED_ROLES);
            $permsQuery->whereNotIn('name', self::PROTECTED_PERMISSIONS);
        }

        return view('admin.roles.index', [
            'roles' => $rolesQuery->get(),
            'permissions' => $permsQuery->get(),
            'isSystemAdmin' => $this->isSystemAdmin(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*.id' => 'required|exists:roles,id',
            'roles.*.permissions' => 'nullable|array',
            'roles.*.permissions.*' => 'exists:permissions,name',
        ]);

        $isSysAdmin = $this->isSystemAdmin();

        foreach ($request->input('roles', []) as $roleData) {
            $role = Role::findOrFail($roleData['id'] ?? 0);

            if (! $isSysAdmin && in_array($role->name, self::PROTECTED_ROLES, true)) {
                continue;
            }

            $permissions = $roleData['permissions'] ?? [];
            $permissions = is_array($permissions) ? $permissions : [];

            if (! $isSysAdmin) {
                $permissions = array_diff($permissions, self::PROTECTED_PERMISSIONS);
                $existingProtected = $role->permissions()
                    ->whereIn('name', self::PROTECTED_PERMISSIONS)
                    ->pluck('name')
                    ->toArray();
                $permissions = array_unique(array_merge($permissions, $existingProtected));
            }

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Permissions updated.');
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:255|unique:roles,name']);

        Role::create(['name' => $request->name, 'guard_name' => 'web']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        if (! $this->isSystemAdmin() && in_array($role->name, self::PROTECTED_ROLES, true)) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot modify a system-level role.');
        }

        $request->validate(['name' => 'required|string|max:255|unique:roles,name,' . $role->id]);

        $role->update(['name' => $request->name]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true) && ! $this->isSystemAdmin()) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete a system-level role.');
        }

        if ($role->name === 'System Administrator') {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete the System Administrator role.');
        }

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:255|unique:permissions,name']);

        if (! $this->isSystemAdmin() && in_array($request->name, self::PROTECTED_PERMISSIONS, true)) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot create a system-level permission.');
        }

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Permission created.');
    }

    public function destroyPermission(Permission $permission): RedirectResponse
    {
        if (! $this->isSystemAdmin() && in_array($permission->name, self::PROTECTED_PERMISSIONS, true)) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete a system-level permission.');
        }

        $permission->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Permission deleted.');
    }
}
