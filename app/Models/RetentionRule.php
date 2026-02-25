<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetentionRule extends Model
{
    public const ACTION_ARCHIVE = 'archive';
    public const ACTION_DISPOSE = 'dispose';

    protected $fillable = ['document_type_id', 'retention_years', 'action', 'disposal_requires_approval', 'is_active'];

    protected $casts = ['disposal_requires_approval' => 'boolean', 'is_active' => 'boolean'];

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}
