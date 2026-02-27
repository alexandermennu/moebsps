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

        // Personal-only users see only their assigned tasks
        if ($user->hasPersonalAccessOnly()) {
            $query->where('assigned_to', $user->id);
        } elseif ($user->isDirector()) {
            // Directors see their division's assignments but NOT those from Office of the Minister
            $query->byDivision($user->division_id)
                  ->whereHas('creator', function ($q) {
                      $q->whereNotIn('role', [
                          User::ROLE_MINISTER,
                          User::ROLE_ADMIN_ASSISTANT,
                          User::ROLE_TECH_ASSISTANT,
                      ]);
                  });
        } elseif ($user->isDivisionScoped()) {
            // Other division-scoped users see their division's assignments
            $query->byDivision($user->division_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('division_id') && $user->hasFullAccess()) {
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

        if (!$user->canManageDivision()) {
            abort(403, 'You do not have permission to create assignments.');
        }

        $divisions = Division::where('is_active', true)->get();

        // Build smart user list: exclude counselors and Office of the Minister staff
        $ministerRoles = [User::ROLE_MINISTER, User::ROLE_ADMIN_ASSISTANT, User::ROLE_TECH_ASSISTANT];
        $usersQuery = User::where('is_active', true)
            ->where('role', '!=', User::ROLE_COUNSELOR)
            ->whereNotIn('role', $ministerRoles);
        if ($user->isDirector()) {
            $usersQuery->where('division_id', $user->division_id);
        }
        $users = $usersQuery->orderBy('name')->get();

        // Counselors list: only for full-access users or CGPC (Counseling Division) directors
        $counselors = collect();
        $canAssignCounselor = false;
        if ($user->hasFullAccess() || ($user->isDirector() && $user->division?->code === 'CGPC')) {
            $canAssignCounselor = true;
            $counselors = User::where('is_active', true)
                ->where('role', User::ROLE_COUNSELOR)
                ->orderBy('name')
                ->get();
        }

        return view('activities.create', compact('user', 'divisions', 'users', 'counselors', 'canAssignCounselor'));
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

        if (!$user->canManageDivision()) {
            abort(403);
        }

        // Validate counselor assignment: only full-access or CGPC director
        if ($validated['assigned_to']) {
            $assignee = User::find($validated['assigned_to']);
            if ($assignee && $assignee->isCounselor()) {
                if (!$user->hasFullAccess() && !($user->isDirector() && $user->division?->code === 'CGPC')) {
                    abort(403, 'You do not have permission to assign tasks to counselors.');
                }
            }
        }

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
                'New Assignment',
                "You have been assigned a new task: {$activity->title}",
                route('activities.show', $activity)
            );
        }

        return redirect()->route('activities.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function show(Activity $activity)
    {
        $user = auth()->user();

        if ($user->hasPersonalAccessOnly() && $activity->assigned_to !== $user->id) {
            abort(403);
        }

        if ($user->isDivisionScoped() && $activity->division_id !== $user->division_id) {
            abort(403);
        }

        // Directors cannot view assignments created by Office of the Minister
        if ($user->isDirector() && $activity->creator && $activity->creator->hasFullAccess()) {
            abort(403);
        }

        $activity->load(['division', 'assignee', 'creator', 'comments.user']);

        return view('activities.show', compact('activity', 'user'));
    }

    public function edit(Activity $activity)
    {
        $user = auth()->user();

        if (!$user->canManageDivision()) {
            abort(403, 'You do not have permission to edit assignments.');
        }

        if ($user->isDirector() && $activity->division_id !== $user->division_id) {
            abort(403);
        }

        // Directors cannot edit assignments created by Office of the Minister
        if ($user->isDirector() && $activity->creator && $activity->creator->hasFullAccess()) {
            abort(403);
        }

        $divisions = Division::where('is_active', true)->get();

        // Build smart user list: exclude counselors and Office of the Minister staff
        $ministerRoles = [User::ROLE_MINISTER, User::ROLE_ADMIN_ASSISTANT, User::ROLE_TECH_ASSISTANT];
        $usersQuery = User::where('is_active', true)
            ->where('role', '!=', User::ROLE_COUNSELOR)
            ->whereNotIn('role', $ministerRoles);
        if ($user->isDirector()) {
            $usersQuery->where('division_id', $user->division_id);
        }
        $users = $usersQuery->orderBy('name')->get();

        // Counselors list: only for full-access users or CGPC director
        $counselors = collect();
        $canAssignCounselor = false;
        if ($user->hasFullAccess() || ($user->isDirector() && $user->division?->code === 'CGPC')) {
            $canAssignCounselor = true;
            $counselors = User::where('is_active', true)
                ->where('role', User::ROLE_COUNSELOR)
                ->orderBy('name')
                ->get();
        }

        // Check if current assignee is a counselor
        $assigneeIsCounselor = $activity->assignee && $activity->assignee->isCounselor();

        return view('activities.edit', compact('activity', 'user', 'divisions', 'users', 'counselors', 'canAssignCounselor', 'assigneeIsCounselor'));
    }

    public function update(Request $request, Activity $activity)
    {
        $user = $request->user();

        if (!$user->canManageDivision()) {
            abort(403);
        }

        if ($user->isDirector() && $activity->division_id !== $user->division_id) {
            abort(403);
        }

        // Directors cannot edit assignments from Office of the Minister
        if ($user->isDirector() && $activity->creator && $activity->creator->hasFullAccess()) {
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

        // Validate counselor assignment
        if ($validated['assigned_to']) {
            $assignee = User::find($validated['assigned_to']);
            if ($assignee && $assignee->isCounselor()) {
                if (!$user->hasFullAccess() && !($user->isDirector() && $user->division?->code === 'CGPC')) {
                    abort(403, 'You do not have permission to assign tasks to counselors.');
                }
            }
        }

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
            ->with('success', 'Assignment updated successfully.');
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

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Assignment deleted successfully.');
    }
}
