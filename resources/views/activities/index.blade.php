@extends('layouts.app')

@section('title', 'Assignments')
@section('page-title', 'Assignments')

@section('content')
<div class="space-y-4">
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
        @if(!$user->isDirector())
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Division</label>
                <select name="division_id" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Divisions</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm hover:bg-gray-200">Filter</button>
        @if(request()->hasAny(['search', 'status', 'priority', 'division_id']))
            <a href="{{ route('activities.index') }}" class="text-xs text-blue-700 hover:underline py-2">Clear</a>
        @endif
    </form>

    {{-- Activities Table --}}
    <div class="bg-white border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Assignment</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Division</th>
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
                            <td class="px-5 py-3 text-gray-600">{{ $activity->division->name }}</td>
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
                            <td colspan="8" class="px-5 py-8 text-center text-gray-500">No assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $activities->links() }}
</div>
@endsection
