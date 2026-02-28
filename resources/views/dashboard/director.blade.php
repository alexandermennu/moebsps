@extends('layouts.app')

@section('title', 'Director Dashboard')
@section('page-title', 'Division Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ── HEADER ──────────────────────────────────────────────── --}}
    <div class="border-b border-gray-300 pb-4">
        <h2 class="text-lg font-semibold text-gray-900">{{ $user->division?->name ?? 'Division' }} — Performance Dashboard</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ now()->format('l, j F Y') }} &middot; {{ $user->name }}</p>
    </div>

    {{-- ── SECTION 1: WEEKLY PERFORMANCE SUMMARY ───────────────── --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">Weekly Performance Summary</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="{{ route('activities.index') }}" class="border border-gray-200 bg-white p-4 hover:bg-gray-50 transition">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_activities'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Assignments Given</p>
            </a>
            <a href="{{ route('activities.index', ['status' => 'completed']) }}" class="border border-gray-200 bg-white p-4 hover:bg-gray-50 transition">
                <p class="text-2xl font-bold text-green-700">{{ $stats['completed'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Completed</p>
            </a>
            <a href="{{ route('activities.index', ['status' => 'in_progress']) }}" class="border border-gray-200 bg-white p-4 hover:bg-gray-50 transition">
                <p class="text-2xl font-bold text-blue-700">{{ $stats['in_progress'] }}</p>
                <p class="text-xs text-gray-500 mt-1">In Progress</p>
            </a>
            <a href="{{ route('activities.index', ['status' => 'not_started']) }}" class="border border-gray-200 bg-white p-4 hover:bg-gray-50 transition">
                <p class="text-2xl font-bold text-gray-600">{{ $stats['not_started'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Not Started</p>
            </a>
            <a href="{{ route('activities.index', ['status' => 'overdue']) }}" class="border border-gray-200 bg-white p-4 hover:bg-gray-50 transition">
                <p class="text-2xl font-bold text-red-700">{{ $stats['overdue'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Overdue</p>
            </a>
            <div class="border border-gray-200 bg-white p-4">
                <p class="text-2xl font-bold {{ $stats['completion_rate'] >= 70 ? 'text-green-700' : ($stats['completion_rate'] >= 40 ? 'text-yellow-700' : 'text-red-700') }}">{{ $stats['completion_rate'] }}%</p>
                <p class="text-xs text-gray-500 mt-1">Completion Rate</p>
            </div>
        </div>

        {{-- Progress Bar --}}
        @if($stats['total_activities'] > 0)
        <div class="mt-4 border border-gray-200 bg-white p-4">
            <div class="flex items-center gap-4 mb-2">
                <span class="text-xs text-gray-500">Assignment Progress</span>
                <span class="text-xs font-medium text-gray-700 ml-auto">{{ $stats['completed'] }} of {{ $stats['total_activities'] }} completed</span>
            </div>
            <div class="w-full bg-gray-200 h-2 flex overflow-hidden">
                @php
                    $total = $stats['total_activities'];
                    $segments = [
                        ['count' => $stats['completed'], 'color' => 'bg-green-600'],
                        ['count' => $stats['in_progress'], 'color' => 'bg-blue-500'],
                        ['count' => $stats['not_started'], 'color' => 'bg-gray-400'],
                        ['count' => $stats['overdue'], 'color' => 'bg-red-500'],
                    ];
                @endphp
                @foreach($segments as $seg)
                    @if($seg['count'] > 0)
                    <div class="{{ $seg['color'] }} h-2" style="width: {{ ($seg['count'] / $total) * 100 }}%"></div>
                    @endif
                @endforeach
            </div>
            <div class="flex items-center gap-5 mt-2 text-xs text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-green-600 inline-block"></span>Completed</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-blue-500 inline-block"></span>In Progress</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-gray-400 inline-block"></span>Not Started</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-red-500 inline-block"></span>Overdue</span>
            </div>
        </div>
        @endif
    </section>

    {{-- ── SECTION 2: SUBMISSION PERFORMANCE ───────────────────── --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">Weekly Plans &amp; Updates — Submission Performance</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Weekly Updates --}}
            <div class="border border-gray-200 bg-white">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-sm font-semibold text-gray-800">Weekly Updates</h4>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-4 gap-3 text-center">
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ $stats['total_updates'] }}</p>
                            <p class="text-[11px] text-gray-500">Total</p>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-green-700">{{ $stats['approved_updates'] }}</p>
                            <p class="text-[11px] text-gray-500">Approved</p>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-blue-700">{{ $stats['submitted_updates'] }}</p>
                            <p class="text-[11px] text-gray-500">Pending Review</p>
                        </div>
                        <div>
                            @php $updateRate = $stats['total_updates'] > 0 ? round(($stats['approved_updates'] / $stats['total_updates']) * 100) : 0; @endphp
                            <p class="text-xl font-bold {{ $updateRate >= 70 ? 'text-green-700' : ($updateRate >= 40 ? 'text-yellow-700' : 'text-red-700') }}">{{ $updateRate }}%</p>
                            <p class="text-[11px] text-gray-500">Approval Rate</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Weekly Plans --}}
            <div class="border border-gray-200 bg-white">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-sm font-semibold text-gray-800">Weekly Plans</h4>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-4 gap-3 text-center">
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ $stats['total_plans'] }}</p>
                            <p class="text-[11px] text-gray-500">Total</p>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-green-700">{{ $stats['approved_plans'] }}</p>
                            <p class="text-[11px] text-gray-500">Approved</p>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-blue-700">{{ $stats['submitted_plans'] }}</p>
                            <p class="text-[11px] text-gray-500">Pending Review</p>
                        </div>
                        <div>
                            @php $planRate = $stats['total_plans'] > 0 ? round(($stats['approved_plans'] / $stats['total_plans']) * 100) : 0; @endphp
                            <p class="text-xl font-bold {{ $planRate >= 70 ? 'text-green-700' : ($planRate >= 40 ? 'text-yellow-700' : 'text-red-700') }}">{{ $planRate }}%</p>
                            <p class="text-[11px] text-gray-500">Approval Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── SECTION 3: STAFF PERFORMANCE ANALYSIS ───────────────── --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">Staff Performance Analysis</h3>
        <div class="border border-gray-200 bg-white">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-500">{{ $stats['total_staff'] }} active staff members</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[11px] text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                            <th class="py-2.5 px-4">Staff Member</th>
                            <th class="py-2.5 px-3 text-center">Role</th>
                            <th class="py-2.5 px-3 text-center">Assigned</th>
                            <th class="py-2.5 px-3 text-center">Completed</th>
                            <th class="py-2.5 px-3 text-center">Overdue</th>
                            <th class="py-2.5 px-3 text-center">Completion %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($divisionStaff as $staff)
                            @php $staffRate = $staff->activities_count > 0 ? round(($staff->completed_activities_count / $staff->activities_count) * 100) : 0; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-2.5 px-4">
                                    <span class="font-medium text-gray-900">{{ $staff->name }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-center text-xs text-gray-500">{{ $staff->role_label }}</td>
                                <td class="py-2.5 px-3 text-center font-medium text-gray-900">{{ $staff->activities_count }}</td>
                                <td class="py-2.5 px-3 text-center text-green-700">{{ $staff->completed_activities_count }}</td>
                                <td class="py-2.5 px-3 text-center {{ $staff->overdue_activities_count > 0 ? 'text-red-700 font-semibold' : 'text-gray-400' }}">{{ $staff->overdue_activities_count }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 bg-gray-200 h-1.5">
                                            <div class="h-1.5 {{ $staffRate >= 70 ? 'bg-green-600' : ($staffRate >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $staffRate }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium {{ $staffRate >= 70 ? 'text-green-700' : ($staffRate >= 40 ? 'text-yellow-700' : 'text-red-700') }}">{{ $staffRate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if($divisionStaff->isEmpty())
                            <tr><td colspan="6" class="py-6 text-center text-sm text-gray-400">No staff in this division yet.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ── SECTION 4: ITEMS REQUIRING ATTENTION ────────────────── --}}
    @if($overdueActivities->count() > 0 || (isset($stats['srgbv_open']) && $stats['srgbv_open'] > 0) || ($trackedStats['stale'] ?? 0) > 0 || ($trackedStats['repeated'] ?? 0) > 0)
    <section>
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">Items Requiring Attention</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Overdue Activities --}}
            @if($overdueActivities->count() > 0)
            <div class="border border-gray-200 bg-white">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800">Overdue Activities</h4>
                    <span class="text-xs text-gray-500">{{ $stats['overdue'] }}</span>
                </div>
                <div class="divide-y divide-gray-100 max-h-56 overflow-y-auto">
                    @foreach($overdueActivities->take(5) as $activity)
                        <a href="{{ route('activities.show', $activity) }}" class="block px-4 py-2.5 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-800 truncate mr-2">{{ $activity->title }}</p>
                                <span class="text-xs text-red-600 whitespace-nowrap">{{ $activity->due_date->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-gray-400">{{ $activity->assignee?->name }}</p>
                            @if($activity->is_escalated)
                                <p class="text-xs text-orange-600 mt-0.5">Escalated to {{ str_replace('_', ' ', $activity->escalated_to) }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- SRGBV Cases --}}
            @if(isset($stats['srgbv_open']) && $stats['srgbv_open'] > 0)
            <a href="{{ route('srgbv.cases.index') }}" class="border border-red-200 bg-white p-4 hover:bg-red-50 transition block">
                <p class="text-sm font-semibold text-red-800">{{ $stats['srgbv_open'] }} Open SRGBV Cases</p>
                @if(isset($stats['srgbv_critical']) && $stats['srgbv_critical'] > 0)
                <p class="text-xs text-red-600 mt-1">{{ $stats['srgbv_critical'] }} critical &middot; {{ $stats['srgbv_total'] }} total cases</p>
                @else
                <p class="text-xs text-gray-500 mt-1">{{ $stats['srgbv_total'] }} total cases</p>
                @endif
            </a>
            @endif

            {{-- Tracked Activity Alerts --}}
            @if(($trackedStats['stale'] ?? 0) > 0 || ($trackedStats['repeated'] ?? 0) > 0)
            <div class="border border-gray-200 bg-white">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800">Activity Tracker Alerts</h4>
                    <a href="{{ route('tracked-activities.index') }}" class="text-xs text-blue-700 hover:underline">View all</a>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-3 gap-3 text-center mb-3">
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $trackedStats['total'] }}</p>
                            <p class="text-[11px] text-gray-500">Tracked</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-amber-700">{{ $trackedStats['stale'] }}</p>
                            <p class="text-[11px] text-gray-500">Stale</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-purple-700">{{ $trackedStats['repeated'] }}</p>
                            <p class="text-[11px] text-gray-500">Repeated</p>
                        </div>
                    </div>
                    @if($flaggedActivities->count() > 0)
                    <div class="border-t border-gray-100 pt-3 space-y-2">
                        @foreach($flaggedActivities as $tracked)
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <p class="text-gray-700 truncate">{{ $tracked->activity_text }}</p>
                                <div class="flex gap-1 flex-shrink-0">
                                    @if($tracked->is_stale)<span class="text-[10px] px-1.5 py-0.5 bg-amber-100 text-amber-800">Stale</span>@endif
                                    @if($tracked->is_repeated)<span class="text-[10px] px-1.5 py-0.5 bg-purple-100 text-purple-800">Repeated</span>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ── SECTION 5: SUBMISSION STATUS ────────────────────────── --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">Submission Status</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Submitted Plans --}}
            <div class="border border-gray-200 bg-white">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800">Weekly Plans</h4>
                    <a href="{{ route('weekly-plans.index') }}" class="text-xs text-blue-700 hover:underline">View all</a>
                </div>
                <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                    @forelse($submittedPlans as $plan)
                        <a href="{{ route('weekly-plans.show', $plan) }}" class="block px-4 py-3 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-800">{{ $plan->week_start->format('M d') }} – {{ $plan->week_end->format('M d') }}</p>
                                <span class="text-[10px] px-1.5 py-0.5
                                    {{ $plan->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $plan->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $plan->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if($plan->status === 'submitted') Awaiting review
                                @elseif($plan->status === 'approved') Approved by {{ $plan->reviewer?->name }} &middot; {{ $plan->reviewed_at?->diffForHumans() }}
                                @elseif($plan->status === 'rejected') Rejected by {{ $plan->reviewer?->name }} &middot; {{ $plan->reviewed_at?->diffForHumans() }}
                                @endif
                            </p>
                        </a>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-400">No submitted plans yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Submitted Updates --}}
            <div class="border border-gray-200 bg-white">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800">Weekly Updates</h4>
                    <a href="{{ route('weekly-updates.index') }}" class="text-xs text-blue-700 hover:underline">View all</a>
                </div>
                <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                    @forelse($submittedUpdates as $update)
                        <a href="{{ route('weekly-updates.show', $update) }}" class="block px-4 py-3 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-800">{{ $update->week_start->format('M d') }} – {{ $update->week_end->format('M d') }}</p>
                                <span class="text-[10px] px-1.5 py-0.5
                                    {{ $update->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $update->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $update->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($update->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if($update->status === 'submitted') Awaiting review
                                @elseif($update->status === 'approved') Approved by {{ $update->reviewer?->name }} &middot; {{ $update->reviewed_at?->diffForHumans() }}
                                @elseif($update->status === 'rejected') Rejected by {{ $update->reviewer?->name }} &middot; {{ $update->reviewed_at?->diffForHumans() }}
                                @endif
                            </p>
                        </a>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-400">No submitted updates yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ── SECTION 6: DIVISION OVERVIEW ────────────────────────── --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">Division Overview</h3>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Division Totals --}}
            <div class="border border-gray-200 bg-white">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-sm font-semibold text-gray-800">Division Totals</h4>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="text-center border border-gray-100 p-3">
                            <p class="text-xl font-bold text-gray-900">{{ $stats['total_staff'] }}</p>
                            <p class="text-xs text-gray-500">Active Staff</p>
                        </div>
                        <div class="text-center border border-gray-100 p-3">
                            <p class="text-xl font-bold text-gray-900">{{ $stats['total_activities'] }}</p>
                            <p class="text-xs text-gray-500">Assignments</p>
                        </div>
                        <div class="text-center border border-gray-100 p-3">
                            <p class="text-xl font-bold text-gray-900">{{ $stats['total_updates'] }}</p>
                            <p class="text-xs text-gray-500">Updates</p>
                        </div>
                        <div class="text-center border border-gray-100 p-3">
                            <p class="text-xl font-bold text-gray-900">{{ $stats['total_plans'] }}</p>
                            <p class="text-xs text-gray-500">Plans</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($stats['srgbv_total']))
            {{-- SRGBV Summary --}}
            <div class="border border-gray-200 bg-white">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-sm font-semibold text-gray-800">SRGBV Cases</h4>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('srgbv.dashboard') }}" class="text-center border border-gray-100 p-3 hover:bg-gray-50 transition">
                            <p class="text-xl font-bold text-red-700">{{ $stats['srgbv_total'] }}</p>
                            <p class="text-xs text-gray-500">Total</p>
                        </a>
                        <a href="{{ route('srgbv.cases.index') }}" class="text-center border border-gray-100 p-3 hover:bg-gray-50 transition">
                            <p class="text-xl font-bold text-red-700">{{ $stats['srgbv_open'] }}</p>
                            <p class="text-xs text-gray-500">Open</p>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent Assignments --}}
            <div class="border border-gray-200 bg-white {{ isset($stats['srgbv_total']) ? '' : 'lg:col-span-2' }}">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800">Recent Assignments</h4>
                    <a href="{{ route('activities.index') }}" class="text-xs text-blue-700 hover:underline">View all</a>
                </div>
                <div class="divide-y divide-gray-100 max-h-56 overflow-y-auto">
                    @forelse($recentActivities as $activity)
                        <a href="{{ route('activities.show', $activity) }}" class="block px-4 py-2.5 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-800 truncate mr-2">{{ $activity->title }}</p>
                                <span class="text-[10px] px-1.5 py-0.5 whitespace-nowrap
                                    {{ $activity->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $activity->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $activity->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $activity->status === 'not_started' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ str_replace('_', ' ', ucfirst($activity->status)) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $activity->assignee?->name }} &middot; Due: {{ $activity->due_date->format('M d, Y') }}</p>
                        </a>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-400">No assignments yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ── QUICK ACTIONS ───────────────────────────────────────── --}}
    <section class="border-t border-gray-200 pt-4">
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Quick Actions</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('activities.create') }}" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700 transition">New Assignment</a>
            <a href="{{ route('weekly-updates.create') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">New Weekly Update</a>
            <a href="{{ route('weekly-plans.create') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">New Weekly Plan</a>
            <a href="{{ route('cases-report') }}" class="px-4 py-2 bg-red-700 text-white text-sm font-medium hover:bg-red-800 transition">Report Case</a>
        </div>
    </section>
</div>
@endsection
