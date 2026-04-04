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
        $todayCarbon = now()->startOfDay();
        $today = $todayCarbon->toDateString();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $view = $request->get('view', 'split'); // split, all, completed

        // Check if new columns exist (for backwards compatibility)
        $hasScheduledDate = \Schema::hasColumn('user_tasks', 'scheduled_date');
        $hasWeeklyTarget = \Schema::hasColumn('user_tasks', 'is_weekly_target');

        if ($view === 'all') {
            // Show all active tasks
            $allTasks = UserTask::where('user_id', $user->id)
                ->pending()
                ->orderByRaw("CASE WHEN due_date < CURDATE() THEN 0 ELSE 1 END")
                ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
                ->orderBy('due_date')
                ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                ->get();
            
            $todaysTasks = collect();
            $weeklyTasks = collect();
        } elseif ($view === 'completed') {
            // Show completed tasks
            $allTasks = UserTask::where('user_id', $user->id)
                ->completed()
                ->orderBy('completed_at', 'desc')
                ->get();
            
            $todaysTasks = collect();
            $weeklyTasks = collect();
        } else {
            $allTasks = collect();
            
            // Get today's PENDING tasks: scheduled for today OR due today
            $todayPendingQuery = UserTask::where('user_id', $user->id)
                ->pending();
            if ($hasScheduledDate) {
                $todayPendingQuery->where(function($q) use ($today) {
                    $q->whereDate('scheduled_date', $today)
                      ->orWhere(function($q2) use ($today) {
                          $q2->whereNull('scheduled_date')
                             ->whereDate('due_date', $today);
                      });
                });
            } else {
                $todayPendingQuery->whereDate('due_date', $today);
            }
            $todayPendingTasks = $todayPendingQuery
                ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                ->get()
                ->map(function($task) {
                    $task->is_overdue_from = null;
                    return $task;
                });

            // Get today's COMPLETED tasks (completed today OR due/scheduled for today)
            $todayCompletedQuery = UserTask::where('user_id', $user->id)
                ->completed()
                ->where(function($q) use ($today, $hasScheduledDate) {
                    // Tasks completed today (regardless of due date)
                    $q->whereDate('completed_at', $today);
                    
                    // OR tasks due/scheduled for today that are completed
                    if ($hasScheduledDate) {
                        $q->orWhere(function($q2) use ($today) {
                            $q2->whereDate('scheduled_date', $today);
                        })->orWhere(function($q2) use ($today) {
                            $q2->whereNull('scheduled_date')
                               ->whereDate('due_date', $today);
                        });
                    } else {
                        $q->orWhereDate('due_date', $today);
                    }
                });
            $todayCompletedTasks = $todayCompletedQuery
                ->orderBy('completed_at', 'desc')
                ->get()
                ->map(function($task) {
                    $task->is_overdue_from = null;
                    return $task;
                });

            // Get user's preference for overdue days (default 3 if not set)
            $overdueDaysLimit = $user->task_overdue_days ?? 3;
            $overdueCutoffDate = $todayCarbon->copy()->subDays($overdueDaysLimit)->toDateString();

            // Get overdue tasks from previous days (PENDING only - not completed)
            // Only show tasks within the user's overdue days limit
            $overdueTasksQuery = UserTask::where('user_id', $user->id)
                ->pending();
            if ($hasScheduledDate) {
                $overdueTasksQuery->where(function($q) use ($today, $overdueCutoffDate) {
                    $q->where(function($q2) use ($today, $overdueCutoffDate) {
                        $q2->whereDate('scheduled_date', '<', $today)
                           ->whereDate('scheduled_date', '>=', $overdueCutoffDate);
                    })->orWhere(function($q2) use ($today, $overdueCutoffDate) {
                        $q2->whereNull('scheduled_date')
                           ->whereDate('due_date', '<', $today)
                           ->whereDate('due_date', '>=', $overdueCutoffDate);
                    });
                });
            } else {
                $overdueTasksQuery->whereDate('due_date', '<', $today)
                                  ->whereDate('due_date', '>=', $overdueCutoffDate);
            }
            $overdueTasks = $overdueTasksQuery
                ->orderBy('due_date', 'desc')
                ->get()
                ->map(function($task) use ($hasScheduledDate) {
                    // Mark the original date for display
                    $originalDate = $hasScheduledDate && $task->scheduled_date 
                        ? $task->scheduled_date 
                        : $task->due_date;
                    $task->is_overdue_from = $originalDate;
                    return $task;
                });

            // Combine in order: today's pending, today's completed, then overdue at bottom
            $todaysTasks = $todayPendingTasks
                ->concat($todayCompletedTasks)
                ->concat($overdueTasks);

            // Get weekly targets: ALL tasks for this week (pending AND completed)
            // Include tasks by due_date OR scheduled_date within this week
            $weeklyQuery = UserTask::where('user_id', $user->id);
            if ($hasWeeklyTarget && $hasScheduledDate) {
                $weeklyQuery->where(function($q) use ($startOfWeek, $endOfWeek) {
                    $q->where('is_weekly_target', true)
                      ->orWhereBetween('due_date', [$startOfWeek, $endOfWeek])
                      ->orWhereBetween('scheduled_date', [$startOfWeek, $endOfWeek]);
                });
            } elseif ($hasScheduledDate) {
                $weeklyQuery->where(function($q) use ($startOfWeek, $endOfWeek) {
                    $q->whereBetween('due_date', [$startOfWeek, $endOfWeek])
                      ->orWhereBetween('scheduled_date', [$startOfWeek, $endOfWeek]);
                });
            } elseif ($hasWeeklyTarget) {
                $weeklyQuery->where(function($q) use ($startOfWeek, $endOfWeek) {
                    $q->where('is_weekly_target', true)
                      ->orWhereBetween('due_date', [$startOfWeek, $endOfWeek]);
                });
            } else {
                $weeklyQuery->whereBetween('due_date', [$startOfWeek, $endOfWeek]);
            }
            $weeklyTasks = $weeklyQuery
                ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
                ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
                ->orderBy('due_date')
                ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                ->get();
        }

        // Get counts for summary
        $pendingCount = UserTask::where('user_id', $user->id)->pending()->count();
        $overdueCount = UserTask::where('user_id', $user->id)->overdue()->count();
        $completedCount = UserTask::where('user_id', $user->id)->completed()->count();
        
        $todayPendingCount = UserTask::where('user_id', $user->id)
            ->whereDate('due_date', $today)
            ->pending()
            ->count();
            
        $weeklyCompletedCount = 0;
        $weeklyTotalCount = 0;
        if ($hasWeeklyTarget) {
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
        }

        $relatedToOptions = UserTask::getRelatedToOptions();
        $priorityOptions = UserTask::getPriorityOptions();

        return view('tasks.index', compact(
            'user',
            'view',
            'todaysTasks',
            'weeklyTasks',
            'allTasks',
            'pendingCount',
            'overdueCount',
            'completedCount',
            'todayPendingCount',
            'weeklyCompletedCount',
            'weeklyTotalCount',
            'relatedToOptions',
            'priorityOptions',
            'hasScheduledDate',
            'hasWeeklyTarget'
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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        if ($task->status === 'completed') {
            $task->markAsPending();
            $message = 'Task marked as pending.';
            $status = 'pending';
        } else {
            $task->markAsCompleted();
            $message = 'Task marked as completed.';
            $status = 'completed';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => $message, 
                'status' => $status,
                'task_id' => $task->id
            ]);
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

    /**
     * Update user's task preferences/settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'task_overdue_days' => ['required', 'integer', 'min:0', 'max:30'],
        ]);

        auth()->user()->update([
            'task_overdue_days' => $validated['task_overdue_days'],
        ]);

        return redirect()->route('tasks.index', ['view' => 'settings'])
            ->with('success', 'Task preferences saved successfully.');
    }
}
