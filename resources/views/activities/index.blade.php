@extends('layouts.app')

@section('title', 'Assignments')
@section('page-title', 'Assignments')

@php
    $isFilteredByDivision = request()->filled('division_id');
    $filteredDivisionName = null;
    if ($isFilteredByDivision) {
        if (request('division_id') === 'minister') {
            $filteredDivisionName = 'Office of the Minister';
        } else {
            $filteredDivisionName = $divisions->firstWhere('id', request('division_id'))?->name ?? 'Division';
        }
    }
@endphp

@section('content')
<div class="space-y-4">
    @if($isFilteredByDivision)
        {{-- Filtered by Division View --}}
        <div class="flex items-center justify-between border-b border-gray-300 pb-4">
            <div>
                <a href="{{ route('activities.index') }}" class="text-xs text-blue-700 hover:underline mb-2 inline-block">← Back to All Assignments</a>
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ $filteredDivisionName }} Assignments</h2>
                <p class="text-xs text-gray-500 mt-1">{{ $activities->total() }} assignment(s) found</p>
            </div>
            @if($user->canManageDivision())
                <a href="{{ route('activities.create') }}" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">+ New Assignment</a>
            @endif
        </div>

        {{-- Status Filter for Filtered View --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('activities.index', ['division_id' => request('division_id')]) }}" 
               class="px-3 py-1.5 text-xs font-medium border {{ !request('status') ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                All
            </a>
            <a href="{{ route('activities.index', ['division_id' => request('division_id'), 'status' => 'in_progress']) }}" 
               class="px-3 py-1.5 text-xs font-medium border {{ request('status') === 'in_progress' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                In Progress
            </a>
            <a href="{{ route('activities.index', ['division_id' => request('division_id'), 'status' => 'not_started']) }}" 
               class="px-3 py-1.5 text-xs font-medium border {{ request('status') === 'not_started' ? 'bg-gray-600 text-white border-gray-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                Not Started
            </a>
            <a href="{{ route('activities.index', ['division_id' => request('division_id'), 'status' => 'overdue']) }}" 
               class="px-3 py-1.5 text-xs font-medium border {{ request('status') === 'overdue' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                Overdue
            </a>
            <a href="{{ route('activities.index', ['division_id' => request('division_id'), 'status' => 'completed']) }}" 
               class="px-3 py-1.5 text-xs font-medium border {{ request('status') === 'completed' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                Completed
            </a>
        </div>
    @else
        {{-- Default View --}}
        <div class="flex items-center justify-between border-b border-gray-300 pb-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Assignments</h2>
                <p class="text-xs text-gray-500 mt-1">Track and manage all bureau assignments</p>
            </div>
            @if($user->canManageDivision())
                <a href="{{ route('activities.create') }}" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">+ New Assignment</a>
            @endif
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search assignments..."
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm w-48">
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Statuses</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Not Started</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Priority</label>
                <select name="priority" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm hover:bg-gray-200">Filter</button>
            @if(request()->hasAny(['search', 'status', 'priority']))
                <a href="{{ route('activities.index') }}" class="text-xs text-blue-700 hover:underline py-2">Clear</a>
            @endif
        </form>
    @endif

    {{-- Activities Table --}}
    <div class="bg-white border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Assignment</th>
                        @if(!$isFilteredByDivision)
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Division</th>
                        @endif
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Assigned To</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Priority</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Status</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Progress</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Due Date</th>
                        <th class="text-right px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50 {{ $activity->is_overdue ? 'bg-red-50' : '' }}">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $activity->title }}</p>
                                @if($activity->is_escalated)
                                    <span class="text-[10px] text-orange-700 font-medium uppercase">Escalated</span>
                                @endif
                            </td>
                            @if(!$isFilteredByDivision)
                            <td class="px-5 py-3 text-gray-600">{{ $activity->division?->name ?? 'Office of the Minister' }}</td>
                            @endif
                            <td class="px-5 py-3 text-gray-600">{{ $activity->assignee?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-[10px] px-1.5 py-0.5 font-medium
                                    {{ $activity->priority === 'critical' ? 'bg-red-50 text-red-700' : '' }}
                                    {{ $activity->priority === 'high' ? 'bg-orange-50 text-orange-700' : '' }}
                                    {{ $activity->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                    {{ $activity->priority === 'low' ? 'bg-gray-100 text-gray-600' : '' }}">
                                    {{ ucfirst($activity->priority) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-[10px] px-1.5 py-0.5 font-medium
                                    {{ $activity->status === 'completed' ? 'bg-green-50 text-green-700' : '' }}
                                    {{ $activity->status === 'in_progress' ? 'bg-blue-50 text-blue-700' : '' }}
                                    {{ $activity->status === 'overdue' ? 'bg-red-50 text-red-700' : '' }}
                                    {{ $activity->status === 'not_started' ? 'bg-gray-100 text-gray-600' : '' }}">
                                    {{ str_replace('_', ' ', ucfirst($activity->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 bg-gray-200 h-1.5">
                                        <div class="bg-gray-600 h-1.5" style="width: {{ $activity->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $activity->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 {{ $activity->is_overdue ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                {{ $activity->due_date->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('activities.show', $activity) }}" class="text-xs text-blue-700 hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isFilteredByDivision ? 7 : 8 }}" class="px-5 py-8 text-center text-gray-500">No assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $activities->links() }}

    {{-- Division Summary Cards (only on main view) --}}
    @if($user->hasFullAccess() && !$isFilteredByDivision)
    <div class="bg-white border border-gray-200 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">By Division</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            @foreach($divisionStats as $key => $stats)
                <a href="{{ route('activities.index', ['division_id' => $key !== 'minister' ? $key : 'minister']) }}" 
                   class="block border border-gray-200 p-3 hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-semibold text-gray-500 uppercase">{{ $stats['code'] ?? Str::limit($stats['name'], 15) }}</span>
                        <span class="text-lg font-bold text-gray-800">{{ $stats['total'] }}</span>
                    </div>
                    <p class="text-xs text-gray-600 truncate mb-2">{{ $stats['name'] }}</p>
                    <div class="flex flex-wrap gap-2 text-[10px]">
                        @if($stats['in_progress'] > 0)
                            <span class="text-blue-600">{{ $stats['in_progress'] }} active</span>
                        @endif
                        @if($stats['overdue'] > 0)
                            <span class="text-red-600 font-medium">{{ $stats['overdue'] }} overdue</span>
                        @endif
                        @if($stats['completed'] > 0)
                            <span class="text-green-600">{{ $stats['completed'] }} done</span>
                        @endif
                        @if($stats['not_started'] > 0)
                            <span class="text-gray-500">{{ $stats['not_started'] }} pending</span>
                        @endif
                        @if($stats['total'] == 0)
                            <span class="text-gray-400">No assignments</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
