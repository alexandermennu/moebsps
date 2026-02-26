<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\BureauNotification;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueActivities extends Command
{
    protected $signature = 'activities:check-overdue';
    protected $description = 'Check for overdue activities and flag them';

    public function handle(): int
    {
        $today = Carbon::today();

        $overdueActivities = Activity::where('status', '!=', 'completed')
            ->where('is_overdue', false)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->get();

        $count = 0;

        foreach ($overdueActivities as $activity) {
            $activity->update([
                'is_overdue' => true,
                'status' => 'overdue',
            ]);

            // Notify the assignee
            if ($activity->assigned_to) {
                BureauNotification::send(
                    $activity->assigned_to,
                    'overdue',
                    'Activity Overdue: ' . $activity->title,
                    'The activity "' . $activity->title . '" is past its due date of ' . $activity->due_date->format('M d, Y') . '.',
                    route('activities.show', $activity)
                );
            }

            // Notify the creator
            if ($activity->created_by && $activity->created_by !== $activity->assigned_to) {
                BureauNotification::send(
                    $activity->created_by,
                    'overdue',
                    'Activity Overdue: ' . $activity->title,
                    'An activity you created "' . $activity->title . '" is now overdue.',
                    route('activities.show', $activity)
                );
            }

            $count++;
        }

        $this->info("Flagged {$count} activities as overdue.");

        return Command::SUCCESS;
    }
}
