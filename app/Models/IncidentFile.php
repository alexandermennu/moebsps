<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['incident_id', 'uploaded_by', 'file_name', 'file_path', 'file_type', 'file_size', 'category', 'description'])]
class IncidentFile extends Model
{
    use HasFactory;

    protected $table = 'incident_files';

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
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

    public function getFileUrl(): ?string
    {
        try {
            $disk = config('filesystems.uploads', 'public');
            $storage = Storage::disk($disk);

            // Check if file exists first
            if (!$storage->exists($this->file_path)) {
                return null;
            }

            // Use signed temporary URLs for S3 (private buckets)
            if (config("filesystems.disks.{$disk}.driver") === 's3') {
                return $storage->temporaryUrl($this->file_path, now()->addHour());
            }

            return $storage->url($this->file_path);
        } catch (\Exception $e) {
            // Log error but don't crash the page
            \Log::warning('Failed to generate file URL', [
                'file_id' => $this->id,
                'file_path' => $this->file_path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function getCategoryLabelAttribute(): string
    {
        return Incident::FILE_CATEGORIES[$this->category] ?? ucfirst($this->category);
    }
}
