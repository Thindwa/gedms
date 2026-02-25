<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStep extends Model
{
    protected $fillable = ['workflow_definition_id', 'step_order', 'name', 'role_name', 'is_parallel'];

    protected $casts = ['is_parallel' => 'boolean'];

    public function workflowDefinition(): BelongsTo
    {
        return $this->belongsTo(WorkflowDefinition::class);
    }

    public function stepInstances(): HasMany
    {
        return $this->hasMany(WorkflowStepInstance::class, 'workflow_step_id');
    }
}
