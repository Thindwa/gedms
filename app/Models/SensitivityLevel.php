<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SensitivityLevel extends Model
{
    public const CODE_PUBLIC = 'public';
    public const CODE_INTERNAL = 'internal';
    public const CODE_RESTRICTED = 'restricted';
    public const CODE_CONFIDENTIAL = 'confidential';

    protected $fillable = ['name', 'code', 'sort_order'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
