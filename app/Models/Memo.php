<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memo extends Model
{
    use SoftDeletes;

    public const DIRECTION_UPWARD = 'upward';
    public const DIRECTION_DOWNWARD = 'downward';
    public const DIRECTION_PERSONAL = 'personal';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_ACKNOWLEDGED = 'acknowledged';
    public const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'direction', 'title', 'body', 'from_user_id', 'to_user_id',
        'ministry_id', 'department_id', 'file_id', 'requires_approval',
        'status', 'sent_at', 'acknowledged_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Memo $model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    protected $casts = [
        'requires_approval' => 'boolean',
        'sent_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
