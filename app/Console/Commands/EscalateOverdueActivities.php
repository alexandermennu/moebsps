<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\BureauNotification;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EscalateOverdueActivities extends Command
{
    protected $signature = 'activities:escalate';
    protected $description = 'Escalate overdue activities to Bureau Head and Minister';

    public function handle(): int
    {
        $today = Carbon::today();
        $escalationDays = (int) SystemSetting::getValue('escalation_days', 3);
        $ministerDays = (int) SystemSetting::getValue('minister_escalation_days', 7);

        $escalatedCount = 0;

        // Escalate to Bureau Head
        $toEscalate = Activity::where('is_overdue', true)
            ->where('is_escalated', false)
            ->whereNotNull('due_date')
            ->whereRaw('JULIANDAY(?) - JULIANDAY(due_date) >= ?', [$today, $escalationDays])
            ->get();

        $bureauHeads = User::where('role', User::ROLE_BUREAU_HEAD)->where('is_active', true)->get();

        foreach ($toEscalate as $activity) {
            $activity->update([
                'is_escalated' => true,
                'escalated_to' => 'bureau_head',
                'escalated_at' => now(),
            ]);

            foreach ($bureauHeads as $head) {
                BureauNotification::send(
                    $head->id,
                    'escalation',
                    'Escalated Activity: ' . $activity->title,
                    'Activity "' . $activity->title . '" from ' . ($activity->division?->name ?? 'Unknown Division') . ' has been overdue for ' . $escalationDays . '+ days.',
                    route('activities.show', $activity)
                );
            }

            $escalatedCount++;
        }

        // Escalate to Minister
        $toMinister = Activity::where('is_overdue', true)
            ->where('is_escalated', true)
            ->where('escalated_to', 'bureau_head')
            ->whereNotNull('due_date')
            ->whereRaw('JULIANDAY(?) - JULIANDAY(due_date) >= ?', [$today, $ministerDays])
            ->get();

        $ministers = User::where('role', User::ROLE_MINISTER)->where('is_active', true)->get();

        foreach ($toMinister as $activity) {
            $activity->update([
                'escalated_to' => 'minister',
                'escalated_at' => now(),
            ]);

            foreach ($ministers as $minister) {
                BureauNotification::send(
                    $minister->id,
                    'escalation',
                    'Minister Escalation: ' . $activity->title,
                    'Activity "' . $activity->title . '" from ' . ($activity->division?->name ?? 'Unknown Division') . ' has been overdue for ' . $ministerDays . '+ days and requires your attention.',
                    route('activities.show', $activity)
                );
            }
        }

        $this->info("Escalated {$escalatedCount} activities to Bureau Head.");
        $this->info("Escalated " . $toMinister->count() . " activities to Minister.");

        return Command::SUCCESS;
    }
}
