@extends('layouts.app')

@section('title', 'Minister Dashboard')
@section('page-title', 'Minister Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Ministry Overview</h2>
        <p class="text-sm text-gray-500">Welcome back, {{ $user->name }}</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Divisions</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_divisions'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Activities</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Completion Rate</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completion_rate'] }}%</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Overdue</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Escalated to You</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['escalated_to_minister'] }}</p>
        </div>
    </div>

    {{-- Division Performance --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Division Performance Overview</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Division</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Total</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Completed</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Overdue</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Performance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($divisions as $division)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ $division->name }}</td>
                            <td class="px-5 py-3 text-center">{{ $division->activities_count }}</td>
                            <td class="px-5 py-3 text-center text-green-600">{{ $division->completed_count }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="{{ $division->overdue_count > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">{{ $division->overdue_count }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @php $rate = $division->activities_count > 0 ? round(($division->completed_count / $division->activities_count) * 100) : 0; @endphp
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $rate >= 70 ? 'bg-green-500' : ($rate >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $rate }}%"></div>
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

    {{-- Critical Activities Escalated to Minister --}}
    <div class="bg-white rounded-lg border border-red-200">
        <div class="px-5 py-4 border-b border-red-200 bg-red-50">
            <h3 class="font-semibold text-red-800">🚨 Activities Escalated to Minister</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($criticalActivities as $activity)
                <a href="{{ route('activities.show', $activity) }}" class="block px-5 py-3 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $activity->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity->division->name }} · Escalated {{ $activity->escalated_at?->diffForHumans() }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">
                            Due: {{ $activity->due_date->format('M d, Y') }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-500">No activities escalated to minister level.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
