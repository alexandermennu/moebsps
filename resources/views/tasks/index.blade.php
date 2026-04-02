@extends('layouts.app')

@section('title', 'My Tasks')
@section('page-title', 'My Tasks')

@section('content')
<div class="space-y-5">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">My Tasks</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your personal tasks and to-dos</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium hover:bg-green-700 rounded">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Task
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                    <p class="text-sm text-gray-500">Pending Tasks</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold {{ $overdueCount > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $overdueCount }}</p>
                    <p class="text-sm text-gray-500">Overdue</p>
                </div>
                <div class="w-10 h-10 {{ $overdueCount > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $overdueCount > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold {{ $dueTodayCount > 0 ? 'text-orange-600' : 'text-gray-900' }}">{{ $dueTodayCount }}</p>
                    <p class="text-sm text-gray-500">Due Today</p>
                </div>
                <div class="w-10 h-10 {{ $dueTodayCount > 0 ? 'bg-orange-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $dueTodayCount > 0 ? 'text-orange-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $completedCount }}</p>
                    <p class="text-sm text-gray-500">Completed</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('tasks.index') }}" class="flex items-end gap-4">
        <div class="w-40">
            <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <div class="w-44">
            <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Related To</label>
            <select name="related_to" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">All Categories</option>
                @foreach($relatedToOptions as $value => $label)
                    <option value="{{ $value }}" {{ request('related_to') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-36">
            <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Priority</label>
            <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="">All Priorities</option>
                @foreach($priorityOptions as $value => $label)
                    <option value="{{ $value }}" {{ request('priority') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1">
            <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Search</label>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tasks..."
                    class="w-full px-3 py-2 pl-8 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500">
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded hover:bg-gray-700">
            Filter
        </button>
        
        @if(request()->hasAny(['status', 'related_to', 'priority', 'search']) && (request('status') != 'active' || request('related_to') || request('priority') || request('search')))
        <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded hover:bg-gray-50">
            Reset
        </a>
        @endif
    </form>

    {{-- Quick Add Task --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <form action="{{ route('tasks.quick-store') }}" method="POST" class="flex items-center gap-3">
            @csrf
            <div class="flex-1">
                <input type="text" name="title" placeholder="Quick add a task... Press Enter to save" required
                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
            </div>
            <div class="w-36">
                <select name="related_to" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500">
                    @foreach($relatedToOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                Add
            </button>
        </form>
    </div>

    {{-- Tasks List --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        @if($tasks->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($tasks as $task)
                    <div class="p-4 hover:bg-gray-50 {{ $task->status === 'completed' ? 'opacity-60' : '' }}">
                        <div class="flex items-start gap-3">
                            {{-- Checkbox --}}
                            <form action="{{ route('tasks.toggle-complete', $task) }}" method="POST" class="mt-0.5">
                                @csrf
                                <button type="submit" class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors
                                    {{ $task->status === 'completed' ? 'bg-green-500 border-green-500' : 'border-gray-300 hover:border-green-500' }}">
                                    @if($task->status === 'completed')
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            </form>

                            {{-- Task Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-medium {{ $task->status === 'completed' ? 'text-gray-500 line-through' : 'text-gray-900' }}">
                                        {{ $task->title }}
                                    </h3>
                                    
                                    {{-- Priority Badge --}}
                                    @if($task->priority === 'high')
                                        <span class="inline-flex px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded">High</span>
                                    @elseif($task->priority === 'medium')
                                        <span class="inline-flex px-1.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded">Medium</span>
                                    @endif

                                    {{-- Overdue Badge --}}
                                    @if($task->is_overdue)
                                        <span class="inline-flex px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded">Overdue</span>
                                    @elseif($task->is_due_today)
                                        <span class="inline-flex px-1.5 py-0.5 text-xs font-medium bg-orange-100 text-orange-700 rounded">Due Today</span>
                                    @endif
                                </div>

                                @if($task->description)
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $task->description }}</p>
                                @endif

                                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                    {{-- Related To --}}
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        {{ $task->related_to_label }}
                                    </span>

                                    {{-- Due Date --}}
                                    @if($task->due_date)
                                        <span class="inline-flex items-center gap-1 {{ $task->is_overdue ? 'text-red-600' : '' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @endif

                                    {{-- Status --}}
                                    @if($task->status === 'in_progress')
                                        <span class="inline-flex items-center gap-1 text-blue-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            In Progress
                                        </span>
                                    @elseif($task->status === 'completed')
                                        <span class="inline-flex items-center gap-1 text-green-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Completed {{ $task->completed_at ? $task->completed_at->diffForHumans() : '' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tasks.edit', $task) }}" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <p class="text-gray-500">No tasks found</p>
                <p class="text-sm text-gray-400 mt-1">
                    @if(request('status') === 'completed')
                        You haven't completed any tasks yet.
                    @else
                        Add your first task using the form above!
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
