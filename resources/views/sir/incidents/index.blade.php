@extends('layouts.app')
@section('title', $module === 'srgbv' ? 'SRGBV Cases' : 'Other Incidents')
@section('page-title', $module === 'srgbv' ? 'SRGBV Cases' : 'Other Incidents')
@section('content')
@php
    $isSrgbv = $module === 'srgbv';
    $indexRoute = $isSrgbv ? 'sir.srgbv.cases.index' : 'sir.other.incidents.index';
    $createRoute = $isSrgbv ? 'sir.srgbv.cases.create' : 'sir.other.incidents.create';
    $showRoute = $isSrgbv ? 'sir.srgbv.cases.show' : 'sir.other.incidents.show';
    $dashboardRoute = $isSrgbv ? 'sir.srgbv.dashboard' : 'sir.other.dashboard';
    $themeColor = $isSrgbv ? 'red' : 'blue';
@endphp
<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('sir.dashboard') }}" class="hover:text-gray-600">SIR</a>
                <span>›</span>
                <a href="{{ route($dashboardRoute) }}" class="hover:text-gray-600">{{ $isSrgbv ? 'SRGBV' : 'Other' }}</a>
                <span>›</span>
                <span class="text-gray-600">{{ $isSrgbv ? 'Cases' : 'Incidents' }}</span>
            </div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ $isSrgbv ? 'SRGBV Cases' : 'Other Incidents' }}</h2>
            <p class="text-sm text-gray-500">{{ $isSrgbv ? 'School-Related Gender-Based Violence reports.' : 'General school incident reports.' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route($dashboardRoute) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">Dashboard</a>
            <a href="{{ route($createRoute) }}" class="px-4 py-2 bg-{{ $themeColor }}-700 text-white text-sm font-medium hover:bg-{{ $themeColor }}-800 rounded-md">
                {{ $isSrgbv ? 'Report SRGBV Case' : 'Report Incident' }}
            </a>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white border border-gray-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $incidents->total() }}</p>
            <p class="text-xs text-gray-500">Total</p>
        </div>
        <div class="bg-{{ $themeColor }}-50 border border-{{ $themeColor }}-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-{{ $themeColor }}-700">{{ $openCount ?? 0 }}</p>
            <p class="text-xs text-{{ $themeColor }}-600">Open</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-orange-700">{{ $criticalCount ?? 0 }}</p>
            <p class="text-xs text-orange-600">Critical</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-green-700">{{ $closedCount ?? 0 }}</p>
            <p class="text-xs text-green-600">Resolved</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-purple-700">{{ $publicCount ?? 0 }}</p>
            <p class="text-xs text-purple-600">Public Reports</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route($indexRoute) }}" class="bg-white border border-gray-200 rounded-md p-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Number, title, name, school..."
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm w-52 focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
            </div>
            @if(!$isSrgbv)
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Type</label>
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
                    <option value="">All Types</option>
                    @foreach(\App\Models\Incident::TYPES as $key => $label)
                    @if($key !== 'srgbv')
                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Source</label>
                <select name="source" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
                    <option value="">All Sources</option>
                    @foreach(\App\Models\Incident::SOURCES as $key => $label)
                    <option value="{{ $key }}" {{ request('source') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Incident::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Priority</label>
                <select name="priority" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
                    <option value="">All Priorities</option>
                    @foreach(\App\Models\Incident::PRIORITIES as $key => $label)
                    <option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700 rounded-md">Filter</button>
            @if(request()->hasAny(['search', 'type', 'source', 'status', 'priority', 'date_from', 'date_to']))
            <a href="{{ route($indexRoute) }}" class="px-4 py-2 text-gray-500 text-sm hover:text-gray-700">Clear</a>
            @endif
        </div>
    </form>

    {{-- Incident Cards --}}
    <div class="space-y-3">
        @forelse($incidents as $incident)
        <a href="{{ route($showRoute, $incident) }}" class="block bg-white border border-gray-200 rounded-md p-4 hover:border-gray-300 hover:shadow-sm transition">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    {{-- Badges --}}
                    <div class="flex flex-wrap items-center gap-1.5 mb-2">
                        <span class="text-[10px] px-1.5 py-0.5 font-medium bg-gray-100 text-gray-600 rounded">{{ $incident->incident_number }}</span>
                        @if(!$isSrgbv)
                        <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->type_color }}-100 text-{{ $incident->type_color }}-700 rounded">{{ $incident->type_label }}</span>
                        @endif
                        <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->priority_color }}-100 text-{{ $incident->priority_color }}-700 rounded">{{ $incident->priority_label }}</span>
                        <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->source_color }}-100 text-{{ $incident->source_color }}-700 rounded">{{ $incident->source_label }}</span>
                        @if($incident->is_confidential)
                        <span class="text-[10px] px-1.5 py-0.5 font-medium bg-purple-100 text-purple-700 rounded">Confidential</span>
                        @endif
                        @if($incident->immediate_action_required)
                        <span class="text-[10px] px-1.5 py-0.5 font-medium bg-red-500 text-white rounded">URGENT</span>
                        @endif
                    </div>

                    {{-- Title & meta --}}
                    <h3 class="text-sm font-semibold text-gray-800">{{ $incident->title }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $incident->category_label }}
                        · {{ $incident->incident_date?->format('M d, Y') ?? 'Date unknown' }}
                        @if($incident->school_name) · {{ $incident->school_name }} @endif
                        @if($incident->school_county) ({{ $incident->school_county }}) @endif
                    </p>

                    {{-- Reporter & Assignee --}}
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                        @if($incident->reporter)
                        <span>Reported by: {{ $incident->reporter->name }}</span>
                        @elseif($incident->public_reporter_name)
                        <span>Public: {{ $incident->public_reporter_name }}</span>
                        @else
                        <span>Anonymous Report</span>
                        @endif

                        @if($incident->assignee)
                        <span>· Assigned: {{ $incident->assignee->name }}</span>
                        @endif

                        <span>· {{ $incident->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- Status badge --}}
                <span class="text-[10px] px-2 py-1 font-medium bg-{{ $incident->status_color }}-100 text-{{ $incident->status_color }}-700 rounded whitespace-nowrap">{{ $incident->status_label }}</span>
            </div>
        </a>
        @empty
        <div class="bg-white border border-gray-200 rounded-md p-8 text-center">
            <p class="text-gray-400 text-sm">No {{ $isSrgbv ? 'SRGBV cases' : 'incidents' }} found.</p>
            <a href="{{ route($createRoute) }}" class="mt-2 inline-block text-sm text-{{ $themeColor }}-700 hover:underline">Report the first {{ $isSrgbv ? 'case' : 'incident' }} →</a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $incidents->links() }}
    </div>
</div>
@endsection
