<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\TrackedActivity;
use App\Models\WeeklyUpdate;

class ActivitySyncService
{
    /**
     * Sync activities from an approved weekly update into the tracked activities table.
     * Matches activities by normalized text hash within the same division.
     */
    public function syncFromWeeklyUpdate(WeeklyUpdate $weeklyUpdate): int
    {
        $weeklyUpdate->loadMissing('activities');
        $divisionId = $weeklyUpdate->division_id;
        $reportDate = $weeklyUpdate->week_end;
        $synced = 0;

        foreach ($weeklyUpdate->activities as $updateActivity) {
            $hash = TrackedActivity::generateHash($updateActivity->activity);

            $existing = TrackedActivity::where('division_id', $divisionId)
                ->where('activity_hash', $hash)
                ->first();

            if ($existing) {
                $statusChanged = $existing->current_status !== $updateActivity->status_flag;

                $existing->update([
                    'current_status' => $updateActivity->status_flag,
                    'responsible_persons' => $updateActivity->responsible_persons ?? $existing->responsible_persons,
                    'status_comment' => $updateActivity->status_comment ?? $existing->status_comment,
                    'challenges' => $updateActivity->challenges ?? $existing->challenges,
                    'last_reported_at' => $reportDate,
                    'times_reported' => $existing->times_reported + 1,
                    'weeks_unchanged' => $statusChanged ? 1 : $existing->weeks_unchanged + 1,
                    'latest_update_activity_id' => $updateActivity->id,
                    'latest_weekly_update_id' => $weeklyUpdate->id,
                    'activity_text' => $updateActivity->activity, // keep latest text
                ]);
            } else {
                TrackedActivity::create([
                    'division_id' => $divisionId,
                    'activity_hash' => $hash,
                    'activity_text' => $updateActivity->activity,
                    'current_status' => $updateActivity->status_flag,
                    'responsible_persons' => $updateActivity->responsible_persons,
                    'status_comment' => $updateActivity->status_comment,
                    'challenges' => $updateActivity->challenges,
                    'first_reported_at' => $reportDate,
                    'last_reported_at' => $reportDate,
                    'times_reported' => 1,
                    'weeks_unchanged' => 1,
                    'source_type' => 'update',
                    'latest_update_activity_id' => $updateActivity->id,
                    'latest_weekly_update_id' => $weeklyUpdate->id,
                ]);
            }

            $synced++;
        }

        // Run stale & repeat detection after sync
        $this->detectFlags($divisionId);

        return $synced;
    }

    /**
     * Run stale and repeat detection for a division (or all divisions).
     */
    public function detectFlags(?int $divisionId = null): void
    {
        $staleEnabled = SystemSetting::getValue('stale_detection_enabled', true);
        $repeatEnabled = SystemSetting::getValue('repeat_detection_enabled', true);
        $staleWeeks = SystemSetting::getValue('stale_activity_weeks', 3);
        $repeatThreshold = SystemSetting::getValue('repeat_threshold', 2);

        $query = TrackedActivity::query();
        if ($divisionId) {
            $query->where('division_id', $divisionId);
        }

        $query->chunk(200, function ($activities) use ($staleEnabled, $repeatEnabled, $staleWeeks, $repeatThreshold) {
            foreach ($activities as $activity) {
                $updates = [];

                // Stale detection: non-completed activities sitting unchanged too long
                if ($staleEnabled) {
                    $isStale = !in_array($activity->current_status, ['completed', 'na'])
                        && $activity->weeks_unchanged >= $staleWeeks;
                    $updates['is_stale'] = $isStale;
                }

                // Repeat detection: activity reported many times
                if ($repeatEnabled) {
                    $isRepeated = $activity->times_reported >= $repeatThreshold
                        && !in_array($activity->current_status, ['completed']);
                    $updates['is_repeated'] = $isRepeated;
                }

                if (!empty($updates)) {
                    $activity->update($updates);
                }
            }
        });
    }

    /**
     * Re-run detection across all divisions (can be called from admin or scheduled).
     */
    public function redetectAll(): void
    {
        $this->detectFlags(null);
    }
}
