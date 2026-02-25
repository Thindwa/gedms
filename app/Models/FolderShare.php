<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FolderShare extends Model
{
    public const PERMISSION_VIEW = 'view';
    public const PERMISSION_EDIT = 'edit';

    protected $fillable = ['folder_id', 'shared_with_user_id', 'shared_by_user_id', 'permission'];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function sharedWith(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

    public function canEdit(): bool
    {
        return $this->permission === self::PERMISSION_EDIT;
    }
}
