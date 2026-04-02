<?php

namespace App\Http\Controllers;

use App\Models\UserTask;
use Illuminate\Http\Request;

class UserTaskController extends Controller
{
    /**
     * Display a listing of user's tasks.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = UserTask::where('user_id', $user->id);

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('status', '!=', 'completed');
            } else {
                $query->where('status', $request->status);
            }
        } else {
            // Default: show active (non-completed) tasks
            $query->where('status', '!=', 'completed');
        }

        // Filter by related_to
        if ($request->has('related_to') && $request->related_to) {
            $query->where('related_to', $request->related_to);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Order: overdue first, then by due date, then by priority
        $tasks = $query->orderByRaw("CASE WHEN due_date < CURDATE() AND status != 'completed' THEN 0 ELSE 1 END")
                       ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
                       ->orderBy('due_date')
                       ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                       ->get();

        // Get counts for summary
        $pendingCount = UserTask::where('user_id', $user->id)->pending()->count();
        $overdueCount = UserTask::where('user_id', $user->id)->overdue()->count();
        $dueTodayCount = UserTask::where('user_id', $user->id)->pending()->dueToday()->count();
        $completedCount = UserTask::where('user_id', $user->id)->completed()->count();

        $relatedToOptions = UserTask::getRelatedToOptions();
        $priorityOptions = UserTask::getPriorityOptions();
        $statusOptions = UserTask::getStatusOptions();

        return view('tasks.index', compact(
            'user',
            'tasks',
            'pendingCount',
            'overdueCount',
            'dueTodayCount',
            'completedCount',
            'relatedToOptions',
            'priorityOptions',
            'statusOptions'
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
            'priority' => 'required|in:low,medium,high',
            'related_to' => 'required|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

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
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'related_to' => 'required|string',
        ]);

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
            'priority' => 'nullable|in:low,medium,high',
            'related_to' => 'nullable|string',
        ]);

        $task = UserTask::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'due_date' => $validated['due_date'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'related_to' => $validated['related_to'] ?? 'personal',
            'status' => 'pending',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'task' => $task]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task added successfully.');
    }
}
