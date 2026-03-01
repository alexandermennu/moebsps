<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['srgbv_case_id', 'uploaded_by', 'file_name', 'file_path', 'file_type', 'file_size', 'category', 'description'])]
class SrgbvCaseFile extends Model
{
    use HasFactory;

    protected $table = 'srgbv_case_files';

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function srgbvCase(): BelongsTo
    {
        return $this->belongsTo(SrgbvCase::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function isImage(): bool
    {
        return in_array($this->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function getFileUrl(): string
    {
        return Storage::disk(config('filesystems.uploads', 'public'))->url($this->file_path);
    }

    public function getCategoryLabelAttribute(): string
    {
        return SrgbvCase::FILE_CATEGORIES[$this->category] ?? ucfirst($this->category);
    }
}
