<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds all Malawi government ministries.
 * Table: ministries
 */
class MinistriesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $ministries = [
            'Ministry of Defence',
            'Ministry of State',
            'Ministry of Finance, Economic Planning and Decentralisation',
            'Ministry of Agriculture, Irrigation and Water Development',
            'Ministry of Education, Science and Technology',
            'Ministry of Justice and Constitutional Affairs',
            'Ministry of Foreign Affairs and International Cooperation',
            'Ministry of Health and Sanitation',
            'Ministry of Local Government and Rural Development',
            'Ministry of Industrialisation, Business, Trade and Tourism',
            'Ministry of Transport and Public Works',
            'Ministry of Homeland Security',
            'Ministry of Gender, Children, Disability and Social Welfare',
            'Ministry of Natural Resources, Energy and Mining',
            'Ministry of Lands, Housing and Urban Development',
            'Ministry of Labour, Skills and Innovation',
            'Ministry of Youth, Sports and Culture',
            'Ministry of Information and Digitalization',
        ];

        foreach ($ministries as $name) {
            DB::table('ministries')->updateOrInsert(
                ['name' => $name],
                [
                    'uuid' => Str::uuid()->toString(),
                    'code' => $this->codeFor($name),
                    'description' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function codeFor(string $name): string
    {
        $codes = [
            'Ministry of Defence' => 'MOD',
            'Ministry of State' => 'MOS',
            'Ministry of Finance, Economic Planning and Decentralisation' => 'MOF',
            'Ministry of Agriculture, Irrigation and Water Development' => 'MOA',
            'Ministry of Education, Science and Technology' => 'MOEST',
            'Ministry of Justice and Constitutional Affairs' => 'MOJ',
            'Ministry of Foreign Affairs and International Cooperation' => 'MOFA',
            'Ministry of Health and Sanitation' => 'MOH',
            'Ministry of Local Government and Rural Development' => 'MOLGRD',
            'Ministry of Industrialisation, Business, Trade and Tourism' => 'MOIBTT',
            'Ministry of Transport and Public Works' => 'MOTPW',
            'Ministry of Homeland Security' => 'MOHS',
            'Ministry of Gender, Children, Disability and Social Welfare' => 'MOGCDSW',
            'Ministry of Natural Resources, Energy and Mining' => 'MONREM',
            'Ministry of Lands, Housing and Urban Development' => 'MOLHUD',
            'Ministry of Labour, Skills and Innovation' => 'MOLSI',
            'Ministry of Youth, Sports and Culture' => 'MOYSC',
            'Ministry of Information and Digitalization' => 'MOID',
        ];

        return $codes[$name] ?? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 6));
    }
}
