<?php

namespace Database\Seeders;

use App\Models\SensitivityLevel;
use Illuminate\Database\Seeder;

class SensitivityLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Public', 'code' => 'public', 'sort_order' => 1],
            ['name' => 'Internal', 'code' => 'internal', 'sort_order' => 2],
            ['name' => 'Restricted', 'code' => 'restricted', 'sort_order' => 3],
            ['name' => 'Confidential', 'code' => 'confidential', 'sort_order' => 4],
        ];

        foreach ($levels as $level) {
            SensitivityLevel::firstOrCreate(
                ['code' => $level['code']],
                $level
            );
        }
    }
}
