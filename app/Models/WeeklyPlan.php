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
     * When a week spans two months, use the month where Friday falls
     */
    public function getWeekLabelAttribute(): string
    {
        if (!$this->week_start) {
            return 'Unknown Week';
        }

        // Use Friday (end of work week) to determine the month
        $friday = $this->week_end ?? $this->week_start->copy()->addDays(4);
        $month = $friday->format('F');
        $year = $friday->format('Y');
        $weekOfMonth = self::getWeekOfMonthForDate($friday);
        
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

        $friday = $this->week_end ?? $this->week_start->copy()->addDays(4);
        $month = $friday->format('M');
        $weekOfMonth = self::getWeekOfMonthForDate($friday);
        
        return "{$month} Week {$weekOfMonth}";
    }

    /**
     * Calculate which week of the month a date falls in (1-4)
     * Based on which Monday of the month the week starts on
     */
    public static function getWeekOfMonthForDate(Carbon $date): int
    {
        // Find the Monday of this week
        $monday = $date->copy()->startOfWeek(Carbon::MONDAY);
        
        // If Monday is in a different month than the date, this is Week 1 of the new month
        if ($monday->month !== $date->month) {
            return 1;
        }
        
        // Count which Monday of the month this is
        $firstDayOfMonth = $monday->copy()->startOfMonth();
        $firstMonday = $firstDayOfMonth->copy();
        
        // Find the first Monday of the month
        if ($firstMonday->dayOfWeek !== Carbon::MONDAY) {
            $firstMonday = $firstMonday->next(Carbon::MONDAY);
        }
        
        // Calculate the week number (1-based)
        $weekNumber = (int) floor($monday->diffInWeeks($firstMonday)) + 1;
        
        return max(1, min($weekNumber, 4)); // Cap at 4 weeks
    }

    /**
     * Get upcoming weeks for planning (next 4-6 weeks)
     */
    public static function getUpcomingWeeks(): array
    {
        $weeks = [];
        $today = now();
        
        // Start from the current week's Monday if today is a weekday,
        // or next Monday if today is weekend
        $currentMonday = $today->copy();
        if ($currentMonday->dayOfWeek === Carbon::SATURDAY) {
            $currentMonday->addDays(2); // Saturday -> next Monday
        } elseif ($currentMonday->dayOfWeek === Carbon::SUNDAY) {
            $currentMonday->addDay(); // Sunday -> next Monday
        } else {
            // It's a weekday - get THIS week's Monday
            $currentMonday = $currentMonday->startOfWeek(Carbon::MONDAY);
        }
        
        // Generate 6 weeks of options starting from current/next Monday
        for ($i = 0; $i < 6; $i++) {
            $monday = $currentMonday->copy()->addWeeks($i);
            $friday = $monday->copy()->addDays(4);
            
            // Use Friday to determine the month (when week spans two months)
            $month = $friday->format('F');
            $year = $friday->format('Y');
            $weekOfMonth = self::getWeekOfMonthForDate($friday);
            
            // Check if this is the current week (contains today)
            $isCurrentWeek = $today->between($monday, $friday->copy()->endOfDay());
            
            $weeks[] = [
                'number' => $weekOfMonth,
                'label' => "{$month} Week {$weekOfMonth}, {$year}",
                'label_short' => "{$friday->format('M')} Week {$weekOfMonth}",
                'start' => $monday,
                'end' => $friday,
                'start_formatted' => $monday->format('Y-m-d'),
                'end_formatted' => $friday->format('Y-m-d'),
                'is_next_week' => $i === 0, // First option is always the default
                'is_current_week' => $isCurrentWeek,
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
