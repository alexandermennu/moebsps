<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

#[Fillable([
    'division_id', 'submitted_by', 'week_start', 'week_end',
    'planned_activities', 'objectives', 'expected_outcomes', 'resources_needed',
    'status', 'reviewed_by', 'reviewed_at', 'review_comments'
])]
class WeeklyPlan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Get a friendly week label like "March Week 2, 2026"
     */
    public function getWeekLabelAttribute(): string
    {
        if (!$this->week_start) {
            return 'Unknown Week';
        }

        $date = $this->week_start;
        $month = $date->format('F');
        $year = $date->format('Y');
        $weekOfMonth = $this->getWeekOfMonth($date);
        
        return "{$month} Week {$weekOfMonth}, {$year}";
    }

    /**
     * Get a short week label like "Mar Week 2"
     */
    public function getWeekLabelShortAttribute(): string
    {
        if (!$this->week_start) {
            return 'Unknown';
        }

        $date = $this->week_start;
        $month = $date->format('M');
        $weekOfMonth = $this->getWeekOfMonth($date);
        
        return "{$month} Week {$weekOfMonth}";
    }

    /**
     * Calculate which week of the month a date falls in
     */
    private function getWeekOfMonth(Carbon $date): int
    {
        $dayOfMonth = $date->day;
        $weekNumber = (int) ceil($dayOfMonth / 7);
        return max(1, min($weekNumber, 5));
    }

    /**
     * Get upcoming weeks for planning (next 4-6 weeks)
     */
    public static function getUpcomingWeeks(): array
    {
        $weeks = [];
        $today = now();
        
        // Start from next Monday (or this Monday if today is weekend)
        $nextMonday = $today->copy();
        if ($nextMonday->dayOfWeek === Carbon::SATURDAY) {
            $nextMonday->addDays(2); // Saturday -> Monday
        } elseif ($nextMonday->dayOfWeek === Carbon::SUNDAY) {
            $nextMonday->addDay(); // Sunday -> Monday
        } else {
            // Weekday - go to next week's Monday
            $nextMonday = $nextMonday->next(Carbon::MONDAY);
        }
        
        // Generate 6 weeks of options
        for ($i = 0; $i < 6; $i++) {
            $monday = $nextMonday->copy()->addWeeks($i);
            $friday = $monday->copy()->addDays(4);
            
            $month = $monday->format('F');
            $year = $monday->format('Y');
            $weekOfMonth = (int) ceil($monday->day / 7);
            
            $weeks[] = [
                'number' => $weekOfMonth,
                'label' => "{$month} Week {$weekOfMonth}, {$year}",
                'label_short' => "{$monday->format('M')} Week {$weekOfMonth}",
                'start' => $monday,
                'end' => $friday,
                'start_formatted' => $monday->format('Y-m-d'),
                'end_formatted' => $friday->format('Y-m-d'),
                'is_next_week' => $i === 0,
            ];
        }
        
        return $weeks;
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(PlanActivity::class)->orderBy('sort_order');
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}
