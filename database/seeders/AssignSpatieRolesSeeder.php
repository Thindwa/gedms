<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Assigns Spatie roles to users based on their role column.
 * Run after UsersSeeder and EdmsSeeder so all users have correct permissions.
 */
class AssignSpatieRolesSeeder extends Seeder
{
    private array $roleMap = [
        'System Admin' => 'System Administrator',
        'Ministry PS' => 'Principal Secretary',
        'Department PS' => 'Principal Secretary',
        'Department Admin' => 'Department Administrator',
        'Minister' => 'Minister',
        'Director' => 'Director',
        'Chief Officer' => 'Chief Officer',
        'Officer' => 'Officer',
        'Clerk' => 'Clerk',
        'Records Officer' => 'Records Officer',
        'Auditor' => 'Auditor',
    ];

    public function run(): void
    {
        foreach ($this->roleMap as $columnRole => $spatieRole) {
            $users = User::where('role', $columnRole)->get();
            foreach ($users as $user) {
                if (!$user->hasRole($spatieRole)) {
                    $user->syncRoles([$spatieRole]);
                }
            }
        }

        // Fix users with .admin@edms.gov.mw email who have department_id but no Spatie roles
        $adminUserIds = User::whereNotNull('department_id')
            ->where('email', 'like', '%.admin@edms.gov.mw')
            ->whereDoesntHave('roles')
            ->pluck('id');
        foreach ($adminUserIds as $id) {
            User::find($id)?->syncRoles(['Department Administrator']);
        }
    }
}
