<?php

namespace Database\Seeders;

use App\Models\Ministry;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Ensures System Administrator has Spatie role. No additional users created.
 */
class EdmsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'system.admin@edms.gov.mw')->first();
        if ($user && !$user->hasRole('System Administrator')) {
            $user->syncRoles(['System Administrator']);
        }
    }
}
