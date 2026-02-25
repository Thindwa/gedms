<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Immutable audit log. Never updated or soft-deleted.
 */
class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'action',
        'subject_type',
        'subject_id',
        'user_id',
        'user_email',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'metadata',
        'ministry_id_scope',
        'department_id_scope',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AuditLog $model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
