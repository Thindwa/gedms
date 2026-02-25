<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Policy', 'code' => 'POLICY', 'description' => 'Policy documents'],
            ['name' => 'Procedure', 'code' => 'PROCEDURE', 'description' => 'Standard operating procedures'],
            ['name' => 'Report', 'code' => 'REPORT', 'description' => 'Official reports'],
            ['name' => 'Correspondence', 'code' => 'CORRESPONDENCE', 'description' => 'Official correspondence'],
            ['name' => 'Contract', 'code' => 'CONTRACT', 'description' => 'Contracts and agreements'],
        ];

        foreach ($types as $type) {
            DocumentType::updateOrCreate(
                ['code' => $type['code']],
                array_merge($type, ['ministry_id' => null, 'is_active' => true])
            );
        }
    }
}
