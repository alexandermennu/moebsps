@extends('layouts.app')

@section('title', 'My Tasks')
@section('page-title', 'My Tasks')

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

    {{-- Summary Cards --}}
    <div class="grid grid-cols-4 gap-4 mb-4">
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $pendingCount }}</p>
                    <p class="text-sm text-slate-500">Active Tasks</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold {{ $overdueCount > 0 ? 'text-red-600' : 'text-slate-800' }}">{{ $overdueCount }}</p>
                    <p class="text-sm text-slate-500">Overdue</p>
                </div>
                <div class="w-10 h-10 {{ $overdueCount > 0 ? 'bg-red-100' : 'bg-slate-100' }} rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $overdueCount > 0 ? 'text-red-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold {{ $todayPendingCount > 0 ? 'text-orange-600' : 'text-slate-800' }}">{{ $todayPendingCount }}</p>
                    <p class="text-sm text-slate-500">Due Today</p>
                </div>
                <div class="w-10 h-10 {{ $todayPendingCount > 0 ? 'bg-orange-100' : 'bg-slate-100' }} rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $todayPendingCount > 0 ? 'text-orange-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $completedCount }}</p>
                    <p class="text-sm text-slate-500">Completed</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- View Tabs --}}
    <div class="flex items-center gap-2 mb-4 border-b border-slate-200">
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
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
                        <div class="task-item px-5 py-3 hover:bg-slate-50 transition-colors group {{ $task->status === 'completed' ? 'bg-slate-50' : '' }}">
                            <div class="flex items-start gap-3">
                                {{-- Checkbox --}}
                                <form action="{{ route('tasks.toggle-complete', $task) }}" method="POST" class="mt-0.5">
                                    @csrf
                                    <label class="relative flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               class="sr-only"
                                               {{ $task->status === 'completed' ? 'checked' : '' }}
                                               onchange="this.form.submit()">
                                        <div class="w-5 h-5 border-2 rounded {{ $task->status === 'completed' ? 'bg-blue-500 border-blue-500' : 'border-slate-300 hover:border-blue-400' }} flex items-center justify-center transition-all">
                                            @if($task->status === 'completed')
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </label>
                                </form>

                                {{-- Task Content --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm {{ $task->status === 'completed' ? 'line-through text-slate-400' : 'text-slate-700' }}">
                                        {{ $task->title }}
                                    </p>
                                    @if($task->related_to && $task->related_to !== 'personal')
                                        <span class="text-xs text-slate-400">{{ $task->related_to_label }}</span>
                                    @endif
                                </div>

                                {{-- Priority Badge --}}
                                @if($task->priority === 'high' && $task->status !== 'completed')
                                    <span class="shrink-0 bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">High</span>
                                @endif

                                {{-- Actions --}}
                                <div class="shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if($hasScheduledDate ?? false)
                                        <form action="{{ route('tasks.unschedule', $task) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-slate-400 hover:text-orange-500 p-1" title="Remove from Today">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('tasks.edit', $task) }}" class="text-slate-400 hover:text-slate-600 p-1" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-500 p-1" title="Delete">
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
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            @php
                                $todayProgress = $todaysTasks->count() > 0 
                                    ? ($todaysTasks->where('status', 'completed')->count() / $todaysTasks->count()) * 100 
                                    : 0;
                            @endphp
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: {{ $todayProgress }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: Weekly Targets --}}
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 rounded-lg p-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Weekly Targets</h2>
                                <p class="text-purple-100 text-sm">{{ now()->startOfWeek()->format('M j') }} - {{ now()->endOfWeek()->format('M j') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-white">{{ $weeklyCompletedCount }}</span>
                            <p class="text-purple-100 text-xs">of {{ $weeklyTotalCount }} done</p>
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
                        <input type="hidden" name="due_date" value="{{ now()->endOfWeek()->toDateString() }}">
                        <input type="text" 
                               name="title" 
                               placeholder="Add a weekly target..." 
                               class="flex-1 text-sm border-slate-200 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                               required>
                        <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-lg text-sm">
                            Add
                        </button>
                    </form>
                </div>

                {{-- Weekly Task List --}}
                <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                    @forelse($weeklyTasks as $task)
                        <div class="task-item px-5 py-3 hover:bg-slate-50 transition-colors group {{ $task->status === 'completed' ? 'bg-slate-50' : '' }}">
                            <div class="flex items-start gap-3">
                                {{-- Checkbox --}}
                                <form action="{{ route('tasks.toggle-complete', $task) }}" method="POST" class="mt-0.5">
                                    @csrf
                                    <label class="relative flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               class="sr-only"
                                               {{ $task->status === 'completed' ? 'checked' : '' }}
                                               onchange="this.form.submit()">
                                        <div class="w-5 h-5 border-2 rounded {{ $task->status === 'completed' ? 'bg-purple-500 border-purple-500' : 'border-slate-300 hover:border-purple-400' }} flex items-center justify-center transition-all">
                                            @if($task->status === 'completed')
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </label>
                                </form>

                                {{-- Task Content --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm {{ $task->status === 'completed' ? 'line-through text-slate-400' : 'text-slate-700' }}">
                                        {{ $task->title }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if($task->due_date)
                                            <span class="text-xs {{ $task->is_overdue ? 'text-red-500' : 'text-slate-400' }}">
                                                Due {{ $task->due_date->format('D, M j') }}
                                            </span>
                                        @endif
                                        @if($task->related_to && $task->related_to !== 'personal')
                                            <span class="text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                                                {{ $task->related_to_label }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Priority & Actions --}}
                                <div class="shrink-0 flex items-center gap-1">
                                    @if($task->priority === 'high' && $task->status !== 'completed')
                                        <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full mr-1">High</span>
                                    @endif

                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{-- Move to Today Button --}}
                                        @if($task->status !== 'completed' && ($hasScheduledDate ?? false))
                                            <form action="{{ route('tasks.schedule-today', $task) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-purple-500 hover:text-purple-700 p-1"
                                                        title="Move to Today">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('tasks.edit', $task) }}" 
                                           class="text-slate-400 hover:text-slate-600 p-1" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>
                                        
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-500 p-1" title="Delete">
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
                        <div class="px-5 py-8 text-center">
                            <div class="text-slate-400 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-slate-500 text-sm">No weekly targets set</p>
                            <p class="text-slate-400 text-xs mt-1">Add your goals for this week above</p>
                        </div>
                    @endforelse
                </div>

                {{-- Weekly Progress --}}
                @if($weeklyTotalCount > 0)
                    <div class="px-5 py-3 bg-slate-50 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                            <span>Week Progress</span>
                            <span>{{ $weeklyCompletedCount }} / {{ $weeklyTotalCount }} completed</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            @php
                                $weekProgress = $weeklyTotalCount > 0 
                                    ? ($weeklyCompletedCount / $weeklyTotalCount) * 100 
                                    : 0;
                            @endphp
                            <div class="bg-purple-500 h-2 rounded-full transition-all duration-300" style="width: {{ $weekProgress }}%"></div>
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
                <div class="task-item px-5 py-3 hover:bg-slate-50 transition-colors group {{ $task->status === 'completed' ? 'bg-slate-50' : '' }}">
                    <div class="flex items-start gap-3">
                        {{-- Checkbox --}}
                        <form action="{{ route('tasks.toggle-complete', $task) }}" method="POST" class="mt-0.5">
                            @csrf
                            <label class="relative flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       class="sr-only"
                                       {{ $task->status === 'completed' ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <div class="w-5 h-5 border-2 rounded {{ $task->status === 'completed' ? 'bg-green-500 border-green-500' : 'border-slate-300 hover:border-green-400' }} flex items-center justify-center transition-all">
                                    @if($task->status === 'completed')
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>
                            </label>
                        </form>

                        {{-- Task Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm {{ $task->status === 'completed' ? 'line-through text-slate-400' : 'text-slate-700' }}">
                                {{ $task->title }}
                            </p>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                @if($task->due_date)
                                    <span class="text-xs {{ $task->is_overdue && $task->status !== 'completed' ? 'text-red-500 font-medium' : 'text-slate-400' }}">
                                        @if($task->is_overdue && $task->status !== 'completed')
                                            Overdue: {{ $task->due_date->format('M j') }}
                                        @else
                                            Due {{ $task->due_date->format('D, M j') }}
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
                                        ✓ Completed {{ $task->completed_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Priority & Actions --}}
                        <div class="shrink-0 flex items-center gap-2">
                            @if($task->priority === 'high' && $task->status !== 'completed')
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">High</span>
                            @elseif($task->priority === 'medium' && $task->status !== 'completed')
                                <span class="bg-yellow-100 text-yellow-600 text-xs px-2 py-0.5 rounded-full">Med</span>
                            @endif

                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('tasks.edit', $task) }}" 
                                   class="text-slate-400 hover:text-slate-600 p-1" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                                
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 p-1" title="Delete">
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
@endsection
