<?php

namespace App\Http\Controllers;

use App\Models\BureauNotification;
use App\Models\UpdateActivity;
use App\Models\WeeklyUpdate;
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

        return view('weekly-updates.index', compact('updates', 'user'));
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

        $weeklyUpdate->load(['division', 'submitter', 'reviewer', 'activities']);

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

        if (!$user->canReview()) {
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

        return redirect()->route('weekly-updates.show', $weeklyUpdate)
            ->with('success', 'Weekly update ' . $validated['action'] . ' successfully.');
    }

    private function notifyBureauHead(WeeklyUpdate $update): void
    {
        $reviewers = \App\Models\User::whereIn('role', ['minister', 'admin_assistant', 'tech_assistant'])
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
}
