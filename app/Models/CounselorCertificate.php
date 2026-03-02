<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'certificate_name',
    'institution',
    'program',
    'year_obtained',
    'certificate_number',
    'expiry_date',
    'description',
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
}
