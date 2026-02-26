@extends('layouts.app')

@section('title', 'Bureau Head Dashboard')
@section('page-title', 'Bureau Head Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Bureau Overview</h2>
        <p class="text-sm text-gray-500">Welcome back, {{ $user->name }}</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Active Divisions</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_divisions'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Activities</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Overdue Activities</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Escalated Activities</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['escalated_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Pending Update Reviews</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['pending_updates'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Pending Plan Reviews</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['pending_plans'] }}</p>
        </div>
    </div>

    {{-- Division Performance --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Division Performance</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Division</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Activities</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Completed</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Overdue</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Completion Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($divisions as $division)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ $division->name }}</td>
                            <td class="px-5 py-3 text-center">{{ $division->activities_count }}</td>
                            <td class="px-5 py-3 text-center text-green-600">{{ $division->completed_count }}</td>
                            <td class="px-5 py-3 text-center">
                                @if($division->overdue_count > 0)
                                    <span class="text-red-600 font-medium">{{ $division->overdue_count }}</span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @php $rate = $division->activities_count > 0 ? round(($division->completed_count / $division->activities_count) * 100) : 0; @endphp
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ $rate }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Escalated Activities --}}
        <div class="bg-white rounded-lg border border-orange-200">
            <div class="px-5 py-4 border-b border-orange-200 bg-orange-50">
                <h3 class="font-semibold text-orange-800">🔺 Escalated Activities</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($escalatedActivities as $activity)
                    <a href="{{ route('activities.show', $activity) }}" class="block px-5 py-3 hover:bg-gray-50">
                        <p class="text-sm font-medium text-gray-800">{{ $activity->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $activity->division->name }} · Due: {{ $activity->due_date->format('M d, Y') }}</p>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500">No escalated activities.</div>
                @endforelse
            </div>
        </div>

        {{-- Pending Reviews --}}
        <div class="bg-white rounded-lg border border-blue-200">
            <div class="px-5 py-4 border-b border-blue-200 bg-blue-50">
                <h3 class="font-semibold text-blue-800">📋 Pending Reviews</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($pendingReviews as $update)
                    <a href="{{ route('weekly-updates.show', $update) }}" class="block px-5 py-3 hover:bg-gray-50">
                        <p class="text-sm font-medium text-gray-800">{{ $update->division->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">By {{ $update->submitter->name }} · {{ $update->week_start->format('M d') }} - {{ $update->week_end->format('M d, Y') }}</p>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500">No pending reviews.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
