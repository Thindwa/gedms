<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FileVersion extends Model
{
    protected $fillable = ['file_id', 'created_by', 'version', 'storage_path', 'mime_type', 'size'];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStoragePath(): string
    {
        return $this->storage_path;
    }

    public function exists(): bool
    {
        return Storage::disk('edms')->exists($this->storage_path);
    }
}
