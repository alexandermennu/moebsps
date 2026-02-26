<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\BureauNotification;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Activity::with(['division', 'assignee', 'creator']);

        if ($user->isDirector()) {
            $query->byDivision($user->division_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('division_id') && !$user->isDirector()) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $activities = $query->latest()->paginate(15);
        $divisions = Division::where('is_active', true)->get();

        return view('activities.index', compact('activities', 'divisions', 'user'));
    }

    public function create()
    {
        $user = auth()->user();
        $divisions = Division::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        return view('activities.create', compact('user', 'divisions', 'users'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'division_id' => 'required|exists:divisions,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,critical',
            'start_date' => 'nullable|date',
            'due_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        if ($user->isDirector()) {
            $validated['division_id'] = $user->division_id;
        }

        $activity = Activity::create([
            ...$validated,
            'created_by' => $user->id,
            'status' => 'not_started',
        ]);

        if ($activity->assigned_to) {
            BureauNotification::send(
                $activity->assigned_to,
                'reminder',
                'New Activity Assigned',
                "You have been assigned a new activity: {$activity->title}",
                route('activities.show', $activity)
            );
        }

        return redirect()->route('activities.index')
            ->with('success', 'Activity created successfully.');
    }

    public function show(Activity $activity)
    {
        $user = auth()->user();

        if ($user->isDirector() && $activity->division_id !== $user->division_id) {
            abort(403);
        }

        $activity->load(['division', 'assignee', 'creator', 'comments.user']);

        return view('activities.show', compact('activity', 'user'));
    }

    public function edit(Activity $activity)
    {
        $user = auth()->user();

        if ($user->isDirector() && $activity->division_id !== $user->division_id) {
            abort(403);
        }

        $divisions = Division::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        return view('activities.edit', compact('activity', 'user', 'divisions', 'users'));
    }

    public function update(Request $request, Activity $activity)
    {
        $user = $request->user();

        if ($user->isDirector() && $activity->division_id !== $user->division_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'division_id' => 'required|exists:divisions,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:not_started,in_progress,completed,overdue',
            'priority' => 'required|in:low,medium,high,critical',
            'start_date' => 'nullable|date',
            'due_date' => 'required|date',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'remarks' => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed') {
            $validated['completed_date'] = now();
            $validated['progress_percentage'] = 100;
            $validated['is_overdue'] = false;
        }

        if ($user->isDirector()) {
            $validated['division_id'] = $user->division_id;
        }

        $activity->update($validated);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activity updated successfully.');
    }

    public function addComment(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        ActivityComment::create([
            'activity_id' => $activity->id,
            'user_id' => $request->user()->id,
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Comment added successfully.');
    }

    public function destroy(Activity $activity)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && !($user->isBureauHead())) {
            abort(403);
        }

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}
