@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Divisions</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_divisions'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Activities</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Overdue</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Completion Rate</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completion_rate'] }}%</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Escalated</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['escalated_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Pending Updates</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['pending_updates'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Pending Plans</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['pending_plans'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Active Users</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_users'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Division Performance --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Division Performance</h3>
            <div class="space-y-3">
                @foreach($divisions as $division)
                    @php $rate = $division->activities_count > 0 ? round(($division->completed_count / $division->activities_count) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ $division->name }}</span>
                            <span class="text-gray-500">{{ $rate }}% ({{ $division->completed_count }}/{{ $division->activities_count }})</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $rate >= 70 ? 'bg-green-500' : ($rate >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $rate }}%"></div>
                        </div>
                        @if($division->overdue_count > 0)
                            <p class="text-xs text-red-500 mt-0.5">{{ $division->overdue_count }} overdue</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Escalated Activities --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Escalated Activities</h3>
            @if($escalatedActivities->isEmpty())
                <p class="text-sm text-gray-500">No escalated activities.</p>
            @else
                <div class="space-y-3">
                    @foreach($escalatedActivities as $activity)
                        <a href="{{ route('activities.show', $activity) }}" class="block p-3 bg-orange-50 border border-orange-200 rounded-md hover:bg-orange-100">
                            <p class="text-sm font-medium text-gray-800">{{ $activity->title }}</p>
                            <p class="text-xs text-gray-500">{{ $activity->division?->name }} · Escalated {{ $activity->escalated_at?->diffForHumans() }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Pending Reviews --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Pending Reviews</h3>
            @if($pendingReviews->isEmpty())
                <p class="text-sm text-gray-500">No pending reviews.</p>
            @else
                <div class="space-y-2">
                    @foreach($pendingReviews as $update)
                        <a href="{{ route('weekly-updates.show', $update) }}" class="block p-3 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100">
                            <p class="text-sm font-medium text-gray-800">{{ $update->division?->name }}</p>
                            <p class="text-xs text-gray-500">By {{ $update->submitter?->name }} · {{ $update->created_at->diffForHumans() }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Admin Quick Actions --}}
        @if($user->isAdmin())
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.users.create') }}" class="block p-3 bg-gray-50 border border-gray-200 rounded-md hover:bg-gray-100 text-sm text-gray-700">+ Add New User</a>
                <a href="{{ route('admin.divisions.create') }}" class="block p-3 bg-gray-50 border border-gray-200 rounded-md hover:bg-gray-100 text-sm text-gray-700">+ Add New Division</a>
                <a href="{{ route('admin.settings.index') }}" class="block p-3 bg-gray-50 border border-gray-200 rounded-md hover:bg-gray-100 text-sm text-gray-700">⚙ System Settings</a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
