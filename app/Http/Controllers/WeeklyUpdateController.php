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

        // Personal access only - cannot view weekly updates
        if ($user->hasPersonalAccessOnly()) {
            abort(403, 'You do not have access to weekly updates.');
        }

        $query = WeeklyUpdate::with(['division', 'submitter', 'reviewer']);

        if ($user->isDivisionScoped()) {
            $query->where('division_id', $user->division_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('division_id') && !$user->isDivisionScoped()) {
            $query->where('division_id', $request->division_id);
        }

        $updates = $query->latest()->paginate(15);

        // Division summaries for the bottom cards (full-access & directors)
        $divisionSummaries = collect();
        if ($user->hasFullAccess() || $user->isDirector()) {
            $summaryQuery = Division::where('is_active', true);
            if ($user->isDirector() && !$user->hasFullAccess()) {
                $summaryQuery->where('id', $user->division_id);
            }
            $divisionSummaries = $summaryQuery
                ->withCount([
                    'weeklyUpdates as total_updates',
                    'weeklyUpdates as approved_updates' => fn($q) => $q->where('status', 'approved'),
                    'weeklyUpdates as submitted_updates' => fn($q) => $q->where('status', 'submitted'),
                    'weeklyUpdates as rejected_updates' => fn($q) => $q->where('status', 'rejected'),
                ])
                ->with(['weeklyUpdates' => function ($q) {
                    $q->with('activities')->whereIn('status', ['submitted', 'approved'])->latest()->take(1);
                }])
                ->get()
                ->map(function ($division) {
                    $latest = $division->weeklyUpdates->first();
                    $division->latest_update = $latest;
                    $activityStats = ['completed' => 0, 'ongoing' => 0, 'not_started' => 0];
                    if ($latest && $latest->activities->count()) {
                        foreach ($latest->activities as $act) {
                            $flag = $act->status_flag ?? 'na';
                            if (isset($activityStats[$flag])) {
                                $activityStats[$flag]++;
                            }
                        }
                    }
                    $division->activity_stats = $activityStats;
                    return $division;
                });
        }

        return view('weekly-updates.index', compact('updates', 'user', 'divisionSummaries'));
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

        // Week filter: default to current week
        $weekStart = $request->filled('week_start') ? $request->week_start : now()->startOfWeek()->format('Y-m-d');
        $weekEnd = $request->filled('week_end') ? $request->week_end : now()->endOfWeek()->format('Y-m-d');
        $statusFilter = $request->input('status', '');

        $query = WeeklyUpdate::with(['division', 'submitter', 'reviewer', 'activities'])
            ->where('week_start', '>=', $weekStart)
            ->where('week_end', '<=', $weekEnd);

        if ($user->isDirector() && !$user->hasFullAccess()) {
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

        return view('weekly-updates.consolidated', compact(
            'groupedUpdates', 'divisionAnalytics', 'weekStart', 'weekEnd', 'statusFilter', 'user'
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
            'title' => "Weekly Update – {$weeklyUpdate->division->name}",
            'subtitle' => "Week of {$weeklyUpdate->week_start->format('M d')} – {$weeklyUpdate->week_end->format('M d, Y')}",
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

        $weekStart = $request->input('week_start', now()->startOfWeek()->format('Y-m-d'));
        $weekEnd = $request->input('week_end', now()->endOfWeek()->format('Y-m-d'));
        $format = $request->input('format', 'pdf');

        $query = WeeklyUpdate::with(['division', 'submitter', 'reviewer', 'activities'])
            ->where('week_start', '>=', $weekStart)
            ->where('week_end', '<=', $weekEnd)
            ->whereIn('status', ['submitted', 'approved']);

        if ($user->isDirector() && !$user->hasFullAccess()) {
            $query->where('division_id', $user->division_id);
        }

        $updates = $query->orderBy('division_id')->latest()->get();
        $grouped = $updates->groupBy(fn($u) => $u->division->name ?? 'Unknown');

        $html = view('weekly-updates.download', [
            'updates' => $updates,
            'grouped' => $grouped,
            'title' => 'Consolidated Weekly Updates Report',
            'subtitle' => "Period: " . \Carbon\Carbon::parse($weekStart)->format('M d') . " – " . \Carbon\Carbon::parse($weekEnd)->format('M d, Y'),
            'isConsolidated' => true,
            'format' => $format,
        ])->render();

        if ($format === 'word') {
            $filename = "consolidated-updates-{$weekStart}-to-{$weekEnd}.doc";
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
