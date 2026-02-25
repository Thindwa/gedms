<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * EDMS roles and permissions aligned with government hierarchy.
 *
 * HIERARCHY (top to bottom):
 * - Minister / Deputy Minister / Principal Secretary: Top policy/approval (cross-departmental)
 * - Director / Deputy Director: Department-level approval
 * - Chief Officer: Unit/Section-level supervision and approvals
 * - Officer / Clerk: Daily document creation, editing, draft handling (no approval)
 * - Records Officer: Official records, retention, disposal
 * - Auditor: Read-only audit access
 *
 * Workflows must respect this hierarchy: Officers cannot approve; Chief Officers approve unit-level;
 * Directors approve departmental; Ministry heads approve cross-departmental.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-system',
            'manage-ministry',
            'manage-department',
            'manage-users',
            'manage-roles',
            'view-audit-logs',
            'manage-document-types',
            'manage-sensitivity-levels',
            'manage-workflows',
            'manage-retention',
            'manage-storage-spaces',
            'view-files',
            'create-files',
            'edit-files',
            'delete-files',
            'share-files',
            'view-documents',
            'create-documents',
            'edit-documents',
            'approve-documents',
            'manage-retention-disposition',
            'view-audit-only',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $baseFiles = ['view-files', 'create-files', 'edit-files', 'share-files'];
        $baseDocuments = ['view-documents', 'create-documents', 'edit-documents'];
        // Approval roles (Minister, Deputy Minister, PS, Director, Deputy Director): document approval only, NO admin/audit
        $approvalPermissions = array_merge($baseFiles, $baseDocuments, ['approve-documents']);

        $roles = [
            // Operational (system config, not approval hierarchy)
            'System Administrator' => ['manage-system', 'manage-ministry', 'manage-department', 'manage-users', 'manage-roles', 'view-audit-logs', 'manage-document-types', 'manage-sensitivity-levels', 'manage-workflows', 'manage-retention', 'manage-storage-spaces', 'view-files', 'create-files', 'edit-files', 'delete-files', 'share-files', 'view-documents', 'create-documents', 'edit-documents', 'approve-documents', 'manage-retention-disposition'],
            'Department Administrator' => ['manage-department', 'manage-users', 'manage-roles', 'manage-document-types', 'manage-sensitivity-levels', 'manage-workflows', 'manage-retention', 'manage-storage-spaces', 'view-audit-logs', 'view-files', 'create-files', 'edit-files', 'delete-files', 'share-files', 'view-documents', 'create-documents', 'edit-documents', 'approve-documents', 'manage-retention-disposition'],

            // Top policy/approval (cross-departmental)
            'Minister' => $approvalPermissions,
            'Deputy Minister' => $approvalPermissions,
            'Principal Secretary' => $approvalPermissions,

            // Department-level approval
            'Director' => $approvalPermissions,
            'Deputy Director' => $approvalPermissions,

            // Unit/Section-level approval
            'Chief Officer' => array_merge($baseFiles, ['delete-files'], $baseDocuments, ['approve-documents']),

            // Daily operations (no approval)
            'Officer' => array_merge($baseFiles, $baseDocuments),
            'Clerk' => array_merge($baseFiles, $baseDocuments),

            // Records management
            'Records Officer' => ['view-files', 'view-documents', 'manage-retention-disposition', 'view-audit-logs'],

            // Read-only audit
            'Auditor' => ['view-audit-only', 'view-documents'],
        ];

        foreach ($roles as $roleName => $permNames) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permNames);
        }

        // Migrate legacy Ministry Administrator -> Department Administrator
        $ministryAdmin = Role::where('name', 'Ministry Administrator')->where('guard_name', 'web')->first();
        if ($ministryAdmin) {
            $deptAdmin = Role::where('name', 'Department Administrator')->where('guard_name', 'web')->first();
            if ($deptAdmin) {
                foreach ($ministryAdmin->users as $user) {
                    $user->removeRole($ministryAdmin);
                    $user->assignRole($deptAdmin);
                }
            }
            $ministryAdmin->delete();
        }

        // Migrate legacy Permanent Secretary -> Principal Secretary
        $permSec = Role::where('name', 'Permanent Secretary')->where('guard_name', 'web')->first();
        if ($permSec) {
            $principalSec = Role::where('name', 'Principal Secretary')->where('guard_name', 'web')->first();
            if ($principalSec) {
                foreach ($permSec->users as $user) {
                    $user->removeRole($permSec);
                    $user->assignRole($principalSec);
                }
            }
            $permSec->delete();
        }

        // Migrate legacy Supervisor -> Chief Officer (for existing installs)
        $supervisor = Role::where('name', 'Supervisor')->where('guard_name', 'web')->first();
        if ($supervisor) {
            $chiefOfficer = Role::where('name', 'Chief Officer')->where('guard_name', 'web')->first();
            if ($chiefOfficer) {
                foreach ($supervisor->users as $user) {
                    $user->removeRole($supervisor);
                    $user->assignRole($chiefOfficer);
                }
            }
            $supervisor->delete();
        }
    }
}
