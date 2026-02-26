@extends('layouts.app')

@section('title', 'Director Dashboard')
@section('page-title', 'Division Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Division Name --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800">{{ $user->division?->name ?? 'My Division' }}</h2>
        <p class="text-sm text-gray-500">Welcome back, {{ $user->name }}</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Activities</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_activities'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Overdue</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Draft Updates</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['pending_updates'] }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Draft Plans</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['pending_plans'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="flex gap-3">
        <a href="{{ route('weekly-updates.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">
            + New Weekly Update
        </a>
        <a href="{{ route('weekly-plans.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
            + New Weekly Plan
        </a>
        <a href="{{ route('activities.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
            + New Activity
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Activities --}}
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Recent Activities</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentActivities as $activity)
                    <a href="{{ route('activities.show', $activity) }}" class="block px-5 py-3 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-800">{{ $activity->title }}</p>
                            <span class="text-xs px-2 py-1 rounded-full
                                {{ $activity->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $activity->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $activity->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $activity->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ str_replace('_', ' ', ucfirst($activity->status)) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Due: {{ $activity->due_date->format('M d, Y') }}</p>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500">No activities yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Overdue Activities --}}
        <div class="bg-white rounded-lg border border-red-200">
            <div class="px-5 py-4 border-b border-red-200 bg-red-50">
                <h3 class="font-semibold text-red-800">⚠ Overdue Activities</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($overdueActivities as $activity)
                    <a href="{{ route('activities.show', $activity) }}" class="block px-5 py-3 hover:bg-red-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-800">{{ $activity->title }}</p>
                            <span class="text-xs text-red-600">{{ $activity->due_date->diffForHumans() }}</span>
                        </div>
                        @if($activity->is_escalated)
                            <p class="text-xs text-orange-600 mt-1">Escalated to {{ str_replace('_', ' ', $activity->escalated_to) }}</p>
                        @endif
                    </a>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500">No overdue activities. Great job! 🎉</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
