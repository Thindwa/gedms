<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowInstance extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = ['document_id', 'workflow_definition_id', 'status', 'current_step_order', 'started_at', 'completed_at'];

    protected $casts = ['started_at' => 'datetime', 'completed_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (WorkflowInstance $model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function workflowDefinition(): BelongsTo
    {
        return $this->belongsTo(WorkflowDefinition::class);
    }

    public function stepInstances(): HasMany
    {
        return $this->hasMany(WorkflowStepInstance::class, 'workflow_instance_id');
    }
}
