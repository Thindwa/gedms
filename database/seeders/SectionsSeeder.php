<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds sections for each department. At least 3 sections per department.
 * Table: sections
 */
class SectionsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = DB::table('departments')->get();
        $now = now();

        $specificSections = [
            'E-Government' => [
                'Government Network Infrastructure',
                'Application Development Support',
                'Policy Planning and Research',
                'Digital Services',
            ],
            "Attorney General's Chambers" => [
                'Legal Advisory Services',
                'Policy and Legislative Drafting',
                'Case Management Support',
                'Constitutional Affairs',
            ],
            'Agriculture Extension Services' => [
                'Field Extension Coordination',
                'Farmer Training and Outreach',
                'Extension Materials Development',
            ],
            'Information' => [
                'Media Relations',
                'Content Management',
                'Information Dissemination',
            ],
            'Budget and Administration' => [
                'Budget Planning',
                'Budget Execution',
                'Administration and Finance',
            ],
            'Directorate of Public Prosecutions' => [
                'Criminal Prosecutions',
                'Appeals and Review',
                'Witness Protection',
            ],
            'Primary Education' => [
                'Curriculum Delivery',
                'School Inspection',
                'Teacher Support',
            ],
        ];

        $defaultSections = [
            'Administration and Support',
            'Policy and Planning',
            'Operations',
            'Coordination and Liaison',
        ];

        foreach ($departments as $dept) {
            $sections = $specificSections[$dept->name] ?? $defaultSections;

            foreach ($sections as $name) {
                DB::table('sections')->updateOrInsert(
                    ['department_id' => $dept->id, 'name' => $name],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}
