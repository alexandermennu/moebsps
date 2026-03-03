@extends('layouts.app')
@section('title', 'SIR Dashboard')
@section('page-title', 'School Incident Reporter (SIR)')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">SIR Dashboard</h2>
            <p class="text-sm text-gray-500">Overview of all school incidents reported through the system.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sir.incidents.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">View All Incidents</a>
            @if($canManage)
            <a href="{{ route('sir.incidents.create') }}" class="px-4 py-2 bg-red-700 text-white text-sm font-medium hover:bg-red-800 rounded-md">Report Incident</a>
            @endif
        </div>
    </div>

    {{-- Key Metrics --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white border border-gray-200 rounded-md p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalIncidents }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <p class="text-xs text-red-600 uppercase tracking-wide">Open</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $openIncidents }}</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-md p-4">
            <p class="text-xs text-orange-600 uppercase tracking-wide">Critical</p>
            <p class="text-2xl font-bold text-orange-700 mt-1">{{ $criticalIncidents }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-md p-4">
            <p class="text-xs text-amber-600 uppercase tracking-wide">Follow-Up Due</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $followUpDue }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <p class="text-xs text-green-600 uppercase tracking-wide">Resolved</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $closedIncidents }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
            <p class="text-xs text-blue-600 uppercase tracking-wide">Resolution Rate</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $resolutionRate }}%</p>
        </div>
    </div>

    {{-- Source Breakdown --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-blue-600 uppercase tracking-wide">Internal Reports</p>
                <p class="text-sm text-blue-500 mt-0.5">From ministry staff & counselors</p>
            </div>
            <p class="text-3xl font-bold text-blue-700">{{ $internalCount }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-md p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-green-600 uppercase tracking-wide">Public Reports</p>
                <p class="text-sm text-green-500 mt-0.5">From the public portal</p>
            </div>
            <p class="text-3xl font-bold text-green-700">{{ $publicCount }}</p>
        </div>
    </div>

    {{-- Urgent Alerts --}}
    @if($immediateAction > 0)
    <div class="bg-red-600 text-white rounded-md p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <div>
                <p class="font-semibold">{{ $immediateAction }} Incident(s) Require Immediate Action</p>
                <p class="text-sm text-red-100">These incidents have been flagged as requiring urgent response.</p>
            </div>
        </div>
        <a href="{{ route('sir.incidents.index', ['priority' => 'critical']) }}" class="px-4 py-2 bg-white text-red-700 text-sm font-medium hover:bg-red-50 rounded-md">View Now</a>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- By Incident Type --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">By Incident Type</h3>
            @forelse($byType as $type => $count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-{{ \App\Models\Incident::TYPES[$type] ? match($type) { 'srgbv' => 'red', 'disciplinary' => 'amber', 'safety' => 'orange', 'infrastructure' => 'blue', 'academic' => 'purple', 'health' => 'teal', default => 'gray' } : 'gray' }}-500"></span>
                    <span class="text-sm text-gray-700">{{ \App\Models\Incident::TYPES[$type] ?? ucfirst($type) }}</span>
                </div>
                <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400">No incidents yet.</p>
            @endforelse
        </div>

        {{-- By Status --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">By Status</h3>
            @forelse($byStatus as $status => $count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-{{ match($status) { 'reported' => 'red', 'under_review' => 'amber', 'under_investigation' => 'orange', 'action_taken' => 'blue', 'referred' => 'purple', 'resolved' => 'green', 'closed' => 'gray', default => 'gray' } }}-500"></span>
                    <span class="text-sm text-gray-700">{{ \App\Models\Incident::STATUSES[$status] ?? ucfirst($status) }}</span>
                </div>
                <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400">No incidents yet.</p>
            @endforelse
        </div>

        {{-- By Priority --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">By Priority</h3>
            @forelse($byPriority as $priority => $count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-{{ match($priority) { 'low' => 'gray', 'medium' => 'blue', 'high' => 'amber', 'critical' => 'red', default => 'gray' } }}-500"></span>
                    <span class="text-sm text-gray-700">{{ \App\Models\Incident::PRIORITIES[$priority] ?? ucfirst($priority) }}</span>
                </div>
                <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400">No incidents yet.</p>
            @endforelse
        </div>

        {{-- SRGBV Spotlight --}}
        <div class="bg-red-50 border border-red-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-red-900 uppercase tracking-wide mb-4">SRGBV Spotlight</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-red-700">Total SRGBV Cases</span>
                    <span class="text-sm font-bold text-red-900">{{ $srgbvTotal }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-red-700">Open SRGBV Cases</span>
                    <span class="text-sm font-bold text-red-900">{{ $srgbvOpen }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-red-700">Critical SRGBV Cases</span>
                    <span class="text-sm font-bold text-red-900">{{ $srgbvCritical }}</span>
                </div>
            </div>
            <a href="{{ route('sir.incidents.index', ['type' => 'srgbv']) }}" class="mt-4 inline-block text-xs text-red-700 hover:underline">View all SRGBV incidents →</a>
        </div>
    </div>

    {{-- Monthly Trend --}}
    @if(count($monthlyTrend) > 0)
    <div class="bg-white border border-gray-200 rounded-md p-6">
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Monthly Incident Trend (Last 12 Months)</h3>
        <div class="flex items-end gap-2 h-40">
            @php $maxVal = max($monthlyTrend) ?: 1; @endphp
            @foreach($monthlyTrend as $month => $count)
            <div class="flex-1 flex flex-col items-center gap-1">
                <span class="text-[10px] font-medium text-gray-600">{{ $count }}</span>
                <div class="w-full bg-red-600 rounded-t" style="height: {{ ($count / $maxVal) * 100 }}%"></div>
                <span class="text-[9px] text-gray-400">{{ \Carbon\Carbon::parse($month . '-01')->format('M') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Top Counties --}}
    @if(count($topCounties) > 0)
    <div class="bg-white border border-gray-200 rounded-md p-6">
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Top Counties by Incidents</h3>
        <div class="space-y-2">
            @php $maxCounty = max($topCounties) ?: 1; @endphp
            @foreach($topCounties as $county => $count)
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-700 w-32 truncate">{{ $county }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                    <div class="bg-red-600 h-full rounded-full" style="width: {{ ($count / $maxCounty) * 100 }}%"></div>
                </div>
                <span class="text-sm font-semibold text-gray-700 w-8 text-right">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Incidents --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Recent Incidents</h3>
            <div class="space-y-3">
                @forelse($recentIncidents as $incident)
                <a href="{{ route('sir.incidents.show', $incident) }}" class="block p-3 bg-gray-50 rounded-md hover:bg-gray-100 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->type_color }}-100 text-{{ $incident->type_color }}-700 rounded">{{ $incident->type_label }}</span>
                                <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->priority_color }}-100 text-{{ $incident->priority_color }}-700 rounded">{{ $incident->priority_label }}</span>
                                @if($incident->isPublicReport())
                                <span class="text-[10px] px-1.5 py-0.5 font-medium bg-green-100 text-green-700 rounded">Public</span>
                                @endif
                            </div>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $incident->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $incident->incident_number }} · {{ $incident->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->status_color }}-100 text-{{ $incident->status_color }}-700 rounded whitespace-nowrap">{{ $incident->status_label }}</span>
                    </div>
                </a>
                @empty
                <p class="text-sm text-gray-400">No incidents reported yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Follow-Up Due --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Follow-Up Due</h3>
            <div class="space-y-3">
                @forelse($followUpIncidents as $incident)
                <a href="{{ route('sir.incidents.show', $incident) }}" class="block p-3 bg-amber-50 rounded-md hover:bg-amber-100 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $incident->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $incident->incident_number }} · Due: {{ $incident->follow_up_date?->format('M d, Y') ?? 'Not set' }}</p>
                        </div>
                        @if($incident->assignee)
                        <span class="text-xs text-gray-500">{{ $incident->assignee->name }}</span>
                        @endif
                    </div>
                </a>
                @empty
                <p class="text-sm text-gray-400">No follow-ups due.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Resolution Stats Footer --}}
    @if($avgResolutionDays)
    <div class="bg-white border border-gray-200 rounded-md p-4 flex items-center justify-center gap-8">
        <div class="text-center">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Avg. Days to Resolve</p>
            <p class="text-xl font-bold text-gray-800">{{ $avgResolutionDays }}</p>
        </div>
        <div class="w-px h-8 bg-gray-200"></div>
        <div class="text-center">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Resolution Rate</p>
            <p class="text-xl font-bold text-green-700">{{ $resolutionRate }}%</p>
        </div>
    </div>
    @endif
</div>
@endsection
