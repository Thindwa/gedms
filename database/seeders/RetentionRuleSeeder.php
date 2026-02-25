<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use App\Models\RetentionRule;
use Illuminate\Database\Seeder;

class RetentionRuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DocumentType::all() as $type) {
            RetentionRule::firstOrCreate(
                ['document_type_id' => $type->id],
                [
                    'retention_years' => 7,
                    'action' => RetentionRule::ACTION_ARCHIVE,
                    'disposal_requires_approval' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}
