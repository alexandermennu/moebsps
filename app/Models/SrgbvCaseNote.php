<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['srgbv_case_id', 'user_id', 'note', 'note_type', 'is_private'])]
class SrgbvCaseNote extends Model
{
    use HasFactory;

    protected $table = 'srgbv_case_notes';

    const NOTE_TYPES = [
        'progress_update' => 'Progress Update',
        'follow_up' => 'Follow-up',
        'referral' => 'Referral',
        'action_taken' => 'Action Taken',
        'assessment' => 'Assessment',
        'counseling_session' => 'Counseling Session',
        'other' => 'Other',
    ];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
        ];
    }

    public function srgbvCase(): BelongsTo
    {
        return $this->belongsTo(SrgbvCase::class);
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
