@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Bureau Dashboard')

@section('content')
<div class="space-y-8">

    {{-- ═══════════════════════════════════════════════════════════════
         WELCOME BAR + KEY ALERT BADGES
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Welcome back, {{ $user->name }}</h2>
            <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }} · {{ $user->role_label }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($stats['overdue_activities'] > 0)
                <a href="{{ route('activities.index', ['status' => 'overdue']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full hover:bg-red-200 transition">
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span> {{ $stats['overdue_activities'] }} Overdue
                </a>
            @endif
            @if($stats['escalated_activities'] > 0)
                <a href="{{ route('activities.index', ['escalated' => '1']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full hover:bg-orange-200 transition">
                    {{ $stats['escalated_activities'] }} Escalated
                </a>
            @endif
            @if($stats['pending_updates'] > 0)
                <a href="{{ route('weekly-updates.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full hover:bg-blue-200 transition">
                    {{ $stats['pending_updates'] }} Updates to Review
                </a>
            @endif
            @if($stats['pending_plans'] > 0)
                <a href="{{ route('weekly-plans.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full hover:bg-purple-200 transition">
                    {{ $stats['pending_plans'] }} Plans to Review
                </a>
            @endif
            @if(($stats['pending_staff'] ?? 0) > 0)
                <a href="{{ route('admin.staff-approvals.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full hover:bg-amber-200 transition">
                    {{ $stats['pending_staff'] }} Staff Pending
                </a>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 1: BUREAU OVERVIEW — DIVISION SUMMARY CARDS
    ═══════════════════════════════════════════════════════════════ --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Bureau Overview</h3>
                <p class="text-sm text-gray-500">Summary across all {{ $stats['total_divisions'] }} divisions · {{ $stats['total_users'] }} active staff</p>
            </div>
            <a href="{{ route('weekly-updates.consolidated') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-md hover:bg-slate-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Consolidated Reports
            </a>
        </div>

        {{-- Bureau-wide stat pills --}}
        <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-5">
            <a href="{{ route('activities.index') }}" class="bg-white rounded-lg border border-gray-200 p-3 text-center hover:border-blue-300 hover:shadow-sm transition">
                <p class="text-xl font-bold text-gray-800">{{ $stats['total_activities'] }}</p>
                <p class="text-[11px] text-gray-500">Assignments</p>
            </a>
            <a href="{{ route('activities.index', ['status' => 'in_progress']) }}" class="bg-white rounded-lg border border-gray-200 p-3 text-center hover:border-blue-300 hover:shadow-sm transition">
                <p class="text-xl font-bold text-blue-600">{{ $stats['in_progress'] }}</p>
                <p class="text-[11px] text-gray-500">In Progress</p>
            </a>
            <a href="{{ route('activities.index', ['status' => 'completed']) }}" class="bg-white rounded-lg border border-gray-200 p-3 text-center hover:border-green-300 hover:shadow-sm transition">
                <p class="text-xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                <p class="text-[11px] text-gray-500">Completed</p>
            </a>
            <a href="{{ route('activities.index', ['status' => 'overdue']) }}" class="bg-white rounded-lg border border-red-200 p-3 text-center hover:border-red-300 hover:shadow-sm transition">
                <p class="text-xl font-bold text-red-600">{{ $stats['overdue_activities'] }}</p>
                <p class="text-[11px] text-red-500">Overdue</p>
            </a>
            <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
                <p class="text-xl font-bold {{ $stats['completion_rate'] >= 70 ? 'text-green-600' : ($stats['completion_rate'] >= 40 ? 'text-yellow-600' : 'text-red-600') }}">{{ $stats['completion_rate'] }}%</p>
                <p class="text-[11px] text-gray-500">Completion</p>
            </div>
            <a href="{{ route('activities.index', ['escalated' => '1']) }}" class="bg-white rounded-lg border border-orange-200 p-3 text-center hover:border-orange-300 hover:shadow-sm transition">
                <p class="text-xl font-bold text-orange-600">{{ $stats['escalated_activities'] }}</p>
                <p class="text-[11px] text-orange-500">Escalated</p>
            </a>
        </div>

        {{-- Division Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($divisions as $division)
                @php
                    $rate = $division->activities_count > 0 ? round(($division->completed_count / $division->activities_count) * 100) : 0;
                    $divSummary = $divisionUpdateSummaries->firstWhere('id', $division->id);
                @endphp
                <div class="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-md transition">
                    {{-- Division Header --}}
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">{{ $division->name }}</h4>
                            <p class="text-xs text-gray-400">{{ $division->code }} · {{ $division->staff_count }} staff</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold {{ $rate >= 70 ? 'text-green-600' : ($rate >= 40 ? 'text-yellow-600' : 'text-red-600') }}">{{ $rate }}%</p>
                            <p class="text-[10px] text-gray-400">completion</p>
                        </div>
                    </div>

                    {{-- Assignment Stats Row --}}
                    <div class="grid grid-cols-4 gap-1.5 mb-3">
                        <div class="text-center p-1.5 bg-gray-50 rounded">
                            <p class="text-sm font-bold text-gray-700">{{ $division->activities_count }}</p>
                            <p class="text-[10px] text-gray-400">Total</p>
                        </div>
                        <div class="text-center p-1.5 bg-green-50 rounded">
                            <p class="text-sm font-bold text-green-600">{{ $division->completed_count }}</p>
                            <p class="text-[10px] text-green-500">Done</p>
                        </div>
                        <div class="text-center p-1.5 bg-blue-50 rounded">
                            <p class="text-sm font-bold text-blue-600">{{ $division->in_progress_count }}</p>
                            <p class="text-[10px] text-blue-500">Active</p>
                        </div>
                        <div class="text-center p-1.5 {{ $division->overdue_count > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded">
                            <p class="text-sm font-bold {{ $division->overdue_count > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $division->overdue_count }}</p>
                            <p class="text-[10px] {{ $division->overdue_count > 0 ? 'text-red-500' : 'text-gray-400' }}">Overdue</p>
                        </div>
                    </div>

                    {{-- Completion bar --}}
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $rate >= 70 ? 'bg-green-500' : ($rate >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $rate }}%"></div>
                        </div>
                    </div>

                    {{-- Latest Update Info --}}
                    @if($divSummary && $divSummary->latest_update)
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-1.5">
                                <p class="text-xs text-gray-500">Latest: {{ $divSummary->latest_update->week_start->format('M d') }} – {{ $divSummary->latest_update->week_end->format('M d') }}</p>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium
                                    {{ $divSummary->latest_update->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $divSummary->latest_update->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $divSummary->latest_update->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $divSummary->latest_update->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}">
                                    {{ ucfirst($divSummary->latest_update->status) }}
                                </span>
                            </div>
                            @if(array_sum($divSummary->activity_stats) > 0)
                                <div class="flex items-center gap-1.5 text-xs">
                                    @if($divSummary->activity_stats['completed'] > 0)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ $divSummary->activity_stats['completed'] }}
                                        </span>
                                    @endif
                                    @if($divSummary->activity_stats['ongoing'] > 0)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>{{ $divSummary->activity_stats['ongoing'] }}
                                        </span>
                                    @endif
                                    @if($divSummary->activity_stats['not_started'] > 0)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-red-100 text-red-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ $divSummary->activity_stats['not_started'] }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            <a href="{{ route('weekly-updates.show', $divSummary->latest_update) }}" class="text-[11px] text-blue-600 hover:text-blue-800 mt-1.5 inline-block">View report →</a>
                        </div>
                    @else
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-xs text-gray-400 italic">No updates submitted yet</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 2: ATTENTION REQUIRED
    ═══════════════════════════════════════════════════════════════ --}}
    @if(($stats['pending_staff'] ?? 0) > 0 || $stats['srgbv_open'] > 0 || ($trackedStats['stale'] ?? 0) > 0 || ($trackedStats['repeated'] ?? 0) > 0 || $escalatedActivities->count() > 0)
    <div>
        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span> Attention Required
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

            {{-- Staff Pending Approval --}}
            @if(($stats['pending_staff'] ?? 0) > 0)
            <a href="{{ route('admin.staff-approvals.index') }}" class="bg-amber-50 rounded-lg border border-amber-200 p-4 hover:border-amber-400 hover:shadow-md transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-200 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">{{ $stats['pending_staff'] }} Staff Awaiting Approval</p>
                        <p class="text-xs text-amber-600 group-hover:text-amber-700">Click to review →</p>
                    </div>
                </div>
            </a>
            @endif

            {{-- SRGBV Cases --}}
            @if($stats['srgbv_open'] > 0)
            <a href="{{ route('srgbv.cases.index') }}" class="bg-red-50 rounded-lg border border-red-200 p-4 hover:border-red-400 hover:shadow-md transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-200 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-red-800">{{ $stats['srgbv_open'] }} Open SRGBV Cases @if($stats['srgbv_critical'] > 0)<span class="text-red-600">({{ $stats['srgbv_critical'] }} critical)</span>@endif</p>
                        <p class="text-xs text-red-600 group-hover:text-red-700">View cases →</p>
                    </div>
                </div>
            </a>
            @endif

            {{-- Escalated Activities --}}
            @if($escalatedActivities->count() > 0)
            <div class="bg-white rounded-lg border border-orange-200">
                <div class="px-4 py-3 border-b border-orange-200 bg-orange-50 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-orange-800">⚠ Escalated</h4>
                    <span class="text-xs bg-orange-200 text-orange-700 px-2 py-0.5 rounded-full">{{ $stats['escalated_activities'] }}</span>
                </div>
                <div class="divide-y divide-gray-50 max-h-48 overflow-y-auto">
                    @foreach($escalatedActivities->take(5) as $activity)
                        <a href="{{ route('activities.show', $activity) }}" class="block px-4 py-2.5 hover:bg-orange-50">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $activity->title }}</p>
                            <p class="text-xs text-gray-500">{{ $activity->division?->name }} · {{ $activity->escalated_at?->diffForHumans() }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Activity Tracker Alerts --}}
        @if(($trackedStats['stale'] ?? 0) > 0 || ($trackedStats['repeated'] ?? 0) > 0)
        <div class="bg-white rounded-lg border border-gray-200 p-5 mt-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-800">📡 Activity Tracker Alerts</h4>
                <a href="{{ route('tracked-activities.index') }}" class="text-xs text-blue-600 hover:text-blue-800">View all tracked →</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                <a href="{{ route('tracked-activities.index') }}" class="p-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                    <p class="text-lg font-bold text-gray-800">{{ $trackedStats['total'] }}</p>
                    <p class="text-[11px] text-gray-500">Total Tracked</p>
                </a>
                <a href="{{ route('tracked-activities.index', ['flag' => 'stale']) }}" class="p-2.5 bg-amber-50 rounded-lg border border-amber-200 hover:border-amber-400 transition text-center">
                    <p class="text-lg font-bold text-amber-700">{{ $trackedStats['stale'] }}</p>
                    <p class="text-[11px] text-amber-600 font-semibold">Stale</p>
                </a>
                <a href="{{ route('tracked-activities.index', ['flag' => 'repeated']) }}" class="p-2.5 bg-purple-50 rounded-lg border border-purple-200 hover:border-purple-400 transition text-center">
                    <p class="text-lg font-bold text-purple-700">{{ $trackedStats['repeated'] }}</p>
                    <p class="text-[11px] text-purple-600 font-semibold">Repeated</p>
                </a>
                <div class="p-2.5 bg-blue-50 rounded-lg text-center">
                    <p class="text-lg font-bold text-blue-700">{{ $trackedStats['active'] }}</p>
                    <p class="text-[11px] text-blue-500">Active</p>
                </div>
            </div>
            @if($flaggedActivities->count() > 0)
            <div class="border-t border-gray-100 pt-3">
                <p class="text-xs font-semibold text-gray-500 mb-2">Flagged Activities Requiring Attention</p>
                <div class="space-y-2">
                    @foreach($flaggedActivities->take(5) as $tracked)
                        <div class="flex items-center gap-3 p-2 rounded-lg {{ $tracked->is_stale && $tracked->is_repeated ? 'bg-red-50' : ($tracked->is_stale ? 'bg-amber-50' : 'bg-purple-50') }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800 font-medium truncate">{{ $tracked->activity_text }}</p>
                                <p class="text-xs text-gray-400">{{ $tracked->division?->name }} · {{ $tracked->status_label }} for {{ $tracked->weeks_unchanged }}w · Reported {{ $tracked->times_reported }}×</p>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                @if($tracked->is_stale)<span class="text-xs px-1.5 py-0.5 rounded bg-amber-200 text-amber-800 font-semibold">Stale</span>@endif
                                @if($tracked->is_repeated)<span class="text-xs px-1.5 py-0.5 rounded bg-purple-200 text-purple-800 font-semibold">Repeated</span>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 3: SUBMISSIONS & REVIEWS
    ═══════════════════════════════════════════════════════════════ --}}
    @if($user->canReviewSubmissions() || $user->isMinister())
    <div>
        <h3 class="text-base font-bold text-gray-800 mb-4">Submissions & Reviews</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-{{ $user->isMinister() ? '2' : '2' }} gap-4">

            {{-- Pending Update Reviews --}}
            @if($user->canReviewSubmissions())
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800">Pending Update Reviews</h4>
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $stats['pending_updates'] }}</span>
                </div>
                <div class="divide-y divide-gray-50 max-h-56 overflow-y-auto">
                    @forelse($pendingReviews as $update)
                        <a href="{{ route('weekly-updates.show', $update) }}" class="block px-5 py-3 hover:bg-blue-50">
                            <p class="text-sm font-medium text-gray-800">{{ $update->division?->name }}</p>
                            <p class="text-xs text-gray-500">By {{ $update->submitter?->name }} · {{ $update->created_at->diffForHumans() }}</p>
                        </a>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No pending update reviews.</div>
                    @endforelse
                </div>
            </div>

            {{-- Pending Plan Reviews --}}
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800">Pending Plan Reviews</h4>
                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">{{ $stats['pending_plans'] }}</span>
                </div>
                <div class="divide-y divide-gray-50 max-h-56 overflow-y-auto">
                    @forelse($pendingPlanReviews as $plan)
                        <a href="{{ route('weekly-plans.show', $plan) }}" class="block px-5 py-3 hover:bg-purple-50">
                            <p class="text-sm font-medium text-gray-800">{{ $plan->division?->name }}</p>
                            <p class="text-xs text-gray-500">By {{ $plan->submitter?->name }} · {{ $plan->created_at->diffForHumans() }}</p>
                        </a>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No pending plan reviews.</div>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Approved Plans (Minister) --}}
            @if($user->isMinister())
            <div class="bg-white rounded-lg border border-green-200">
                <div class="px-5 py-3.5 border-b border-green-200 bg-green-50 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-green-800">Approved Plans</h4>
                    <a href="{{ route('weekly-plans.index', ['status' => 'approved']) }}" class="text-xs text-green-600 hover:text-green-800">View all →</a>
                </div>
                <div class="divide-y divide-gray-50 max-h-56 overflow-y-auto">
                    @forelse($approvedPlans as $plan)
                        <a href="{{ route('weekly-plans.show', $plan) }}" class="block px-5 py-3 hover:bg-green-50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-800">{{ $plan->division?->name }}</p>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Approved</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $plan->week_start->format('M d') }} – {{ $plan->week_end->format('M d') }} · {{ $plan->submitter?->name }}</p>
                        </a>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No approved plans yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Approved Updates (Minister) --}}
            <div class="bg-white rounded-lg border border-green-200">
                <div class="px-5 py-3.5 border-b border-green-200 bg-green-50 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-green-800">Approved Updates</h4>
                    <a href="{{ route('weekly-updates.index', ['status' => 'approved']) }}" class="text-xs text-green-600 hover:text-green-800">View all →</a>
                </div>
                <div class="divide-y divide-gray-50 max-h-56 overflow-y-auto">
                    @forelse($approvedUpdates as $update)
                        <a href="{{ route('weekly-updates.show', $update) }}" class="block px-5 py-3 hover:bg-green-50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-800">{{ $update->division?->name }}</p>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Approved</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $update->week_start->format('M d') }} – {{ $update->week_end->format('M d') }} · {{ $update->submitter?->name }}</p>
                        </a>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No approved updates yet.</div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 4: PERFORMANCE & TEAM
    ═══════════════════════════════════════════════════════════════ --}}
    <div>
        <h3 class="text-base font-bold text-gray-800 mb-4">Performance & Team</h3>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Division Performance Table --}}
            <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-5">
                <h4 class="text-sm font-semibold text-gray-800 mb-3">Division Performance</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-gray-500 uppercase border-b border-gray-100">
                                <th class="pb-2 pr-4">Division</th>
                                <th class="pb-2 px-2 text-center">Staff</th>
                                <th class="pb-2 px-2 text-center">Total</th>
                                <th class="pb-2 px-2 text-center">Done</th>
                                <th class="pb-2 px-2 text-center">Active</th>
                                <th class="pb-2 px-2 text-center">Overdue</th>
                                <th class="pb-2 pl-2">Progress</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($divisions as $division)
                                @php $rate = $division->activities_count > 0 ? round(($division->completed_count / $division->activities_count) * 100) : 0; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2.5 pr-4">
                                        <p class="font-medium text-gray-800">{{ $division->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $division->code }}</p>
                                    </td>
                                    <td class="py-2.5 px-2 text-center text-gray-600">{{ $division->staff_count }}</td>
                                    <td class="py-2.5 px-2 text-center font-medium text-gray-800">{{ $division->activities_count }}</td>
                                    <td class="py-2.5 px-2 text-center text-green-600">{{ $division->completed_count }}</td>
                                    <td class="py-2.5 px-2 text-center text-blue-600">{{ $division->in_progress_count }}</td>
                                    <td class="py-2.5 px-2 text-center {{ $division->overdue_count > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">{{ $division->overdue_count }}</td>
                                    <td class="py-2.5 pl-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-full bg-gray-100 rounded-full h-2 min-w-[60px]">
                                                <div class="h-2 rounded-full {{ $rate >= 70 ? 'bg-green-500' : ($rate >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $rate }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 w-8">{{ $rate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Staff by Role + Bureau Totals --}}
            <div class="space-y-4">
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Staff by Role</h4>
                    <div class="space-y-2.5">
                        @foreach(\App\Models\User::ROLES as $key => $label)
                            @php $count = $staffByRole[$key] ?? 0; @endphp
                            @if($count > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ $label }}</span>
                                <span class="text-sm font-medium text-gray-800 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ $count }}</span>
                            </div>
                            @endif
                        @endforeach
                        <div class="border-t border-gray-100 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-700">Total</span>
                                <span class="text-sm font-bold text-gray-800">{{ $stats['total_users'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <h4 class="text-xs text-gray-500 uppercase tracking-wide mb-3">Bureau Totals</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <a href="{{ route('weekly-updates.index') }}" class="p-2.5 bg-gray-50 rounded-md hover:bg-gray-100 transition">
                            <p class="font-bold text-gray-800">{{ $stats['total_updates'] }}</p>
                            <p class="text-xs text-gray-400">Updates</p>
                        </a>
                        <a href="{{ route('weekly-plans.index') }}" class="p-2.5 bg-gray-50 rounded-md hover:bg-gray-100 transition">
                            <p class="font-bold text-gray-800">{{ $stats['total_plans'] }}</p>
                            <p class="text-xs text-gray-400">Plans</p>
                        </a>
                        <a href="{{ route('srgbv.dashboard') }}" class="p-2.5 bg-red-50 rounded-md hover:bg-red-100 transition">
                            <p class="font-bold text-red-700">{{ $stats['srgbv_total'] }}</p>
                            <p class="text-xs text-red-400">SRGBV Cases</p>
                        </a>
                        <a href="{{ route('admin.divisions.index') }}" class="p-2.5 bg-gray-50 rounded-md hover:bg-gray-100 transition">
                            <p class="font-bold text-gray-800">{{ $stats['total_divisions'] }}</p>
                            <p class="text-xs text-gray-400">Divisions</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 5: RECENT ASSIGNMENTS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Recent Assignments</h3>
            <a href="{{ route('activities.index') }}" class="text-xs text-blue-600 hover:text-blue-800">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
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
                    <p class="text-xs text-gray-500 mt-1">{{ $activity->division?->name }} · {{ $activity->assignee?->name }}</p>
                </a>
            @empty
                <div class="px-5 py-6 text-center text-sm text-gray-400">No assignments yet.</div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 6: QUICK ACTIONS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Quick Actions</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('activities.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Assignment
            </a>
            <a href="{{ route('cases-report') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-700 text-white text-sm font-medium rounded-md hover:bg-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                Report Case
            </a>
            <a href="{{ route('weekly-updates.consolidated') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Consolidated Reports
            </a>
            @if($user->isAdmin())
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">+ Add User</a>
            <a href="{{ route('admin.divisions.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">+ Add Division</a>
            <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">⚙ Settings</a>
            @endif
        </div>
    </div>
</div>
@endsection
