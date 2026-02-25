<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = ['department_id', 'name', 'mandatory_folders'];

    protected $casts = [
        'mandatory_folders' => 'array',
    ];

    /** @return array<string> */
    public function getMandatoryFolderNames(): array
    {
        $raw = $this->mandatory_folders;
        if (! is_array($raw)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', $raw)));
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function storageSpace(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StorageSpace::class, 'owner_section_id')
            ->where('type', StorageSpace::TYPE_SECTION);
    }
}
