<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\ActivityFile;
use App\Models\BureauNotification;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Check module access
        if (!$user->canAccessAssignments()) {
            abort(403, 'You do not have access to assignments.');
        }

        $query = Activity::with(['division', 'assignee', 'creator']);

        // All users (including Minister) see only:
        // 1. Assignments they created
        // 2. Assignments assigned to them
        $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('division_id')) {
            if ($request->division_id === 'minister') {
                $query->whereNull('division_id');
            } else {
                $query->where('division_id', $request->division_id);
            }
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $activities = $query->latest()->paginate(15);
        $divisions = Division::where('is_active', true)->get();

        // Division summary stats
        $divisionStats = [];
        $statsQuery = Activity::query();
        
        // Apply same scope restrictions for stats
        if ($user->hasPersonalAccessOnly()) {
            $statsQuery->where('assigned_to', $user->id);
        } elseif ($user->isDirector()) {
            $statsQuery->where(function ($q) use ($user) {
                $q->where(function ($inner) use ($user) {
                    $inner->where('division_id', $user->division_id)
                          ->whereHas('creator', function ($c) {
                              $c->whereNotIn('role', [
                                  User::ROLE_MINISTER,
                                  User::ROLE_ADMIN_ASSISTANT,
                                  User::ROLE_TECH_ASSISTANT,
                              ]);
                          });
                })->orWhere('assigned_to', $user->id);
            });
        } elseif ($user->isDivisionScoped()) {
            $statsQuery->where(function ($q) use ($user) {
                $q->where('division_id', $user->division_id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        $allActivities = $statsQuery->get();
        
        foreach ($divisions as $division) {
            $divisionActivities = $allActivities->where('division_id', $division->id);
            $divisionStats[$division->id] = [
                'name' => $division->name,
                'code' => $division->code,
                'total' => $divisionActivities->count(),
                'completed' => $divisionActivities->where('status', 'completed')->count(),
                'in_progress' => $divisionActivities->where('status', 'in_progress')->count(),
                'overdue' => $divisionActivities->where('is_overdue', true)->count(),
                'not_started' => $divisionActivities->where('status', 'not_started')->count(),
            ];
        }
        
        // Add Office of the Minister stats (null division)
        $ministerActivities = $allActivities->whereNull('division_id');
        $divisionStats['minister'] = [
            'name' => 'Office of the Minister',
            'code' => 'OOM',
            'total' => $ministerActivities->count(),
            'completed' => $ministerActivities->where('status', 'completed')->count(),
            'in_progress' => $ministerActivities->where('status', 'in_progress')->count(),
            'overdue' => $ministerActivities->where('is_overdue', true)->count(),
            'not_started' => $ministerActivities->where('status', 'not_started')->count(),
        ];
        
        // Overall stats
        $overallStats = [
            'total' => $allActivities->count(),
            'completed' => $allActivities->where('status', 'completed')->count(),
            'in_progress' => $allActivities->where('status', 'in_progress')->count(),
            'overdue' => $allActivities->where('is_overdue', true)->count(),
            'not_started' => $allActivities->where('status', 'not_started')->count(),
        ];

        return view('activities.index', compact('activities', 'divisions', 'user', 'divisionStats', 'overallStats'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->canManageDivision()) {
            abort(403, 'You do not have permission to create assignments.');
        }

        $divisions = Division::where('is_active', true)->get();

        // Build user list: users in the same division as the current user
        // Exclude counselors (handled separately) and Minister (doesn't get assigned tasks)
        $usersQuery = User::where('is_active', true)
            ->where('role', '!=', User::ROLE_COUNSELOR)
            ->where('role', '!=', User::ROLE_MINISTER);
        
        // Directors and Minister's Office staff only see users in their own division
        if ($user->isDirector() || $user->hasFullAccess()) {
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
        if ($validated['assigned_to'] ?? null) {
            $assignee = User::find($validated['assigned_to']);
            if ($assignee && $assignee->isCounselor()) {
                if (!$user->hasFullAccess() && !($user->isDirector() && $user->division?->code === 'CGPC')) {
                    abort(403, 'You do not have permission to assign tasks to counselors.');
                }
            }
        }

        $activity = Activity::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'division_id' => $validated['division_id'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'],
            'remarks' => $validated['remarks'] ?? null,
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

        // Assignees can always view their own assigned tasks
        $isAssignee = $activity->assigned_to === $user->id;

        if (!$isAssignee) {
            if ($user->hasPersonalAccessOnly()) {
                abort(403);
            }

            if ($user->isDivisionScoped() && $activity->division_id !== $user->division_id) {
                abort(403);
            }

            // Directors cannot view assignments created by Office of the Minister
            if ($user->isDirector() && $activity->creator && $activity->creator->hasFullAccess()) {
                abort(403);
            }
        }

        $activity->load(['division', 'assignee', 'creator', 'comments.user', 'files.uploader']);

        return view('activities.show', compact('activity', 'user'));
    }

    public function edit(Activity $activity)
    {
        $user = auth()->user();

        if (!$user->canManageDivision()) {
            abort(403, 'You do not have permission to edit assignments.');
        }

        if ($user->isDirector() && $activity->division_id !== $user->division_id && $activity->assigned_to !== $user->id) {
            abort(403);
        }

        // Directors cannot edit assignments created by Office of the Minister (unless assigned to them)
        if ($user->isDirector() && $activity->creator && $activity->creator->hasFullAccess() && $activity->assigned_to !== $user->id) {
            abort(403);
        }

        $divisions = Division::where('is_active', true)->get();

        // Build user list: users in the same division as the current user
        // Exclude counselors (handled separately) and Minister (doesn't get assigned tasks)
        $usersQuery = User::where('is_active', true)
            ->where('role', '!=', User::ROLE_COUNSELOR)
            ->where('role', '!=', User::ROLE_MINISTER);
        
        // Directors and Minister's Office staff only see users in their own division
        if ($user->isDirector() || $user->hasFullAccess()) {
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

        if ($user->isDirector() && $activity->division_id !== $user->division_id && $activity->assigned_to !== $user->id) {
            abort(403);
        }

        // Directors cannot edit assignments from Office of the Minister (unless assigned to them)
        if ($user->isDirector() && $activity->creator && $activity->creator->hasFullAccess() && $activity->assigned_to !== $user->id) {
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

        // Progress can only be > 0 if status is not 'not_started'
        if ($validated['status'] === 'not_started' && $validated['progress_percentage'] > 0) {
            return back()->withErrors(['progress_percentage' => 'Progress cannot be set while status is "Not Started". Change status first.'])->withInput();
        }

        if ($validated['status'] === 'completed') {
            $validated['completed_date'] = now();
            $validated['progress_percentage'] = 100;
            $validated['is_overdue'] = false;
        }

        // If starting work, set start_date if not already set
        if ($validated['status'] === 'in_progress' && !$activity->start_date) {
            $validated['start_date'] = now();
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

    /**
     * Update progress and status (for assignees).
     * Assignees can only update status and progress (not other fields).
     * Progress can only be updated if status is not 'not_started'.
     */
    public function updateProgress(Request $request, Activity $activity)
    {
        $user = $request->user();

        // Only the assignee or managers can update progress
        $isAssignee = $activity->assigned_to === $user->id;
        $isManager = $user->canManageDivision();

        if (!$isAssignee && !$isManager) {
            abort(403, 'You do not have permission to update this assignment.');
        }

        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed,overdue',
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        // Progress can only be > 0 if status is not 'not_started'
        if ($validated['status'] === 'not_started' && $validated['progress_percentage'] > 0) {
            return back()->withErrors(['progress_percentage' => 'Progress cannot be set while status is "Not Started". Change status first.']);
        }

        // Auto-set progress to 100 when completed
        if ($validated['status'] === 'completed') {
            $validated['progress_percentage'] = 100;
            $validated['completed_date'] = now();
            $validated['is_overdue'] = false;
        }

        // If starting work, set start_date if not already set
        if ($validated['status'] === 'in_progress' && !$activity->start_date) {
            $validated['start_date'] = now();
        }

        $activity->update($validated);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Progress updated successfully.');
    }

    /**
     * Upload files to an activity.
     */
    public function uploadFiles(Request $request, Activity $activity)
    {
        $user = $request->user();

        // Assignee or managers can upload files
        $isAssignee = $activity->assigned_to === $user->id;
        $isManager = $user->canManageDivision();

        if (!$isAssignee && !$isManager) {
            abort(403, 'You do not have permission to upload files to this assignment.');
        }

        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,txt,csv,zip',
            'description' => 'nullable|string|max:500',
        ]);

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('activity-files/' . $activity->id, $filename, 'local');

            $activityFile = ActivityFile::create([
                'activity_id' => $activity->id,
                'uploaded_by' => $user->id,
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'path' => $path,
                'description' => $request->description,
            ]);

            $uploadedFiles[] = $activityFile;
        }

        return redirect()->route('activities.show', $activity)
            ->with('success', count($uploadedFiles) . ' file(s) uploaded successfully.');
    }

    /**
     * Download a file.
     */
    public function downloadFile(Activity $activity, ActivityFile $file)
    {
        $user = auth()->user();

        // Verify file belongs to activity
        if ($file->activity_id !== $activity->id) {
            abort(404);
        }

        // Check access - same as show
        $isAssignee = $activity->assigned_to === $user->id;

        if (!$isAssignee) {
            if ($user->hasPersonalAccessOnly()) {
                abort(403);
            }
            if ($user->isDivisionScoped() && $activity->division_id !== $user->division_id) {
                abort(403);
            }
        }

        if (!Storage::disk('local')->exists($file->path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download($file->path, $file->original_filename);
    }

    /**
     * Delete a file.
     */
    public function deleteFile(Activity $activity, ActivityFile $file)
    {
        $user = auth()->user();

        // Verify file belongs to activity
        if ($file->activity_id !== $activity->id) {
            abort(404);
        }

        // Only file uploader or managers can delete
        $isUploader = $file->uploaded_by === $user->id;
        $isManager = $user->canManageDivision();

        if (!$isUploader && !$isManager) {
            abort(403, 'You do not have permission to delete this file.');
        }

        // Delete from storage
        Storage::disk('local')->delete($file->path);

        // Delete record
        $file->delete();

        return redirect()->route('activities.show', $activity)
            ->with('success', 'File deleted successfully.');
    }

    public function destroy(Activity $activity)
    {
        $user = auth()->user();

        if (!$user->hasFullAccess()) {
            abort(403);
        }

        // Delete all associated files from storage
        foreach ($activity->files as $file) {
            Storage::disk('local')->delete($file->path);
        }

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Assignment deleted successfully.');
    }
}
