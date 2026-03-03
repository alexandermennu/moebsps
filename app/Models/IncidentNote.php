<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['incident_id', 'user_id', 'note', 'note_type', 'is_private'])]
class IncidentNote extends Model
{
    use HasFactory;

    protected $table = 'incident_notes';

    const NOTE_TYPES = [
        'progress_update' => 'Progress Update',
        'follow_up' => 'Follow-up',
        'referral' => 'Referral',
        'action_taken' => 'Action Taken',
        'assessment' => 'Assessment',
        'counseling_session' => 'Counseling Session',
        'investigation' => 'Investigation Note',
        'status_change' => 'Status Change',
        'other' => 'Other',
    ];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
        ];
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNoteTypeLabelAttribute(): string
    {
        return self::NOTE_TYPES[$this->note_type] ?? ucfirst($this->note_type);
    }
}
