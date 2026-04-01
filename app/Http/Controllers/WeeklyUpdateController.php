<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use App\Models\Division;
use App\Models\UpdateActivity;
use App\Models\UpdateActivityComment;
use App\Models\WeeklyUpdate;
use App\Services\ActivitySyncService;
use Illuminate\Http\Request;

class WeeklyUpdateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Check module access
        if (!$user->canAccessWeeklyUpdates()) {
            abort(403, 'You do not have access to weekly updates.');
        }

        // Weekly Updates are for the PREVIOUS week's activities
        $today = now();
        $thisWeekStart = $today->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        
        // The week we're reporting on (last week)
        $reportingWeekStart = $thisWeekStart->copy()->subWeek();
        $reportingWeekEnd = $reportingWeekStart->copy()->addDays(4); // Friday
        
        // Due date is end of Monday (first working day of current week)
        $dueDate = $thisWeekStart->copy()->endOfDay();

        // Get all divisions for tracking
        $divisionsQuery = Division::where('is_active', true);
        if ($user->isDivisionScoped()) {
            $divisionsQuery->where('id', $user->division_id);
        }
        $allDivisions = $divisionsQuery->orderBy('name')->get();

        // Updates for this reporting week
        $reportingWeekUpdates = WeeklyUpdate::with(['division', 'submitter', 'reviewer', 'activities'])
            ->where('week_start', $reportingWeekStart->toDateString())
            ->get();

        // Build detailed division status
        $divisionStatuses = $allDivisions->map(function ($division) use ($reportingWeekUpdates, $dueDate, $today) {
            $update = $reportingWeekUpdates->firstWhere('division_id', $division->id);
            
            // Determine submission status
            $statusInfo = $this->getSubmissionStatus($update, $dueDate, $today);
            
            // Count content - activities from the UpdateActivity table
            $activityCount = $update ? $update->activities->count() : 0;
            
            return (object) [
                'division' => $division,
                'update' => $update,
                'status' => $statusInfo['status'],
                'status_label' => $statusInfo['label'],
                'status_color' => $statusInfo['color'],
                'status_detail' => $statusInfo['detail'],
                'submission_details' => $statusInfo['submission_details'],
                'activity_count' => $activityCount,
                'has_content' => $activityCount > 0,
            ];
        });

        // Calculate summary stats
        $submittedCount = $divisionStatuses->filter(fn($s) => $s->update !== null)->count();
        $onTimeCount = $divisionStatuses->filter(fn($s) => $s->status === 'on_time')->count();
        $lateCount = $divisionStatuses->filter(fn($s) => $s->status === 'late')->count();
        $overdueCount = $divisionStatuses->filter(fn($s) => $s->status === 'overdue')->count();
        $pendingCount = $divisionStatuses->filter(fn($s) => $s->status === 'pending')->count();
        $notSubmittedCount = $divisionStatuses->filter(fn($s) => in_array($s->status, ['not_submitted', 'overdue', 'pending']))->count();

        // Previous weeks - updates from before this reporting week
        $previousWeeksQuery = WeeklyUpdate::with(['division', 'submitter'])
            ->where('week_start', '<', $reportingWeekStart->toDateString());

        if ($user->isDivisionScoped()) {
            $previousWeeksQuery->where('division_id', $user->division_id);
        }

        $previousUpdates = $previousWeeksQuery->orderBy('week_start', 'desc')->get();

        // Group previous updates by week
        $previousWeeksGrouped = $previousUpdates->groupBy(function ($update) {
            return $update->week_start->toDateString();
        })->map(function ($weekUpdates, $weekStart) use ($allDivisions) {
            $firstUpdate = $weekUpdates->first();
            $submittedCount = $weekUpdates->count();
            $totalDivisions = $allDivisions->count();
            return (object) [
                'week_start' => $firstUpdate->week_start,
                'week_end' => $firstUpdate->week_end,
                'week_label' => $firstUpdate->week_label,
                'week_label_short' => $firstUpdate->week_label_short,
                'updates' => $weekUpdates,
                'total_divisions' => $totalDivisions,
                'submitted_count' => $submittedCount,
                'approved_count' => $weekUpdates->where('status', 'approved')->count(),
                'is_complete' => $submittedCount >= $totalDivisions,
            ];
        })->take(12);

        // Generate week label
        $reportingWeekLabel = $this->getWeekLabel($reportingWeekStart);

        return view('weekly-updates.index', compact(
            'user', 
            'reportingWeekStart', 
            'reportingWeekEnd',
            'reportingWeekLabel',
            'dueDate',
            'divisionStatuses',
            'submittedCount',
            'onTimeCount',
            'lateCount',
            'overdueCount',
            'pendingCount',
            'notSubmittedCount',
            'previousWeeksGrouped',
            'allDivisions'
        ));
    }

    /**
     * Determine submission status for a division
     */
    private function getSubmissionStatus($update, $dueDate, $today)
    {
        if (!$update) {
            // Not submitted - check if overdue
            if ($today->gt($dueDate)) {
                $daysOverdue = $today->diffInDays($dueDate);
                return [
                    'status' => 'overdue',
                    'label' => 'Not Submitted',
                    'color' => 'red',
                    'detail' => $daysOverdue . ' ' . ($daysOverdue == 1 ? 'day' : 'days') . ' overdue',
                    'submission_details' => $daysOverdue . ' ' . ($daysOverdue == 1 ? 'day' : 'days') . ' overdue',
                ];
            } else if ($today->isSameDay($dueDate) || $today->lt($dueDate)) {
                return [
                    'status' => 'pending',
                    'label' => 'Pending',
                    'color' => 'orange',
                    'detail' => 'Due today',
                    'submission_details' => 'Due today',
                ];
            }
        }

        // Has been submitted - check if it was on time or late
        $submittedAt = $update->created_at;
        $wasLate = $submittedAt->gt($dueDate);
        
        if ($wasLate) {
            $daysLate = $submittedAt->startOfDay()->diffInDays($dueDate->startOfDay());
            return [
                'status' => 'late',
                'label' => 'Late',
                'color' => 'orange',
                'detail' => 'Submitted ' . $submittedAt->format('M d'),
                'submission_details' => 'Submitted ' . $submittedAt->format('M d') . ' (' . $daysLate . ' ' . ($daysLate == 1 ? 'day' : 'days') . ' late)',
            ];
        }

        return [
            'status' => 'on_time',
            'label' => 'On Time',
            'color' => 'green',
            'detail' => 'Submitted ' . $submittedAt->format('M d'),
            'submission_details' => 'Submitted ' . $submittedAt->format('M d'),
        ];
    }

    /**
     * Get a friendly week label like "March Week 2, 2026"
     */
    private function getWeekLabel($date): string
    {
        $month = $date->format('F');
        $year = $date->format('Y');
        
        // Calculate which week of the month this is
        $firstOfMonth = $date->copy()->startOfMonth();
        $firstMonday = $firstOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        if ($firstMonday->month != $date->month) {
            $firstMonday->addWeek();
        }
        $weekOfMonth = (int) ceil(($date->diffInDays($firstMonday) + 1) / 7) + 1;
        if ($weekOfMonth < 1) $weekOfMonth = 1;
        
        return "{$month} Week {$weekOfMonth}, {$year}";
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->canManageDivision()) {
            abort(403, 'You do not have permission to create weekly updates.');
        }

        return view('weekly-updates.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->canManageDivision()) {
            abort(403);
        }

        $validated = $request->validate([
            'week_start' => 'required|date',
            'week_end' => 'required|date|after:week_start',
            'accomplishments' => 'nullable|string',
            'challenges' => 'nullable|string',
            'support_needed' => 'nullable|string',
            'key_metrics' => 'nullable|string',
            'status' => 'in:draft,submitted',
            'activities' => 'required|array|min:1',
            'activities.*.activity' => 'required|string',
            'activities.*.responsible_persons' => 'nullable|string|max:255',
            'activities.*.status_flag' => 'required|in:not_started,ongoing,completed,na',
            'activities.*.status_comment' => 'nullable|string',
            'activities.*.challenges' => 'nullable|string',
            'activities.*.track_this' => 'nullable',
        ]);

        $update = WeeklyUpdate::create([
            'week_start' => $validated['week_start'],
            'week_end' => $validated['week_end'],
            'accomplishments' => $validated['accomplishments'] ?? '',
            'challenges' => $validated['challenges'] ?? null,
            'support_needed' => $validated['support_needed'] ?? null,
            'key_metrics' => $validated['key_metrics'] ?? null,
            'division_id' => $user->division_id,
            'submitted_by' => $user->id,
            'status' => $request->input('status', 'draft'),
        ]);

        foreach ($validated['activities'] as $index => $activityData) {
            $update->activities()->create([
                'sort_order' => $index + 1,
                'activity' => $activityData['activity'],
                'responsible_persons' => $activityData['responsible_persons'] ?? null,
                'status_flag' => $activityData['status_flag'],
                'status_comment' => $activityData['status_comment'] ?? null,
                'challenges' => $activityData['challenges'] ?? null,
                'track_this' => !empty($activityData['track_this']),
            ]);
        }

        if ($update->status === 'submitted') {
            $this->notifyBureauHead($update);
        }

        return redirect()->route('weekly-updates.index')
            ->with('success', 'Weekly update ' . ($update->status === 'submitted' ? 'submitted' : 'saved as draft') . ' successfully.');
    }

    public function show(WeeklyUpdate $weeklyUpdate)
    {
        $user = auth()->user();

        if ($user->isDivisionScoped() && $weeklyUpdate->division_id !== $user->division_id) {
            abort(403);
        }

        $weeklyUpdate->load(['division', 'submitter', 'reviewer', 'activities.comments.user']);

        return view('weekly-updates.show', compact('weeklyUpdate', 'user'));
    }

    public function edit(WeeklyUpdate $weeklyUpdate)
    {
        $user = auth()->user();

        if (!$user->canManageDivision() || $weeklyUpdate->submitted_by !== $user->id) {
            abort(403);
        }

        if (!in_array($weeklyUpdate->status, ['draft', 'rejected'])) {
            return redirect()->route('weekly-updates.show', $weeklyUpdate)
                ->with('error', 'Only draft or rejected updates can be edited.');
        }

        $weeklyUpdate->load('activities');

        return view('weekly-updates.edit', compact('weeklyUpdate', 'user'));
    }

    public function update(Request $request, WeeklyUpdate $weeklyUpdate)
    {
        $user = $request->user();

        if (!$user->canManageDivision() || $weeklyUpdate->submitted_by !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'week_start' => 'required|date',
            'week_end' => 'required|date|after:week_start',
            'accomplishments' => 'nullable|string',
            'challenges' => 'nullable|string',
            'support_needed' => 'nullable|string',
            'key_metrics' => 'nullable|string',
            'status' => 'in:draft,submitted',
            'activities' => 'required|array|min:1',
            'activities.*.activity' => 'required|string',
            'activities.*.responsible_persons' => 'nullable|string|max:255',
            'activities.*.status_flag' => 'required|in:not_started,ongoing,completed,na',
            'activities.*.status_comment' => 'nullable|string',
            'activities.*.challenges' => 'nullable|string',
            'activities.*.track_this' => 'nullable',
        ]);

        $weeklyUpdate->update([
            'week_start' => $validated['week_start'],
            'week_end' => $validated['week_end'],
            'accomplishments' => $validated['accomplishments'] ?? '',
            'challenges' => $validated['challenges'] ?? null,
            'support_needed' => $validated['support_needed'] ?? null,
            'key_metrics' => $validated['key_metrics'] ?? null,
            'status' => $validated['status'] ?? 'draft',
        ]);

        // Replace all activities
        $weeklyUpdate->activities()->delete();
        foreach ($validated['activities'] as $index => $activityData) {
            $weeklyUpdate->activities()->create([
                'sort_order' => $index + 1,
                'activity' => $activityData['activity'],
                'responsible_persons' => $activityData['responsible_persons'] ?? null,
                'status_flag' => $activityData['status_flag'],
                'status_comment' => $activityData['status_comment'] ?? null,
                'challenges' => $activityData['challenges'] ?? null,
                'track_this' => !empty($activityData['track_this']),
            ]);
        }

        if ($weeklyUpdate->status === 'submitted') {
            $this->notifyBureauHead($weeklyUpdate);
        }

        return redirect()->route('weekly-updates.show', $weeklyUpdate)
            ->with('success', 'Weekly update updated successfully.');
    }

    public function review(Request $request, WeeklyUpdate $weeklyUpdate)
    {
        $user = $request->user();

        if (!$user->canReviewSubmissions()) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:approved,rejected',
            'review_comments' => 'nullable|string',
        ]);

        $weeklyUpdate->update([
            'status' => $validated['action'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_comments' => $validated['review_comments'],
        ]);

        // Notify the submitter
        BureauNotification::send(
            $weeklyUpdate->submitted_by,
            $validated['action'] === 'approved' ? 'approval' : 'rejection',
            'Weekly Update ' . ucfirst($validated['action']),
            "Your weekly update for {$weeklyUpdate->week_start->format('M d')} - {$weeklyUpdate->week_end->format('M d, Y')} has been {$validated['action']}.",
            route('weekly-updates.show', $weeklyUpdate)
        );

        // If approved, sync activities to tracker and notify Minister
        if ($validated['action'] === 'approved') {
            app(ActivitySyncService::class)->syncFromWeeklyUpdate($weeklyUpdate);
            $this->notifyMinister($weeklyUpdate, 'update');
        }

        return redirect()->route('weekly-updates.show', $weeklyUpdate)
            ->with('success', 'Weekly update ' . $validated['action'] . ' successfully.');
    }

    public function consolidated(Request $request)
    {
        $user = $request->user();

        if (!$user->hasFullAccess() && !$user->isDirector()) {
            abort(403);
        }

        $divisionId = $request->input('division_id');
        $statusFilter = $request->input('status', '');
        
        // Check if explicit date filters were provided
        $hasDateFilter = $request->filled('week_start') || $request->filled('week_end');
        
        // If no explicit date filter is provided, show all updates (no date restriction)
        // This applies both when viewing a specific division and viewing all consolidated
        if (!$hasDateFilter) {
            $weekStart = null;
            $weekEnd = null;
        } else {
            $weekStart = $request->input('week_start');
            $weekEnd = $request->input('week_end');
        }

        $query = WeeklyUpdate::with(['division', 'submitter', 'reviewer', 'activities']);
        
        // Apply date filters only if they are set
        if ($weekStart && $weekEnd) {
            $query->where('week_start', '>=', $weekStart)
                  ->where('week_end', '<=', $weekEnd);
        }

        // Filter by specific division if provided
        if ($divisionId) {
            $query->where('division_id', $divisionId);
        } elseif ($user->isDirector() && !$user->hasFullAccess()) {
            $query->where('division_id', $user->division_id);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        } else {
            $query->whereIn('status', ['submitted', 'approved']);
        }

        $updates = $query->orderBy('division_id')->latest()->get();

        // Group by division
        $groupedUpdates = $updates->groupBy(fn($u) => $u->division->name ?? 'Unknown');

        // Division analytics
        $divisions = Division::where('is_active', true)->get();
        $divisionAnalytics = [];
        foreach ($divisions as $division) {
            $divUpdates = $updates->where('division_id', $division->id);
            $allActivities = $divUpdates->flatMap->activities;
            $divisionAnalytics[$division->name] = [
                'total_updates' => $divUpdates->count(),
                'total_activities' => $allActivities->count(),
                'completed' => $allActivities->where('status_flag', 'completed')->count(),
                'ongoing' => $allActivities->where('status_flag', 'ongoing')->count(),
                'not_started' => $allActivities->where('status_flag', 'not_started')->count(),
            ];
        }

        // Get selected division name for display
        $selectedDivision = $divisionId ? Division::find($divisionId) : null;

        return view('weekly-updates.consolidated', compact(
            'groupedUpdates', 'divisionAnalytics', 'weekStart', 'weekEnd', 'statusFilter', 'user', 'divisionId', 'selectedDivision'
        ));
    }

    public function downloadSingle(Request $request, WeeklyUpdate $weeklyUpdate)
    {
        $user = $request->user();

        if ($user->isDivisionScoped() && $weeklyUpdate->division_id !== $user->division_id) {
            abort(403);
        }

        $weeklyUpdate->load(['division', 'submitter', 'reviewer', 'activities']);
        $format = $request->input('format', 'pdf');

        $html = view('weekly-updates.download', [
            'updates' => collect([$weeklyUpdate]),
            'title' => "{$weeklyUpdate->division->name} – {$weeklyUpdate->week_label}",
            'subtitle' => "{$weeklyUpdate->week_start->format('M d')} – {$weeklyUpdate->week_end->format('M d, Y')} (Working Days)",
            'isConsolidated' => false,
            'format' => $format,
        ])->render();

        if ($format === 'word') {
            $filename = "weekly-update-{$weeklyUpdate->division->code}-{$weeklyUpdate->week_start->format('Y-m-d')}.doc";
            return response($html)
                ->header('Content-Type', 'application/vnd.ms-word')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        }

        // For PDF, return the printable page so the browser can print/save as PDF
        return response($html);
    }

    public function downloadConsolidated(Request $request)
    {
        $user = $request->user();

        if (!$user->hasFullAccess() && !$user->isDirector()) {
            abort(403);
        }

        $weekStart = $request->input('week_start');
        $weekEnd = $request->input('week_end');
        $divisionId = $request->input('division_id');
        $format = $request->input('format', 'pdf');

        $query = WeeklyUpdate::with(['division', 'submitter', 'reviewer', 'activities'])
            ->whereIn('status', ['submitted', 'approved']);

        // Apply date filters only if provided
        if ($weekStart && $weekEnd) {
            $query->where('week_start', '>=', $weekStart)
                  ->where('week_end', '<=', $weekEnd);
        }

        // Filter by specific division if provided
        if ($divisionId) {
            $query->where('division_id', $divisionId);
        } elseif ($user->isDirector() && !$user->hasFullAccess()) {
            $query->where('division_id', $user->division_id);
        }

        $updates = $query->orderBy('division_id')->latest()->get();
        $grouped = $updates->groupBy(fn($u) => $u->division->name ?? 'Unknown');

        // Build subtitle based on filters
        $subtitle = '';
        if ($weekStart && $weekEnd) {
            $subtitle = "Period: " . \Carbon\Carbon::parse($weekStart)->format('M d') . " – " . \Carbon\Carbon::parse($weekEnd)->format('M d, Y');
        } else {
            $subtitle = "All Submitted & Approved Reports";
        }

        // Get division name if filtering by division
        $divisionName = null;
        if ($divisionId) {
            $division = Division::find($divisionId);
            $divisionName = $division ? $division->name : null;
        }

        $html = view('weekly-updates.download', [
            'updates' => $updates,
            'grouped' => $grouped,
            'title' => $divisionName ? "{$divisionName} - Weekly Updates" : 'Consolidated Weekly Updates Report',
            'subtitle' => $subtitle,
            'isConsolidated' => true,
            'format' => $format,
        ])->render();

        if ($format === 'word') {
            $filename = $divisionId 
                ? "division-updates-{$divisionId}.doc" 
                : ($weekStart ? "consolidated-updates-{$weekStart}-to-{$weekEnd}.doc" : "consolidated-updates-all.doc");
            return response($html)
                ->header('Content-Type', 'application/vnd.ms-word')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        }

        // For PDF, return the printable page
        return response($html);
    }

    private function notifyBureauHead(WeeklyUpdate $update): void
    {
        // Only notify Admin Asst & Tech Asst for review (not Minister)
        $reviewers = \App\Models\User::whereIn('role', ['admin_assistant', 'tech_assistant'])
            ->where('is_active', true)->get();

        foreach ($reviewers as $reviewer) {
            BureauNotification::send(
                $reviewer->id,
                'reminder',
                'New Weekly Update Submitted',
                "A weekly update has been submitted by {$update->submitter->name} from {$update->division->name}.",
                route('weekly-updates.show', $update)
            );
        }
    }

    private function notifyMinister($update, string $type = 'update'): void
    {
        $ministers = \App\Models\User::where('role', 'minister')
            ->where('is_active', true)->get();

        $label = $type === 'update' ? 'Weekly Update' : 'Weekly Plan';

        foreach ($ministers as $minister) {
            BureauNotification::send(
                $minister->id,
                'approval',
                "{$label} Approved",
                "A {$label} from {$update->division->name} by {$update->submitter->name} has been approved and is ready for your review.",
                route('weekly-updates.show', $update)
            );
        }
    }

    public function activityComment(Request $request, UpdateActivity $activity)
    {
        $user = $request->user();

        // Only full-access users and directors of the same division can comment
        if (!$user->hasFullAccess() && !($user->isDirector() && $user->division_id === $activity->weeklyUpdate->division_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment = $activity->comments()->create([
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        // Notify the update submitter if the commenter is different
        $weeklyUpdate = $activity->weeklyUpdate;
        if ($user->id !== $weeklyUpdate->submitted_by) {
            BureauNotification::send(
                $weeklyUpdate->submitted_by,
                'reminder',
                'Comment on Your Activity',
                "{$user->name} commented on an activity in your weekly update for {$weeklyUpdate->week_start->format('M d')} - {$weeklyUpdate->week_end->format('M d, Y')}.",
                route('weekly-updates.show', $weeklyUpdate)
            );
        }

        return redirect()->route('weekly-updates.show', $weeklyUpdate)
            ->with('success', 'Comment added successfully.');
    }

    /**
     * Delete a weekly update (only drafts/rejected, by the submitter or full-access users).
     */
    public function destroy(WeeklyUpdate $weeklyUpdate)
    {
        $user = auth()->user();

        // Full-access users can delete any update; submitters can delete their own drafts/rejected
        if ($user->hasFullAccess()) {
            // allowed
        } elseif ($user->canManageDivision() && $weeklyUpdate->submitted_by === $user->id && in_array($weeklyUpdate->status, ['draft', 'rejected'])) {
            // allowed
        } else {
            abort(403);
        }

        $weekLabel = $weeklyUpdate->week_start->format('M d') . ' – ' . $weeklyUpdate->week_end->format('M d, Y');

        $weeklyUpdate->activities()->delete();
        $weeklyUpdate->delete();

        return redirect()->route('weekly-updates.index')
            ->with('success', "Weekly update for {$weekLabel} has been deleted.");
    }
}
