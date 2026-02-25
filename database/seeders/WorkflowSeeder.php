<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowStep;
use Illuminate\Database\Seeder;

/**
 * Default workflows aligned with role hierarchy.
 * Chief Officer (unit) -> Director (department) -> Minister (ministry/cross-departmental).
 */
class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $policyType = DocumentType::where('code', 'POLICY')->first();
        if (!$policyType) {
            return;
        }

        $workflow = WorkflowDefinition::firstOrCreate(
            ['document_type_id' => $policyType->id],
            [
                'name' => 'Policy Approval',
                'description' => 'Chief Officer (unit) then Director (department) approval. Add Minister step for cross-departmental policies.',
                'is_active' => true,
            ]
        );

        WorkflowStep::updateOrCreate(
            ['workflow_definition_id' => $workflow->id, 'step_order' => 1],
            ['name' => 'Chief Officer Review', 'role_name' => 'Chief Officer', 'is_parallel' => false]
        );
        WorkflowStep::updateOrCreate(
            ['workflow_definition_id' => $workflow->id, 'step_order' => 2],
            ['name' => 'Director Approval', 'role_name' => 'Director', 'is_parallel' => false]
        );

        $policyType->update(['workflow_definition_id' => $workflow->id]);
    }
}
