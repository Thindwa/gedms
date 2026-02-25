<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'file_id',
        'document_type_id',
        'ministry_id',
        'department_id',
        'unit_id',
        'owner_id',
        'sensitivity_level_id',
        'title',
        'status',
        'requires_workflow',
        'checked_out_by',
        'checked_out_at',
        'current_version',
        'legal_hold',
    ];

    protected $casts = [
        'checked_out_at' => 'datetime',
        'legal_hold' => 'boolean',
        'requires_workflow' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Document $model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function sensitivityLevel(): BelongsTo
    {
        return $this->belongsTo(SensitivityLevel::class);
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderByDesc('version');
    }

    public function workflowInstances(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class)->orderByDesc('created_at');
    }

    public function activeWorkflow(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WorkflowInstance::class)->where('status', WorkflowInstance::STATUS_IN_PROGRESS);
    }

    public function isCheckedOut(): bool
    {
        return $this->checked_out_by !== null;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canEdit(User $user): bool
    {
        if ($this->isApproved()) {
            return false;
        }
        if ($this->isCheckedOut() && $this->checked_out_by !== $user->id) {
            return false;
        }
        return true;
    }
}
