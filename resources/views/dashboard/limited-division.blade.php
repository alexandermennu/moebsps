@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard — ' . ($user->division?->name ?? 'Division'))

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Assignments</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">In Progress</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Completed</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Overdue</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue'] }}</p>
        </div>
    </div>

    {{-- Recent Assignments --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Division Assignments</h3>
        <div class="space-y-2">
            @forelse($recentActivities as $activity)
                <a href="{{ route('activities.show', $activity) }}" class="block p-3 border border-gray-100 rounded-md hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $activity->title }}</p>
                            <p class="text-xs text-gray-500">{{ $activity->assignee?->name ?? 'Unassigned' }} · {{ $activity->status }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full {{ $activity->status_badge_color }}">{{ ucfirst(str_replace('_', ' ', $activity->status)) }}</span>
                    </div>
                </a>
            @empty
                <p class="text-sm text-gray-500">No assignments in your division.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
