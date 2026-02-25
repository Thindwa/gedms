<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds departments for each Malawi government ministry.
 * Table: departments
 */
class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $ministries = DB::table('ministries')->get()->keyBy('name');
        $now = now();

        $map = [
            'Ministry of Defence' => [
                'Defence Administration', 'Military Operations', 'Defence Policy and Planning',
                'Human Resources and Logistics',
            ],
            'Ministry of State' => [
                'Cabinet Affairs', 'Presidential Delivery Unit', 'Government Communications',
                'Administration and Support',
            ],
            'Ministry of Finance, Economic Planning and Decentralisation' => [
                'Economic Policy Unit', 'Revenue Policy Unit', 'Budget and Administration',
                'Debt and Aid Management', 'Monitoring and Evaluation', 'Accountant General',
                'Internal Audit', 'Public Financial Management',
            ],
            'Ministry of Agriculture, Irrigation and Water Development' => [
                'Agriculture Extension Services', 'Crop Development', 'Animal Health and Livestock',
                'Agriculture Research and Innovation', 'Planning and Policy Support',
                'Land Resource and Conservation', 'Irrigation Services', 'Agricultural Marketing',
            ],
            'Ministry of Education, Science and Technology' => [
                'Primary Education', 'Secondary Education', 'Higher Education',
                'Teacher Education and Development', 'Planning and Policy', 'Curriculum Development',
                'Special Needs Education',
            ],
            'Ministry of Justice and Constitutional Affairs' => [
                "Attorney General's Chambers", 'Directorate of Public Prosecutions',
                'Registrar General', "Administrator General's Office", 'Legal Aid Bureau',
                'Law Commission',
            ],
            'Ministry of Foreign Affairs and International Cooperation' => [
                'Political Affairs', 'Economic Cooperation', 'Protocol and Consular',
                'Administration', 'Africa and Regional Integration',
            ],
            'Ministry of Health and Sanitation' => [
                'Clinical Services', 'Preventive Health Services', 'Pharmacy and Medicines',
                'Health Planning and Policy', 'Health Research', 'Nursing Services',
            ],
            'Ministry of Local Government and Rural Development' => [
                'Local Government Administration', 'District Development', 'Urban Development',
                'Traditional Authority Affairs', 'Planning and Coordination',
            ],
            'Ministry of Industrialisation, Business, Trade and Tourism' => [
                'Trade Policy', 'Industrial Development', 'Tourism Development',
                'Business Registration', 'Standards and Quality Assurance',
            ],
            'Ministry of Transport and Public Works' => [
                'Roads Authority', 'Civil Aviation', 'Maritime and Inland Waterways',
                'Public Works', 'Transport Planning and Policy',
            ],
            'Ministry of Homeland Security' => [
                'Immigration', 'Correctional Services', 'Fire and Rescue',
                'Disaster Management', 'Security Policy',
            ],
            'Ministry of Gender, Children, Disability and Social Welfare' => [
                'Gender Development', 'Child Development', 'Disability Affairs',
                'Social Welfare', 'Community Development',
            ],
            'Ministry of Natural Resources, Energy and Mining' => [
                'Forestry', 'Environmental Affairs', 'Mining', 'Energy',
                'Water Development', 'Geological Survey',
            ],
            'Ministry of Lands, Housing and Urban Development' => [
                'Lands Administration', 'Physical Planning', 'Housing Development',
                'Survey', 'Land Reform',
            ],
            'Ministry of Labour, Skills and Innovation' => [
                'Labour Administration', 'Employment Services', 'Occupational Safety and Health',
                'Technical Education and Vocational Training', 'Innovation and Technology',
            ],
            'Ministry of Youth, Sports and Culture' => [
                'Youth Development', 'Sports Development', 'Culture and Heritage',
                'National Library Services', 'Arts and Creative Industries',
            ],
            'Ministry of Information and Digitalization' => [
                'Information', 'E-Government', 'Administration', 'Broadcasting',
                'Media and Communications',
            ],
        ];

        foreach ($map as $ministryName => $departments) {
            $ministry = $ministries->get($ministryName);
            if (!$ministry) {
                continue;
            }

            foreach ($departments as $name) {
                DB::table('departments')->updateOrInsert(
                    ['ministry_id' => $ministry->id, 'name' => $name],
                    [
                        'uuid' => Str::uuid()->toString(),
                        'code' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 8)),
                        'description' => null,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }
}
