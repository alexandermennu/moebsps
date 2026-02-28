<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'division_id', 'activity_hash', 'activity_text', 'current_status',
    'responsible_persons', 'status_comment', 'challenges',
    'first_reported_at', 'last_reported_at', 'times_reported',
    'weeks_unchanged', 'is_stale', 'is_repeated', 'source_type',
    'latest_update_activity_id', 'latest_weekly_update_id',
    'latest_plan_activity_id', 'latest_weekly_plan_id',
])]
class TrackedActivity extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'first_reported_at' => 'date',
            'last_reported_at' => 'date',
            'is_stale' => 'boolean',
            'is_repeated' => 'boolean',
        ];
    }

    // ─── Relationships ───

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function latestUpdateActivity(): BelongsTo
    {
        return $this->belongsTo(UpdateActivity::class, 'latest_update_activity_id');
    }

    public function latestWeeklyUpdate(): BelongsTo
    {
        return $this->belongsTo(WeeklyUpdate::class, 'latest_weekly_update_id');
    }

    public function latestPlanActivity(): BelongsTo
    {
        return $this->belongsTo(PlanActivity::class, 'latest_plan_activity_id');
    }

    public function latestWeeklyPlan(): BelongsTo
    {
        return $this->belongsTo(WeeklyPlan::class, 'latest_weekly_plan_id');
    }

    // ─── Scopes ───

    public function scopeStale($query)
    {
        return $query->where('is_stale', true);
    }

    public function scopeRepeated($query)
    {
        return $query->where('is_repeated', true);
    }

    public function scopeFlagged($query)
    {
        return $query->where(function ($q) {
            $q->where('is_stale', true)->orWhere('is_repeated', true);
        });
    }

    public function scopeByDivision($query, $divisionId)
    {
        return $query->where('division_id', $divisionId);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('current_status', ['completed', 'na']);
    }

    // ─── Helpers ───

    /**
     * Generate a normalized hash for activity matching.
     * Lowercases, strips punctuation, collapses whitespace.
     */
    public static function generateHash(string $text): string
    {
        $normalized = mb_strtolower(trim($text));
        $normalized = preg_replace('/[^\p{L}\p{N}\s]/u', '', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return md5($normalized);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->current_status) {
            'not_started' => 'Not Started',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'na' => 'N/A',
            default => ucfirst($this->current_status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->current_status) {
            'not_started' => 'red',
            'ongoing' => 'yellow',
            'completed' => 'green',
            'na' => 'gray',
            default => 'gray',
        };
    }

    public function getWeeksActiveAttribute(): int
    {
        return $this->first_reported_at->diffInWeeks($this->last_reported_at) + 1;
    }
}
