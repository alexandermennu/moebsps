<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'weekly_update_id', 'sort_order', 'activity',
    'responsible_persons', 'status_flag', 'status_comment', 'challenges', 'track_this'
])]
class UpdateActivity extends Model
{
    protected function casts(): array
    {
        return [
            'track_this' => 'boolean',
        ];
    }

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NA = 'na';

    public const STATUSES = [
        self::STATUS_NOT_STARTED => 'Not Started',
        self::STATUS_ONGOING => 'Ongoing',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_NA => 'N/A',
    ];

    public const STATUS_COLORS = [
        self::STATUS_NOT_STARTED => 'red',
        self::STATUS_ONGOING => 'yellow',
        self::STATUS_COMPLETED => 'green',
        self::STATUS_NA => 'gray',
    ];

    public function weeklyUpdate(): BelongsTo
    {
        return $this->belongsTo(WeeklyUpdate::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(UpdateActivityComment::class, 'update_activity_id')->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status_flag] ?? ucfirst($this->status_flag);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status_flag] ?? 'gray';
    }
}
