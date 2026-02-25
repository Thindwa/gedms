<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class ShareLink extends Model
{
    public const PERMISSION_VIEW = 'view';
    public const PERMISSION_EDIT = 'edit';

    protected $fillable = ['shareable_type', 'shareable_id', 'token', 'permission', 'created_by_user_id'];

    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public static function generateToken(): string
    {
        return Str::random(32);
    }
}
