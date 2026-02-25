<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use SoftDeletes;

    protected $fillable = ['storage_space_id', 'parent_id', 'name', 'created_by', 'locked_by', 'locked_at', 'is_mandatory'];

    protected $casts = [
        'locked_at' => 'datetime',
        'is_mandatory' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Folder $model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    public function storageSpace(): BelongsTo
    {
        return $this->belongsTo(StorageSpace::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(FolderShare::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_by !== null;
    }

    public function getPathAttribute(): string
    {
        $parts = [];
        $folder = $this;
        while ($folder) {
            array_unshift($parts, $folder->name);
            $folder = $folder->parent;
        }
        return implode('/', $parts);
    }
}
