<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['ministry_id', 'name', 'code', 'description', 'is_active', 'drive_style', 'mandatory_folders'];

    protected $casts = [
        'is_active' => 'boolean',
        'mandatory_folders' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Department $model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function storageSpace(): HasOne
    {
        return $this->hasOne(StorageSpace::class, 'owner_department_id')
            ->where('type', StorageSpace::TYPE_DEPARTMENT);
    }
}
