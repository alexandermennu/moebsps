@extends('layouts.app')

@section('title', 'Director Dashboard')
@section('page-title', 'Division Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Division Header --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800">{{ $user->division?->name ?? 'My Division' }}</h2>
        <p class="text-sm text-gray-500">Welcome back, {{ $user->name }}. Here's your division overview.</p>
    </div>

    {{-- Clickable Stats Row 1: Core Metrics --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('activities.index') }}" class="bg-white rounded-lg border border-gray-200 p-5 hover:border-blue-400 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Activities</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_activities'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 group-hover:text-blue-500">View all →</p>
        </a>

        <a href="{{ route('activities.index', ['status' => 'in_progress']) }}" class="bg-white rounded-lg border border-gray-200 p-5 hover:border-blue-400 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">In Progress</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 transition">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 group-hover:text-blue-500">View in-progress →</p>
        </a>

        <a href="{{ route('activities.index', ['status' => 'completed']) }}" class="bg-white rounded-lg border border-gray-200 p-5 hover:border-green-400 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Completed</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 group-hover:text-green-500">View completed →</p>
        </a>

        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Completion Rate</p>
                    <p class="text-2xl font-bold {{ $stats['completion_rate'] >= 70 ? 'text-green-600' : ($stats['completion_rate'] >= 40 ? 'text-yellow-600' : 'text-red-600') }} mt-1">{{ $stats['completion_rate'] }}%</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3">
                <div class="h-1.5 rounded-full {{ $stats['completion_rate'] >= 70 ? 'bg-green-500' : ($stats['completion_rate'] >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $stats['completion_rate'] }}%"></div>
            </div>
        </div>
    </div>

    {{-- Clickable Stats Row 2 --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="{{ route('activities.index', ['status' => 'overdue']) }}" class="bg-red-50 rounded-lg border border-red-200 p-4 hover:border-red-400 hover:shadow-md transition group">
            <p class="text-xs text-red-600 uppercase tracking-wide font-semibold">Overdue</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $stats['overdue'] }}</p>
            <p class="text-xs text-red-400 mt-1 group-hover:text-red-600">View overdue →</p>
        </a>

        <a href="{{ route('activities.index', ['status' => 'not_started']) }}" class="bg-white rounded-lg border border-gray-200 p-4 hover:border-gray-400 hover:shadow-md transition group">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Not Started</p>
            <p class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['not_started'] }}</p>
            <p class="text-xs text-gray-400 mt-1 group-hover:text-gray-600">View pending →</p>
        </a>

        <a href="{{ route('activities.index', ['escalated' => '1']) }}" class="bg-orange-50 rounded-lg border border-orange-200 p-4 hover:border-orange-400 hover:shadow-md transition group">
            <p class="text-xs text-orange-600 uppercase tracking-wide font-semibold">Escalated</p>
            <p class="text-2xl font-bold text-orange-700 mt-1">{{ $stats['escalated'] }}</p>
            <p class="text-xs text-orange-400 mt-1 group-hover:text-orange-600">View escalated →</p>
        </a>

        <a href="{{ route('weekly-updates.index') }}" class="bg-white rounded-lg border border-gray-200 p-4 hover:border-blue-400 hover:shadow-md transition group">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Draft Updates</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['pending_updates'] }}</p>
            <p class="text-xs text-gray-400 mt-1 group-hover:text-blue-500">View updates →</p>
        </a>

        <a href="{{ route('weekly-plans.index') }}" class="bg-white rounded-lg border border-gray-200 p-4 hover:border-purple-400 hover:shadow-md transition group">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Draft Plans</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['pending_plans'] }}</p>
            <p class="text-xs text-gray-400 mt-1 group-hover:text-purple-500">View plans →</p>
        </a>
    </div>

    {{-- SRGBV Alert for CGPC Director --}}
    @if(isset($stats['srgbv_open']) && $stats['srgbv_open'] > 0)
    <a href="{{ route('srgbv.cases.index') }}" class="block bg-red-50 rounded-lg border border-red-300 p-4 hover:border-red-500 hover:shadow-md transition group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-200 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-red-800">{{ $stats['srgbv_open'] }} Open SRGBV Cases @if($stats['srgbv_critical'] > 0)<span class="text-red-600">({{ $stats['srgbv_critical'] }} critical)</span>@endif</p>
                <p class="text-xs text-red-600 group-hover:text-red-700">Total: {{ $stats['srgbv_total'] }} cases — Click to manage →</p>
            </div>
        </div>
    </a>
    @endif

    {{-- Quick Actions --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('activities.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Activity
        </a>
        <a href="{{ route('weekly-updates.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">+ New Weekly Update</a>
        <a href="{{ route('weekly-plans.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">+ New Weekly Plan</a>
        <a href="{{ route('cases-report') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-700 text-white text-sm font-medium rounded-md hover:bg-red-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            Report Case
        </a>
    </div>

    {{-- Division Overview Section --}}
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Division Overview</h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Staff Performance Table --}}
        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Staff Performance</h3>
                <span class="text-xs text-gray-500">{{ $stats['total_staff'] }} active staff</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase border-b border-gray-100">
                            <th class="pb-2 pr-4">Staff Member</th>
                            <th class="pb-2 px-2 text-center">Role</th>
                            <th class="pb-2 px-2 text-center">Total</th>
                            <th class="pb-2 px-2 text-center">Done</th>
                            <th class="pb-2 px-2 text-center">Overdue</th>
                            <th class="pb-2 pl-2">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($divisionStaff as $staff)
                            @php $staffRate = $staff->activities_count > 0 ? round(($staff->completed_activities_count / $staff->activities_count) * 100) : 0; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-2.5 pr-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-slate-200 rounded-full flex items-center justify-center text-xs font-bold text-slate-600">
                                            {{ strtoupper(substr($staff->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $staff->name }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-2 text-center text-xs text-gray-500">{{ $staff->role_label }}</td>
                                <td class="py-2.5 px-2 text-center font-medium text-gray-800">{{ $staff->activities_count }}</td>
                                <td class="py-2.5 px-2 text-center text-green-600">{{ $staff->completed_activities_count }}</td>
                                <td class="py-2.5 px-2 text-center {{ $staff->overdue_activities_count > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">{{ $staff->overdue_activities_count }}</td>
                                <td class="py-2.5 pl-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-full bg-gray-100 rounded-full h-2 min-w-[60px]">
                                            <div class="h-2 rounded-full {{ $staffRate >= 70 ? 'bg-green-500' : ($staffRate >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $staffRate }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 w-8">{{ $staffRate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if($divisionStaff->isEmpty())
                            <tr>
                                <td colspan="6" class="py-6 text-center text-sm text-gray-500">No staff in this division yet.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Division Summary --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Division Summary</h3>
            <div class="space-y-4">
                {{-- Activity Breakdown --}}
                <div>
                    <h4 class="text-xs text-gray-500 uppercase tracking-wide mb-2">Activity Status</h4>
                    @php
                        $statusData = [
                            ['label' => 'Completed', 'count' => $stats['completed'], 'color' => 'bg-green-500'],
                            ['label' => 'In Progress', 'count' => $stats['in_progress'], 'color' => 'bg-blue-500'],
                            ['label' => 'Not Started', 'count' => $stats['not_started'], 'color' => 'bg-gray-400'],
                            ['label' => 'Overdue', 'count' => $stats['overdue'], 'color' => 'bg-red-500'],
                        ];
                    @endphp
                    @if($stats['total_activities'] > 0)
                    <div class="w-full bg-gray-100 rounded-full h-3 flex overflow-hidden mb-2">
                        @foreach($statusData as $s)
                            @if($s['count'] > 0)
                            <div class="{{ $s['color'] }} h-3" style="width: {{ ($s['count'] / $stats['total_activities']) * 100 }}%" title="{{ $s['label'] }}: {{ $s['count'] }}"></div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-1 text-xs">
                        @foreach($statusData as $s)
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $s['color'] }}"></span>
                            <span class="text-gray-600">{{ $s['label'] }}: <span class="font-medium">{{ $s['count'] }}</span></span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Division Totals --}}
                <div class="pt-3 border-t border-gray-100">
                    <h4 class="text-xs text-gray-500 uppercase tracking-wide mb-2">Totals</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <a href="{{ route('weekly-updates.index') }}" class="p-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
                            <p class="font-bold text-gray-800">{{ $stats['total_updates'] }}</p>
                            <p class="text-xs text-gray-400">Updates</p>
                        </a>
                        <a href="{{ route('weekly-plans.index') }}" class="p-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
                            <p class="font-bold text-gray-800">{{ $stats['total_plans'] }}</p>
                            <p class="text-xs text-gray-400">Plans</p>
                        </a>
                        <div class="p-2 bg-gray-50 rounded-md">
                            <p class="font-bold text-gray-800">{{ $stats['total_staff'] }}</p>
                            <p class="text-xs text-gray-400">Staff Members</p>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-md">
                            <p class="font-bold text-gray-800">{{ $stats['escalated'] }}</p>
                            <p class="text-xs text-gray-400">Escalated</p>
                        </div>
                    </div>
                </div>

                @if(isset($stats['srgbv_total']))
                <div class="pt-3 border-t border-gray-100">
                    <h4 class="text-xs text-gray-500 uppercase tracking-wide mb-2">SRGBV Cases</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <a href="{{ route('srgbv.dashboard') }}" class="p-2 bg-red-50 rounded-md hover:bg-red-100 transition">
                            <p class="font-bold text-red-700">{{ $stats['srgbv_total'] }}</p>
                            <p class="text-xs text-red-400">Total Cases</p>
                        </a>
                        <a href="{{ route('srgbv.cases.index') }}" class="p-2 bg-red-50 rounded-md hover:bg-red-100 transition">
                            <p class="font-bold text-red-700">{{ $stats['srgbv_open'] }}</p>
                            <p class="text-xs text-red-400">Open Cases</p>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Activities --}}
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Recent Activities</h3>
                <a href="{{ route('activities.index') }}" class="text-xs text-blue-600 hover:text-blue-800">View all →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentActivities as $activity)
                    <a href="{{ route('activities.show', $activity) }}" class="block px-5 py-3 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-800 truncate mr-2">{{ $activity->title }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full whitespace-nowrap
                                {{ $activity->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $activity->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $activity->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $activity->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ str_replace('_', ' ', ucfirst($activity->status)) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $activity->assignee?->name }} · Due: {{ $activity->due_date->format('M d, Y') }}</p>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500">No activities yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Overdue Activities --}}
        <div class="bg-white rounded-lg border border-red-200">
            <div class="px-5 py-4 border-b border-red-200 bg-red-50 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-red-800">⚠ Overdue Activities</h3>
                <span class="text-xs bg-red-200 text-red-700 px-2 py-0.5 rounded-full">{{ $stats['overdue'] }}</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($overdueActivities as $activity)
                    <a href="{{ route('activities.show', $activity) }}" class="block px-5 py-3 hover:bg-red-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-800 truncate mr-2">{{ $activity->title }}</p>
                            <span class="text-xs text-red-600 whitespace-nowrap">{{ $activity->due_date->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $activity->assignee?->name }}</p>
                        @if($activity->is_escalated)
                            <p class="text-xs text-orange-600 mt-0.5">⬆ Escalated to {{ str_replace('_', ' ', $activity->escalated_to) }}</p>
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
