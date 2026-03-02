<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'certificate_name',
    'institution',
    'program',
    'year_obtained',
    'certificate_number',
    'expiry_date',
    'description',
    'document_path',
    'document_name',
    'document_type',
    'document_size',
])]
class CounselorCertificate extends Model
{
    protected $table = 'counselor_certificates';

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ──────────────────────────────────────────

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->expiry_date) {
            return 'No Expiry';
        }
        return $this->is_expired ? 'Expired' : 'Valid';
    }

    // ── Document Helpers ───────────────────────────────────

    public function hasDocument(): bool
    {
        return !empty($this->document_path);
    }

    public function getDocumentUrl(): ?string
    {
        if (!$this->document_path) {
            return null;
        }

        $disk = config('filesystems.uploads', 'public');
        $storage = Storage::disk($disk);

        if (config("filesystems.disks.{$disk}.driver") === 's3') {
            return $storage->temporaryUrl($this->document_path, now()->addHour());
        }

        return $storage->url($this->document_path);
    }

    public function deleteDocument(): void
    {
        if ($this->document_path) {
            Storage::disk(config('filesystems.uploads', 'public'))->delete($this->document_path);
            $this->update([
                'document_path' => null,
                'document_name' => null,
                'document_type' => null,
                'document_size' => null,
            ]);
        }
    }

    public function getDocumentSizeFormattedAttribute(): string
    {
        if (!$this->document_size) {
            return '—';
        }

        if ($this->document_size < 1024) {
            return $this->document_size . ' B';
        } elseif ($this->document_size < 1048576) {
            return round($this->document_size / 1024, 1) . ' KB';
        } else {
            return round($this->document_size / 1048576, 1) . ' MB';
        }
    }
}
