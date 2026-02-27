@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Assigned to Me</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['assigned_to_me'] }}</p>
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

    {{-- My Assignments --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">My Tasks</h3>
        <div class="space-y-2">
            @forelse($myActivities as $activity)
                <a href="{{ route('activities.show', $activity) }}" class="block p-3 border border-gray-100 rounded-md hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $activity->title }}</p>
                            <p class="text-xs text-gray-500">{{ $activity->division?->name }} · Due {{ $activity->due_date?->format('M d, Y') ?? 'No date' }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full {{ $activity->status_badge_color }}">{{ ucfirst(str_replace('_', ' ', $activity->status)) }}</span>
                    </div>
                    @if($activity->progress_percentage > 0)
                        <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ $activity->progress_percentage }}%"></div>
                        </div>
                    @endif
                </a>
            @empty
                <p class="text-sm text-gray-500">No tasks assigned to you yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
