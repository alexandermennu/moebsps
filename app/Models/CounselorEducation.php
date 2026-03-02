<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'institution',
    'program',
    'degree_level',
    'year_started',
    'year_graduated',
    'year_obtained',
    'country',
    'notes',
    'document_path',
    'document_name',
    'document_type',
    'document_size',
])]
class CounselorEducation extends Model
{
    protected $table = 'counselor_education';

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ──────────────────────────────────────────

    public function getDegreeLevelLabelAttribute(): string
    {
        return User::COUNSELOR_QUALIFICATIONS[$this->degree_level] ?? ucfirst($this->degree_level ?? '—');
    }

    public function getYearRangeAttribute(): string
    {
        if ($this->year_started && $this->year_graduated) {
            return $this->year_started . ' – ' . $this->year_graduated;
        }
        if ($this->year_graduated) {
            return 'Graduated ' . $this->year_graduated;
        }
        if ($this->year_started) {
            return 'Started ' . $this->year_started;
        }
        return '—';
    }

    /**
     * Human-readable document file size.
     */
    public function getDocumentSizeFormattedAttribute(): string
    {
        if (!$this->document_size) {
            return '';
        }
        $bytes = $this->document_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 0) . ' KB';
    }

    // ── Document Helpers ───────────────────────────────────

    public function hasDocument(): bool
    {
        return !empty($this->document_path);
    }

    public function getDocumentUrl(): ?string
    {
        if (!$this->hasDocument()) {
            return null;
        }
        $disk = Storage::disk(config('filesystems.uploads', 'public'));
        if (method_exists($disk, 'temporaryUrl')) {
            try {
                return $disk->temporaryUrl($this->document_path, now()->addMinutes(30));
            } catch (\RuntimeException $e) {
                // fallback
            }
        }
        return $disk->url($this->document_path);
    }

    public function deleteDocument(): void
    {
        if ($this->hasDocument()) {
            Storage::disk(config('filesystems.uploads', 'public'))->delete($this->document_path);
            $this->update([
                'document_path' => null,
                'document_name' => null,
                'document_type' => null,
                'document_size' => null,
            ]);
        }
    }
}
