<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $fillable = ['name', 'code', 'description', 'ministry_id', 'workflow_definition_id', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function workflowDefinition(): BelongsTo
    {
        return $this->belongsTo(WorkflowDefinition::class);
    }

    public function retentionRule(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RetentionRule::class)->where('is_active', true);
    }

    public function retentionRules(): HasMany
    {
        return $this->hasMany(RetentionRule::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
