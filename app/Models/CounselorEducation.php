<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'institution',
    'program',
    'degree_level',
    'year_started',
    'year_graduated',
    'country',
    'notes',
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
}
