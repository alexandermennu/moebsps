@extends('layouts.app')

@section('title', 'My Tasks')
@section('page-title', 'My Tasks')

@push('styles')
<style>
    /* Smooth task animations */
    .task-item {
        transition: all 0.2s ease-out;
    }
    .task-item:hover {
        transform: translateX(4px);
        background-color: rgb(248 250 252);
    }
    
    /* Checkbox animation */
    .task-checkbox-wrapper {
        transition: transform 0.15s ease-out;
        cursor: pointer;
    }
    .task-checkbox-wrapper:hover {
        transform: scale(1.1);
    }
    .task-checkbox-wrapper:active {
        transform: scale(0.95);
    }
    .task-checkbox {
        pointer-events: none;
    }
    
    /* Action buttons */
    .task-actions {
        transition: opacity 0.15s ease-out, transform 0.15s ease-out;
        transform: translateX(8px);
    }
    .task-item:hover .task-actions {
        opacity: 1 !important;
        transform: translateX(0);
    }
    .task-action-btn {
        transition: all 0.15s ease-out;
    }
    .task-action-btn:hover {
        transform: scale(1.15);
    }
    .task-action-btn:active {
        transform: scale(0.9);
    }
    
    /* Card hover effects */
    .stat-card {
        transition: all 0.2s ease-out;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    /* Tab transitions */
    .tab-link {
        transition: all 0.2s ease-out;
    }
    
    /* Progress bar animation */
    .progress-fill {
        transition: width 0.5s ease-out;
    }
    
    /* Task completion styles */
    .task-item.is-completed .task-title {
        text-decoration: line-through;
        color: rgb(148 163 184);
    }
    .task-item.is-completed {
        background-color: rgb(248 250 252 / 0.5);
    }
    .task-checkbox.checked {
        background-color: rgb(34 197 94);
        border-color: rgb(34 197 94);
    }
    .task-checkbox.checked svg {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">My Tasks</h1>
            <p class="text-sm text-slate-500 mt-1">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <a href="{{ route('tasks.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Task
        </a>
    </div>

    {{-- View Tabs --}}
    <div class="flex items-center gap-2 mb-3 border-b border-slate-200">
        <a href="{{ route('tasks.index', ['view' => 'split']) }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $view === 'split' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700' }}">
            Today & Weekly
        </a>
        <a href="{{ route('tasks.index', ['view' => 'all']) }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $view === 'all' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700' }}">
            All Tasks
        </a>
        <a href="{{ route('tasks.index', ['view' => 'completed']) }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $view === 'completed' ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-slate-700' }}">
            Completed
        </a>
    </div>

    @if($view === 'split')
    {{-- Two Column Layout for Today & Weekly --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
        {{-- LEFT COLUMN: Today's Tasks --}}
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 rounded-lg p-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Today's Tasks</h2>
                                <p class="text-blue-100 text-sm">{{ now()->format('D, M j') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-white">{{ $todaysTasks->where('status', '!=', 'completed')->count() }}</span>
                            <p class="text-blue-100 text-xs">remaining</p>
                        </div>
                    </div>
                </div>

                {{-- Quick Add for Today --}}
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <form action="{{ route('tasks.quick-store') }}" method="POST" class="flex gap-2">
                        @csrf
                        @if($hasScheduledDate ?? false)
                            <input type="hidden" name="scheduled_date" value="{{ now()->toDateString() }}">
                        @else
                            <input type="hidden" name="due_date" value="{{ now()->toDateString() }}">
                        @endif
                        <input type="text" 
                               name="title" 
                               placeholder="Add a task for today..." 
                               class="flex-1 text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               required>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                            Add
                        </button>
                    </form>
                </div>

                {{-- Today's Task List --}}
                <div class="divide-y divide-slate-100">
                    @forelse($todaysTasks as $task)
                        <div class="task-item px-5 py-2.5 group {{ $task->status === 'completed' ? 'is-completed' : '' }} {{ $task->is_overdue_from ? 'bg-red-50/50' : '' }}" data-task-id="{{ $task->id }}">
                            <div class="flex items-center gap-3">
                                {{-- Checkbox --}}
                                <div class="task-checkbox-wrapper">
                                    <label class="relative flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               class="sr-only task-toggle"
                                               data-task-id="{{ $task->id }}"
                                               {{ $task->status === 'completed' ? 'checked' : '' }}>
                                        <div class="task-checkbox w-5 h-5 border-2 {{ $task->status === 'completed' ? 'bg-green-500 border-green-500 checked' : 'border-slate-300 hover:border-blue-400 hover:bg-blue-50' }} flex items-center justify-center transition-all duration-150">
                                            <svg class="w-3 h-3 text-white {{ $task->status === 'completed' ? '' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </label>
                                </div>

                                {{-- Task Content --}}
                                <div class="flex-1 min-w-0">
                                    <p class="task-title text-sm {{ $task->status === 'completed' ? 'line-through text-slate-400' : 'text-slate-700' }} transition-all duration-200">
                                        {{ $task->title }}
                                    </p>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($task->is_overdue_from)
                                            <span class="text-xs text-red-500 font-medium">
                                                ⚠ Undone from {{ $task->is_overdue_from->format('D, M j') }}
                                            </span>
                                        @endif
                                        @if($task->related_to && $task->related_to !== 'personal')
                                            <span class="text-xs text-slate-400">{{ $task->related_to_label }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Priority Badge --}}
                                @if($task->priority === 'high' && $task->status !== 'completed')
                                    <span class="shrink-0 bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full font-medium">!</span>
                                @endif

                                {{-- Actions --}}
                                <div class="task-actions shrink-0 flex items-center gap-0.5 opacity-0">
                                    @if($hasScheduledDate ?? false)
                                        <form action="{{ route('tasks.unschedule', $task) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="task-action-btn text-slate-400 hover:text-orange-500 p-1.5 rounded-full hover:bg-orange-50" title="Remove from Today">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('tasks.edit', $task) }}" class="task-action-btn text-slate-400 hover:text-blue-500 p-1.5 rounded-full hover:bg-blue-50" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="task-action-btn text-slate-400 hover:text-red-500 p-1.5 rounded-full hover:bg-red-50" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center">
                            <div class="text-slate-400 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-slate-500 text-sm">No tasks for today</p>
                            <p class="text-slate-400 text-xs mt-1">Add a task above or move one from weekly targets</p>
                        </div>
                    @endforelse
                </div>

                {{-- Today's Progress --}}
                @if($todaysTasks->count() > 0)
                    <div class="px-5 py-3 bg-slate-50 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                            <span>Progress</span>
                            <span>{{ $todaysTasks->where('status', 'completed')->count() }} / {{ $todaysTasks->count() }} completed</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            @php
                                $todayProgress = $todaysTasks->count() > 0 
                                    ? ($todaysTasks->where('status', 'completed')->count() / $todaysTasks->count()) * 100 
                                    : 0;
                            @endphp
                            <div class="progress-fill bg-blue-500 h-1.5 rounded-full" style="width: {{ $todayProgress }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: Weekly Targets --}}
        <div>
            {{-- Summary Cards --}}
            <div class="grid grid-cols-4 gap-2 mb-3">
                <div class="stat-card bg-white border border-slate-200 rounded-lg px-2 py-1.5 cursor-default">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 leading-none">{{ $pendingCount }}</p>
                            <p class="text-[10px] text-slate-500">Active</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-white border border-slate-200 rounded-lg px-2 py-1.5 cursor-default">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 {{ $overdueCount > 0 ? 'bg-red-100' : 'bg-slate-100' }} rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 {{ $overdueCount > 0 ? 'text-red-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold {{ $overdueCount > 0 ? 'text-red-600' : 'text-slate-800' }} leading-none">{{ $overdueCount }}</p>
                            <p class="text-[10px] text-slate-500">Overdue</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-white border border-slate-200 rounded-lg px-2 py-1.5 cursor-default">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 {{ $todayPendingCount > 0 ? 'bg-orange-100' : 'bg-slate-100' }} rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 {{ $todayPendingCount > 0 ? 'text-orange-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold {{ $todayPendingCount > 0 ? 'text-orange-600' : 'text-slate-800' }} leading-none">{{ $todayPendingCount }}</p>
                            <p class="text-[10px] text-slate-500">Today</p>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-white border border-slate-200 rounded-lg px-2 py-1.5 cursor-default">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-green-600 leading-none">{{ $completedCount }}</p>
                            <p class="text-[10px] text-slate-500">Done</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                {{-- Header --}}
                <div class="px-5 py-3 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-slate-100 rounded-lg p-2">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-700">Weekly Targets</h2>
                                <p class="text-slate-400 text-sm">{{ now()->startOfWeek()->format('M j') }} - {{ now()->endOfWeek()->format('M j') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-slate-600">{{ $weeklyCompletedCount }}</span>
                            <p class="text-slate-400 text-xs">of {{ $weeklyTotalCount }} done</p>
                        </div>
                    </div>
                </div>

                {{-- Quick Add for Weekly --}}
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <form action="{{ route('tasks.quick-store') }}" method="POST" class="flex gap-2">
                        @csrf
                        @if($hasWeeklyTarget ?? false)
                            <input type="hidden" name="is_weekly_target" value="1">
                        @endif
                        <input type="text" 
                               name="title" 
                               placeholder="Add a weekly target..." 
                               class="flex-1 text-sm border-slate-200 rounded-lg focus:ring-slate-400 focus:border-slate-400"
                               required>
                        <select name="due_date" class="text-sm border-slate-200 rounded-lg focus:ring-slate-400 focus:border-slate-400 bg-white">
                            @for($i = 0; $i < 7; $i++)
                                @php $dayDate = now()->startOfWeek()->addDays($i); @endphp
                                <option value="{{ $dayDate->toDateString() }}" {{ $dayDate->isToday() ? 'selected' : '' }}>
                                    {{ $dayDate->isToday() ? 'Today' : ($dayDate->isTomorrow() ? 'Tomorrow' : $dayDate->format('D, M j')) }}
                                </option>
                            @endfor
                        </select>
                        <button type="submit" class="bg-slate-500 hover:bg-slate-600 text-white px-3 py-2 rounded-lg text-sm">
                            Add
                        </button>
                    </form>
                </div>

                {{-- Weekly Task List - Categorized by Day --}}
                <div class="max-h-[500px] overflow-y-auto">
                    @php
                        $today = now()->startOfDay();
                        $weekDays = [];
                        for ($i = 0; $i < 7; $i++) {
                            $date = now()->startOfWeek()->addDays($i);
                            $weekDays[$date->format('Y-m-d')] = [
                                'label' => $date->isToday() ? 'Today' : ($date->isTomorrow() ? 'Tomorrow' : $date->format('l')),
                                'date' => $date->format('M j'),
                                'isToday' => $date->isToday(),
                                'isPast' => $date->lt($today),
                                'tasks' => $weeklyTasks->filter(function($task) use ($date) {
                                    $taskDate = $task->scheduled_date ?? $task->due_date;
                                    return $taskDate && $taskDate->format('Y-m-d') === $date->format('Y-m-d');
                                })
                            ];
                        }
                        // Tasks without a date
                        $noDateTasks = $weeklyTasks->filter(function($task) {
                            return !$task->scheduled_date && !$task->due_date;
                        });
                    @endphp

                    @foreach($weekDays as $dayKey => $day)
                        @if($day['tasks']->count() > 0)
                            <div class="border-b border-slate-100 last:border-b-0">
                                {{-- Day Header --}}
                                <div class="px-4 py-2 bg-slate-50 flex items-center justify-between sticky top-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold {{ $day['isToday'] ? 'text-blue-600' : ($day['isPast'] ? 'text-slate-400' : 'text-slate-600') }}">
                                            {{ $day['label'] }}
                                        </span>
                                        <span class="text-xs text-slate-400">{{ $day['date'] }}</span>
                                    </div>
                                    <span class="text-xs text-slate-400">
                                        {{ $day['tasks']->where('status', 'completed')->count() }}/{{ $day['tasks']->count() }}
                                    </span>
                                </div>
                                {{-- Tasks for this day --}}
                                @foreach($day['tasks'] as $task)
                                    <div class="task-item px-5 py-2 group {{ $task->status === 'completed' ? 'is-completed' : '' }}" data-task-id="{{ $task->id }}">
                                        <div class="flex items-center gap-3">
                                            {{-- Checkbox --}}
                                            <div class="task-checkbox-wrapper">
                                                <label class="relative flex items-center cursor-pointer">
                                                    <input type="checkbox" 
                                                           class="sr-only task-toggle"
                                                           data-task-id="{{ $task->id }}"
                                                           {{ $task->status === 'completed' ? 'checked' : '' }}>
                                                    <div class="task-checkbox w-4 h-4 border-2 {{ $task->status === 'completed' ? 'bg-green-500 border-green-500 checked' : 'border-slate-300 hover:border-slate-400 hover:bg-slate-50' }} flex items-center justify-center transition-all duration-150">
                                                        <svg class="w-2.5 h-2.5 text-white {{ $task->status === 'completed' ? '' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>
                                                </label>
                                            </div>

                                            {{-- Task Content --}}
                                            <div class="flex-1 min-w-0">
                                                <p class="task-title text-sm {{ $task->status === 'completed' ? 'line-through text-slate-400' : 'text-slate-700' }} transition-all duration-200">
                                                    {{ $task->title }}
                                                </p>
                                            </div>

                                            {{-- Priority & Actions --}}
                                            <div class="shrink-0 flex items-center gap-1">
                                                @if($task->priority === 'high' && $task->status !== 'completed')
                                                    <span class="bg-red-100 text-red-600 text-[10px] px-1.5 py-0.5 rounded font-medium">!</span>
                                                @endif

                                                <div class="task-actions flex items-center gap-0.5 opacity-0">
                                                    @if($task->status !== 'completed' && ($hasScheduledDate ?? false) && !$day['isToday'])
                                                        <form action="{{ route('tasks.schedule-today', $task) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="task-action-btn text-blue-500 hover:text-blue-700 p-1 rounded hover:bg-blue-50" title="Move to Today">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('tasks.edit', $task) }}" class="task-action-btn text-slate-400 hover:text-blue-500 p-1 rounded hover:bg-blue-50" title="Edit">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="task-action-btn text-slate-400 hover:text-red-500 p-1 rounded hover:bg-red-50" title="Delete">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach

                    {{-- Tasks without date --}}
                    @if($noDateTasks->count() > 0)
                        <div class="border-b border-slate-100 last:border-b-0">
                            <div class="px-4 py-2 bg-slate-50 flex items-center justify-between sticky top-0">
                                <span class="text-xs font-semibold text-slate-500">No Date Set</span>
                                <span class="text-xs text-slate-400">{{ $noDateTasks->count() }}</span>
                            </div>
                            @foreach($noDateTasks as $task)
                                <div class="task-item px-5 py-2 group {{ $task->status === 'completed' ? 'is-completed' : '' }}" data-task-id="{{ $task->id }}">
                                    <div class="flex items-center gap-3">
                                        <div class="task-checkbox-wrapper">
                                            <label class="relative flex items-center cursor-pointer">
                                                <input type="checkbox" class="sr-only task-toggle" data-task-id="{{ $task->id }}" {{ $task->status === 'completed' ? 'checked' : '' }}>
                                                <div class="task-checkbox w-4 h-4 border-2 {{ $task->status === 'completed' ? 'bg-green-500 border-green-500 checked' : 'border-slate-300 hover:border-slate-400' }} flex items-center justify-center transition-all duration-150">
                                                    <svg class="w-2.5 h-2.5 text-white {{ $task->status === 'completed' ? '' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="task-title text-sm {{ $task->status === 'completed' ? 'line-through text-slate-400' : 'text-slate-700' }}">{{ $task->title }}</p>
                                        </div>
                                        <div class="task-actions flex items-center gap-0.5 opacity-0">
                                            <a href="{{ route('tasks.edit', $task) }}" class="task-action-btn text-slate-400 hover:text-blue-500 p-1 rounded hover:bg-blue-50" title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($weeklyTasks->count() === 0)
                        <div class="px-5 py-8 text-center">
                            <div class="text-slate-400 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-slate-500 text-sm">No weekly targets set</p>
                            <p class="text-slate-400 text-xs mt-1">Add your goals for this week above</p>
                        </div>
                    @endif
                </div>

                {{-- Weekly Progress --}}
                @if($weeklyTotalCount > 0)
                    <div class="px-5 py-3 bg-slate-50 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                            <span>Week Progress</span>
                            <span>{{ $weeklyCompletedCount }} / {{ $weeklyTotalCount }} completed</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            @php
                                $weekProgress = $weeklyTotalCount > 0 
                                    ? ($weeklyCompletedCount / $weeklyTotalCount) * 100 
                                    : 0;
                            @endphp
                            <div class="progress-fill bg-slate-400 h-1.5 rounded-full" style="width: {{ $weekProgress }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Overdue Tasks Alert --}}
            @if($overdueCount > 0)
                <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-100 rounded-lg p-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-red-700">{{ $overdueCount }} overdue {{ Str::plural('task', $overdueCount) }}</p>
                            <p class="text-xs text-red-500">Review and reschedule or complete</p>
                        </div>
                        <a href="{{ route('tasks.index', ['view' => 'all']) }}" class="text-red-600 text-sm hover:underline">View All →</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @elseif($view === 'all' || $view === 'completed')
    {{-- All Tasks / Completed View --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Quick Add --}}
        @if($view === 'all')
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <form action="{{ route('tasks.quick-store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" 
                       name="title" 
                       placeholder="Add a new task..." 
                       class="flex-1 text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       required>
                <select name="priority" class="text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
                <input type="date" name="due_date" class="text-sm border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                    Add
                </button>
            </form>
        </div>
        @endif

        {{-- Task List --}}
        <div class="divide-y divide-slate-100">
            @forelse($allTasks as $task)
                <div class="task-item px-5 py-2.5 group {{ $task->status === 'completed' ? 'is-completed' : '' }}" data-task-id="{{ $task->id }}">
                    <div class="flex items-center gap-3">
                        {{-- Checkbox --}}
                        <div class="task-checkbox-wrapper">
                            <label class="relative flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       class="sr-only task-toggle"
                                       data-task-id="{{ $task->id }}"
                                       {{ $task->status === 'completed' ? 'checked' : '' }}>
                                <div class="task-checkbox w-5 h-5 border-2 {{ $task->status === 'completed' ? 'bg-green-500 border-green-500 checked' : 'border-slate-300 hover:border-green-400 hover:bg-green-50' }} flex items-center justify-center transition-all duration-150">
                                    <svg class="w-3 h-3 text-white {{ $task->status === 'completed' ? '' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </label>
                        </div>

                        {{-- Task Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="task-title text-sm {{ $task->status === 'completed' ? 'line-through text-slate-400' : 'text-slate-700' }} transition-all duration-200">
                                {{ $task->title }}
                            </p>
                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                @if($task->due_date)
                                    <span class="text-xs {{ $task->is_overdue && $task->status !== 'completed' ? 'text-red-500 font-medium' : 'text-slate-400' }}">
                                        @if($task->is_overdue && $task->status !== 'completed')
                                            Overdue: {{ $task->due_date->format('M j') }}
                                        @else
                                            {{ $task->due_date->format('D, M j') }}
                                        @endif
                                    </span>
                                @endif
                                @if($task->related_to && $task->related_to !== 'personal')
                                    <span class="text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                                        {{ $task->related_to_label }}
                                    </span>
                                @endif
                                @if($task->status === 'completed' && $task->completed_at)
                                    <span class="text-xs text-green-500">
                                        ✓ {{ $task->completed_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Priority & Actions --}}
                        <div class="shrink-0 flex items-center gap-1">
                            @if($task->priority === 'high' && $task->status !== 'completed')
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full font-medium mr-1">!</span>
                            @elseif($task->priority === 'medium' && $task->status !== 'completed')
                                <span class="bg-yellow-100 text-yellow-600 text-xs px-1.5 py-0.5 rounded-full text-[10px]">M</span>
                            @endif

                            <div class="task-actions flex items-center gap-0.5 opacity-0">
                                <a href="{{ route('tasks.edit', $task) }}" 
                                   class="task-action-btn text-slate-400 hover:text-blue-500 p-1.5 rounded-full hover:bg-blue-50" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                                
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="task-action-btn text-slate-400 hover:text-red-500 p-1.5 rounded-full hover:bg-red-50" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <div class="text-slate-400 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    @if($view === 'completed')
                        <p class="text-slate-500 text-sm">No completed tasks yet</p>
                        <p class="text-slate-400 text-xs mt-1">Tasks you complete will appear here</p>
                    @else
                        <p class="text-slate-500 text-sm">No active tasks</p>
                        <p class="text-slate-400 text-xs mt-1">Add your first task using the form above</p>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
    @endif

    {{-- Migration Notice --}}
    @if(!($hasScheduledDate ?? true) || !($hasWeeklyTarget ?? true))
    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <div class="bg-yellow-100 rounded-lg p-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-yellow-700">Database migration needed</p>
                <p class="text-xs text-yellow-600">Run <code class="bg-yellow-100 px-1 rounded">php artisan migrate</code> to enable daily scheduling and weekly targets features.</p>
            </div>
        </div>
    </div>
    @endif
</div>

@if(session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         x-transition
         class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg text-sm">
        {{ session('success') }}
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Helper function to update task UI
    function updateTaskUI(taskItem, isCompleting) {
        const checkboxDiv = taskItem.querySelector('.task-checkbox');
        const checkIcon = checkboxDiv.querySelector('svg');
        const taskTitle = taskItem.querySelector('.task-title');
        const checkbox = taskItem.querySelector('.task-toggle');
        
        checkbox.checked = isCompleting;
        
        if (isCompleting) {
            checkboxDiv.classList.add('bg-green-500', 'border-green-500', 'checked');
            checkboxDiv.classList.remove('border-slate-300', 'hover:border-blue-400', 'hover:bg-blue-50', 'hover:border-slate-400', 'hover:bg-slate-50', 'hover:border-green-400', 'hover:bg-green-50');
            checkIcon.classList.remove('hidden');
            taskTitle.classList.add('line-through', 'text-slate-400');
            taskTitle.classList.remove('text-slate-700');
            taskItem.classList.add('is-completed');
        } else {
            checkboxDiv.classList.remove('bg-green-500', 'border-green-500', 'checked');
            checkboxDiv.classList.add('border-slate-300');
            checkIcon.classList.add('hidden');
            taskTitle.classList.remove('line-through', 'text-slate-400');
            taskTitle.classList.add('text-slate-700');
            taskItem.classList.remove('is-completed');
        }
    }
    
    // Make checkbox wrapper clickable
    document.querySelectorAll('.task-checkbox-wrapper').forEach(function(wrapper) {
        wrapper.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const checkbox = this.querySelector('.task-toggle');
            const taskId = checkbox.dataset.taskId;
            const isCompleting = !checkbox.checked;
            
            // Update ALL task items with the same ID (syncs Today & Weekly)
            document.querySelectorAll(`.task-item[data-task-id="${taskId}"]`).forEach(function(taskItem) {
                updateTaskUI(taskItem, isCompleting);
            });
            
            // Send AJAX request
            fetch(`/my-tasks/${taskId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Task updated:', data.status);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert ALL on error
                document.querySelectorAll(`.task-item[data-task-id="${taskId}"]`).forEach(function(taskItem) {
                    updateTaskUI(taskItem, !isCompleting);
                });
            });
        });
    });
});
</script>
@endpush
@endsection
