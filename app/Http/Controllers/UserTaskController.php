<?php

namespace App\Http\Controllers;

use App\Models\UserTask;
use Illuminate\Http\Request;

class UserTaskController extends Controller
{
    /**
     * Display a listing of user's tasks with daily/weekly layout.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        // Get today's tasks (scheduled for today)
        $todaysTasks = UserTask::where('user_id', $user->id)
            ->where(function($q) use ($today) {
                $q->whereDate('scheduled_date', $today)
                  ->orWhereDate('due_date', $today);
            })
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->get();

        // Get weekly targets (tasks marked as weekly targets or due this week)
        $weeklyTasks = UserTask::where('user_id', $user->id)
            ->where(function($q) use ($startOfWeek, $endOfWeek, $today) {
                $q->where('is_weekly_target', true)
                  ->orWhereBetween('due_date', [$startOfWeek, $endOfWeek]);
            })
            ->where(function($q) use ($today) {
                // Exclude tasks already shown in today's list (unless they're weekly targets)
                $q->where('is_weekly_target', true)
                  ->orWhereNull('scheduled_date')
                  ->orWhereDate('scheduled_date', '!=', $today);
            })
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
            ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->get();

        // Get counts for summary
        $pendingCount = UserTask::where('user_id', $user->id)->pending()->count();
        $overdueCount = UserTask::where('user_id', $user->id)->overdue()->count();
        $todayCompletedCount = UserTask::where('user_id', $user->id)
            ->whereDate('scheduled_date', $today)
            ->completed()
            ->count();
        $todayPendingCount = UserTask::where('user_id', $user->id)
            ->where(function($q) use ($today) {
                $q->whereDate('scheduled_date', $today)
                  ->orWhereDate('due_date', $today);
            })
            ->pending()
            ->count();
        $weeklyCompletedCount = UserTask::where('user_id', $user->id)
            ->where(function($q) use ($startOfWeek, $endOfWeek) {
                $q->where('is_weekly_target', true)
                  ->orWhereBetween('due_date', [$startOfWeek, $endOfWeek]);
            })
            ->completed()
            ->count();
        $weeklyTotalCount = UserTask::where('user_id', $user->id)
            ->where(function($q) use ($startOfWeek, $endOfWeek) {
                $q->where('is_weekly_target', true)
                  ->orWhereBetween('due_date', [$startOfWeek, $endOfWeek]);
            })
            ->count();

        $relatedToOptions = UserTask::getRelatedToOptions();
        $priorityOptions = UserTask::getPriorityOptions();

        return view('tasks.index', compact(
            'user',
            'todaysTasks',
            'weeklyTasks',
            'pendingCount',
            'overdueCount',
            'todayCompletedCount',
            'todayPendingCount',
            'weeklyCompletedCount',
            'weeklyTotalCount',
            'relatedToOptions',
            'priorityOptions'
        ));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $relatedToOptions = UserTask::getRelatedToOptions();
        $priorityOptions = UserTask::getPriorityOptions();

        return view('tasks.create', compact('relatedToOptions', 'priorityOptions'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'scheduled_date' => 'nullable|date',
            'is_weekly_target' => 'nullable|boolean',
            'priority' => 'required|in:low,medium,high',
            'related_to' => 'required|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';
        $validated['is_weekly_target'] = $request->has('is_weekly_target');

        UserTask::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(UserTask $task)
    {
        // Ensure user owns this task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $relatedToOptions = UserTask::getRelatedToOptions();
        $priorityOptions = UserTask::getPriorityOptions();
        $statusOptions = UserTask::getStatusOptions();

        return view('tasks.edit', compact('task', 'relatedToOptions', 'priorityOptions', 'statusOptions'));
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, UserTask $task)
    {
        // Ensure user owns this task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'scheduled_date' => 'nullable|date',
            'is_weekly_target' => 'nullable|boolean',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'related_to' => 'required|string',
        ]);

        $validated['is_weekly_target'] = $request->has('is_weekly_target');

        // Set completed_at if marking as completed
        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(UserTask $task)
    {
        // Ensure user owns this task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    /**
     * Toggle task completion status (AJAX).
     */
    public function toggleComplete(Request $request, UserTask $task)
    {
        // Ensure user owns this task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        if ($task->status === 'completed') {
            $task->markAsPending();
            $message = 'Task marked as pending.';
        } else {
            $task->markAsCompleted();
            $message = 'Task marked as completed.';
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message, 'task' => $task]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Quick add task (AJAX).
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'scheduled_date' => 'nullable|date',
            'is_weekly_target' => 'nullable|boolean',
            'priority' => 'nullable|in:low,medium,high',
            'related_to' => 'nullable|string',
        ]);

        $task = UserTask::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'due_date' => $validated['due_date'] ?? null,
            'scheduled_date' => $validated['scheduled_date'] ?? null,
            'is_weekly_target' => $request->has('is_weekly_target'),
            'priority' => $validated['priority'] ?? 'medium',
            'related_to' => $validated['related_to'] ?? 'personal',
            'status' => 'pending',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'task' => $task]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task added successfully.');
    }

    /**
     * Schedule a task for today (move from weekly to daily).
     */
    public function scheduleForToday(Request $request, UserTask $task)
    {
        // Ensure user owns this task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update(['scheduled_date' => now()->toDateString()]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Task scheduled for today.', 'task' => $task]);
        }

        return redirect()->back()->with('success', 'Task scheduled for today.');
    }

    /**
     * Remove task from today's schedule.
     */
    public function unschedule(Request $request, UserTask $task)
    {
        // Ensure user owns this task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update(['scheduled_date' => null]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Task removed from today.', 'task' => $task]);
        }

        return redirect()->back()->with('success', 'Task removed from today\'s schedule.');
    }
}
