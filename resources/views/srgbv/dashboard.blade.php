@extends('layouts.app')

@section('title', 'SRGBV Dashboard')
@section('page-title', 'SRGBV Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">SRGBV Dashboard</h2>
            <p class="text-sm text-gray-500">School-Related Gender-Based Violence overview and statistics</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('srgbv.cases.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">
                View Cases
            </a>
            <a href="{{ route('srgbv.cases.create') }}" class="px-4 py-2 bg-red-700 text-white text-sm font-medium hover:bg-red-800">
                + Report Case
            </a>
        </div>
    </div>

    {{-- Key Metrics --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Cases</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalCases }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 p-4">
            <p class="text-xs text-red-600 uppercase tracking-wide">Open</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $openCases }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 p-4">
            <p class="text-xs text-amber-600 uppercase tracking-wide">Critical</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $criticalCases }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 p-4">
            <p class="text-xs text-green-600 uppercase tracking-wide">Resolved</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $closedCases }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 p-4">
            <p class="text-xs text-blue-600 uppercase tracking-wide">Resolution Rate</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $resolutionRate }}%</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 p-4">
            <p class="text-xs text-purple-600 uppercase tracking-wide">Follow-up Due</p>
            <p class="text-2xl font-bold text-purple-700 mt-1">{{ $followUpDue }}</p>
        </div>
    </div>

    @if($avgResolutionDays !== null)
    <div class="bg-white border border-gray-200 p-4 max-w-xs">
        <p class="text-xs text-gray-500">Avg. Time to Resolution</p>
        <p class="text-lg font-bold text-gray-800">{{ $avgResolutionDays }} days</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Cases by Status --}}
        <div class="bg-white border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Cases by Status</h3>
            <div class="space-y-3">
                @foreach(\App\Models\SrgbvCase::STATUSES as $key => $label)
                    @php $count = $casesByStatus[$key] ?? 0; $pct = $totalCases > 0 ? round(($count / $totalCases) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-medium text-gray-800">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full
                                @switch($key)
                                    @case('reported') bg-red-500 @break
                                    @case('under_investigation') bg-amber-500 @break
                                    @case('action_taken') bg-blue-500 @break
                                    @case('referred') bg-purple-500 @break
                                    @case('resolved') bg-green-500 @break
                                    @case('closed') bg-gray-400 @break
                                @endswitch
                            " style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Cases by Category --}}
        <div class="bg-white border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Cases by Category</h3>
            <div class="space-y-3">
                @foreach(\App\Models\SrgbvCase::CATEGORIES as $key => $label)
                    @php $count = $casesByCategory[$key] ?? 0; $pct = $totalCases > 0 ? round(($count / $totalCases) * 100) : 0; @endphp
                    @if($count > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-medium text-gray-800">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full bg-red-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endif
                @endforeach
                @if(empty(array_filter($casesByCategory)))
                    <p class="text-sm text-gray-500">No data available yet.</p>
                @endif
            </div>
        </div>

        {{-- Cases by Priority --}}
        <div class="bg-white border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Cases by Priority</h3>
            <div class="grid grid-cols-2 gap-3">
                @foreach(\App\Models\SrgbvCase::PRIORITIES as $key => $label)
                    @php $count = $casesByPriority[$key] ?? 0; @endphp
                    <div class="p-3
                        @switch($key)
                            @case('critical') bg-red-50 border border-red-200 @break
                            @case('high') bg-amber-50 border border-amber-200 @break
                            @case('medium') bg-blue-50 border border-blue-200 @break
                            @case('low') bg-gray-50 border border-gray-200 @break
                        @endswitch
                    ">
                        <p class="text-2xl font-bold
                            @switch($key)
                                @case('critical') text-red-700 @break
                                @case('high') text-amber-700 @break
                                @case('medium') text-blue-700 @break
                                @case('low') text-gray-600 @break
                            @endswitch
                        ">{{ $count }}</p>
                        <p class="text-xs text-gray-500">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Monthly Trend --}}
        <div class="bg-white border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Monthly Trend (Last 12 Months)</h3>
            @if(empty($monthlyTrend))
                <p class="text-sm text-gray-500">No data available yet.</p>
            @else
                <div class="flex items-end gap-1 h-40">
                    @php $maxVal = max($monthlyTrend) ?: 1; @endphp
                    @foreach($monthlyTrend as $month => $count)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <span class="text-xs font-medium text-gray-700">{{ $count }}</span>
                            <div class="w-full bg-red-500 rounded-t-sm" style="height: {{ ($count / $maxVal) * 100 }}%"></div>
                            <span class="text-[10px] text-gray-400 -rotate-45 origin-top-left whitespace-nowrap">{{ \Carbon\Carbon::parse($month . '-01')->format('M Y') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Cases --}}
        <div class="bg-white border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Recent Cases</h3>
                <a href="{{ route('srgbv.cases.index') }}" class="text-xs text-blue-700 hover:underline">View all</a>
            </div>
            @if($recentCases->isEmpty())
                <p class="text-sm text-gray-500">No cases reported yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentCases as $case)
                        <a href="{{ route('srgbv.cases.show', $case) }}" class="block p-3 bg-gray-50 border border-gray-100 hover:bg-gray-100">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-mono text-gray-400">{{ $case->case_number }}</span>
                                <span class="text-[10px] px-1.5 py-0.5
                                    @switch($case->priority)
                                        @case('critical') bg-red-100 text-red-700 @break
                                        @case('high') bg-amber-100 text-amber-700 @break
                                        @case('medium') bg-blue-100 text-blue-700 @break
                                        @case('low') bg-gray-100 text-gray-600 @break
                                    @endswitch
                                ">{{ $case->priority_label }}</span>
                                <span class="text-[10px] px-1.5 py-0.5
                                    @switch($case->status)
                                        @case('reported') bg-red-100 text-red-700 @break
                                        @case('under_investigation') bg-amber-100 text-amber-700 @break
                                        @case('action_taken') bg-blue-100 text-blue-700 @break
                                        @case('referred') bg-purple-100 text-purple-700 @break
                                        @case('resolved') bg-green-100 text-green-700 @break
                                        @case('closed') bg-gray-100 text-gray-600 @break
                                    @endswitch
                                ">{{ $case->status_label }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-800">{{ $case->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $case->reporter?->name }} · {{ $case->created_at->diffForHumans() }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Follow-up Due --}}
        <div class="bg-white border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Cases Requiring Follow-up</h3>
            @if($followUpCases->isEmpty())
                <div class="text-center py-6">
                    <p class="text-sm text-gray-500">No follow-ups due.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($followUpCases as $case)
                        <a href="{{ route('srgbv.cases.show', $case) }}" class="block p-3 {{ $case->follow_up_date && $case->follow_up_date->isPast() ? 'bg-red-50 border border-red-200' : 'bg-amber-50 border border-amber-200' }} hover:opacity-90">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $case->case_number }}: {{ Str::limit($case->title, 40) }}</p>
                                    <p class="text-xs text-gray-500">Assigned: {{ $case->assignee?->name ?? 'Unassigned' }}</p>
                                </div>
                                <div class="text-right">
                                    @if($case->follow_up_date)
                                        <p class="text-xs font-medium {{ $case->follow_up_date->isPast() ? 'text-red-700' : 'text-amber-700' }}">
                                            {{ $case->follow_up_date->format('M d') }}
                                        </p>
                                        @if($case->follow_up_date->isPast())
                                            <p class="text-xs text-red-600 font-semibold">OVERDUE</p>
                                        @endif
                                    @else
                                        <p class="text-xs text-amber-600">No date set</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
