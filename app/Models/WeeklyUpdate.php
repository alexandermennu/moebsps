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
    'accomplishments', 'challenges', 'support_needed', 'key_metrics',
    'status', 'reviewed_by', 'reviewed_at', 'review_comments'
])]
class WeeklyUpdate extends Model
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
        
        // Calculate which week of the month this is
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
        $firstOfMonth = $date->copy()->startOfMonth();
        $firstMonday = $firstOfMonth->copy();
        
        // Find the first Monday of the month
        if ($firstMonday->dayOfWeek !== Carbon::MONDAY) {
            $firstMonday = $firstMonday->next(Carbon::MONDAY);
        }
        
        // If the date is before the first Monday, it's week 1
        if ($date->lt($firstMonday)) {
            return 1;
        }
        
        // Calculate the week number
        $weekNumber = (int) ceil(($date->day - $firstMonday->day + $firstMonday->dayOfWeek) / 7) + 1;
        
        // Simpler calculation: which Monday is this?
        $dayOfMonth = $date->day;
        $weekNumber = (int) ceil($dayOfMonth / 7);
        
        // Ensure we have at least week 1
        return max(1, min($weekNumber, 5));
    }

    /**
     * Get the working days range (Mon-Fri) as a formatted string
     */
    public function getWorkingDaysRangeAttribute(): string
    {
        if (!$this->week_start || !$this->week_end) {
            return '';
        }

        return $this->week_start->format('M d') . ' – ' . $this->week_end->format('d, Y');
    }

    /**
     * Get working days for a given week (returns Monday to Friday)
     */
    public static function getWorkingDaysForWeek(Carbon $anyDateInWeek): array
    {
        $monday = $anyDateInWeek->copy()->startOfWeek(Carbon::MONDAY);
        $friday = $monday->copy()->addDays(4); // Friday is 4 days after Monday
        
        return [
            'start' => $monday,
            'end' => $friday,
        ];
    }

    /**
     * Get all weeks in a month with their working day ranges
     */
    public static function getWeeksInMonth(int $year, int $month): array
    {
        $weeks = [];
        $date = Carbon::create($year, $month, 1);
        $endOfMonth = $date->copy()->endOfMonth();
        
        // Start from the first Monday
        $currentMonday = $date->copy()->startOfWeek(Carbon::MONDAY);
        
        // If first Monday is in previous month, move to next Monday
        if ($currentMonday->month < $month) {
            $currentMonday->addWeek();
        }
        
        $weekNum = 1;
        while ($currentMonday->month == $month && $currentMonday->lte($endOfMonth)) {
            $friday = $currentMonday->copy()->addDays(4);
            
            $weeks[] = [
                'number' => $weekNum,
                'label' => $date->format('F') . " Week {$weekNum}, {$year}",
                'label_short' => $date->format('M') . " Week {$weekNum}",
                'start' => $currentMonday->copy(),
                'end' => $friday,
                'start_formatted' => $currentMonday->format('Y-m-d'),
                'end_formatted' => $friday->format('Y-m-d'),
            ];
            
            $currentMonday->addWeek();
            $weekNum++;
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
        return $this->hasMany(UpdateActivity::class)->orderBy('sort_order');
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
