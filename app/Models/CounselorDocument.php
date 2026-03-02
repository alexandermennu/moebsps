<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['user_id', 'document_type', 'title', 'file_name', 'file_path', 'file_type', 'file_size'])]
class CounselorDocument extends Model
{
    use HasFactory;

    // ── Document Type Constants ─────────────────────────────
    const TYPE_CERTIFICATE    = 'certificate';
    const TYPE_CREDENTIAL     = 'credential';
    const TYPE_TRAINING_CERT  = 'training_cert';
    const TYPE_OTHER          = 'other';

    const DOCUMENT_TYPES = [
        self::TYPE_CERTIFICATE   => 'Academic Certificate',
        self::TYPE_CREDENTIAL    => 'Professional Credential',
        self::TYPE_TRAINING_CERT => 'Training Certificate',
        self::TYPE_OTHER         => 'Other Document',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ──────────────────────────────────────────

    public function getDocumentTypeLabelAttribute(): string
    {
        return self::DOCUMENT_TYPES[$this->document_type] ?? ucfirst($this->document_type);
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

    public function getFileUrl(): string
    {
        $disk = config('filesystems.uploads', 'public');
        $storage = Storage::disk($disk);

        // Use signed temporary URLs for S3 (private buckets)
        if (config("filesystems.disks.{$disk}.driver") === 's3') {
            return $storage->temporaryUrl($this->file_path, now()->addHour());
        }

        return $storage->url($this->file_path);
    }

    public function isImage(): bool
    {
        return in_array($this->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }

    /**
     * Delete the document file from storage.
     */
    public function deleteFile(): void
    {
        Storage::disk(config('filesystems.uploads', 'public'))->delete($this->file_path);
    }
}
