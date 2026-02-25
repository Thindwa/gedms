<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds units/sections for each department.
 * Table: units (sections in org hierarchy - Ministry → Department → Unit)
 * At least 3 units per department.
 */
class UnitsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = DB::table('departments')->get();
        $now = now();

        // Department-specific units where we have known structure; otherwise use generic set
        $departmentSpecificUnits = [
            'E-Government' => [
                'Government Network Infrastructure',
                'Application Development Support',
                'Policy Planning & Research',
                'Digital Services',
            ],
            "Attorney General's Chambers" => [
                'Legal Advisory Services',
                'Policy & Legislative Drafting',
                'Case Management Support',
                'Constitutional Affairs',
            ],
            'Agriculture Extension Services' => [
                'Field Extension Coordination',
                'Farmer Training & Outreach',
                'Extension Materials Development',
            ],
            'Information' => [
                'Media Relations',
                'Content Management',
                'Information Dissemination',
            ],
            'Budget & Administration' => [
                'Budget Planning',
                'Budget Execution',
                'Administration & Finance',
            ],
        ];

        $defaultUnits = [
            'Administration & Support',
            'Policy & Planning',
            'Operations',
            'Coordination & Liaison',
        ];

        foreach ($departments as $dept) {
            $units = $departmentSpecificUnits[$dept->name] ?? $defaultUnits;

            foreach ($units as $index => $name) {
                $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4)) . ($index + 1);
                DB::table('units')->updateOrInsert(
                    [
                        'department_id' => $dept->id,
                        'name' => $name,
                    ],
                    [
                        'uuid' => Str::uuid()->toString(),
                        'code' => $code,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }
}
