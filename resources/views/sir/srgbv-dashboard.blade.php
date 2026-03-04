@extends('layouts.app')
@section('title', 'SRGBV Dashboard')
@section('page-title', 'SRGBV Dashboard')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('sir.dashboard') }}" class="hover:text-gray-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    SIR
                </a>
                <span>›</span>
                <span class="text-gray-600">SRGBV</span>
            </div>
            <h2 class="text-xl font-bold text-gray-900">SRGBV Dashboard</h2>
            <p class="text-sm text-gray-500">Managing reports of School-Related Gender-Based Violence and Bullying.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sir.dashboard') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to SIR
            </a>
            @if($canManage)
            <a href="{{ route('sir.srgbv.cases.create') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Report
            </a>
            @endif
        </div>
    </div>

    {{-- Alert Banner --}}
    @if($immediateAction > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-red-800">{{ $immediateAction }} High Risk {{ Str::plural('Case', $immediateAction) }} Require Immediate Attention</p>
            <p class="text-xs text-red-600">These cases have been flagged as critical and need urgent review.</p>
        </div>
        <a href="{{ route('sir.srgbv.cases.index', ['priority' => 'critical']) }}" class="shrink-0 text-sm font-medium text-red-700 hover:text-red-800 flex items-center gap-1">
            View Cases
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    @endif

    {{-- Stat Cards (5 cards) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        {{-- Active Cases --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs text-green-600 font-medium">Active</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $openIncidents }}</p>
            <p class="text-xs text-gray-500">Open Cases</p>
        </div>

        {{-- New Today --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-xs text-blue-600 font-medium">Today</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $newToday ?? 0 }}</p>
            <p class="text-xs text-gray-500">New Reports</p>
        </div>

        {{-- High Risk --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="text-xs text-red-600 font-medium">Critical</span>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $criticalIncidents }}</p>
            <p class="text-xs text-gray-500">High Risk Cases</p>
        </div>

        {{-- Resolved This Month --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs text-purple-600 font-medium">Month</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $resolvedThisMonth ?? 0 }}</p>
            <p class="text-xs text-gray-500">Resolved</p>
        </div>

        {{-- Avg Response Time --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs text-amber-600 font-medium">Avg</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $avgResolutionDays ?? '—' }}</p>
            <p class="text-xs text-gray-500">Days to Resolve</p>
        </div>
    </div>

    {{-- Charts Row: Trends + Cases by Category --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- SRGBV Trends Chart --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">SRGBV Trends</h3>
                <div class="flex items-center gap-1 text-xs">
                    <button type="button" class="px-2.5 py-1 rounded bg-blue-100 text-blue-700 font-medium">12M</button>
                    <button type="button" class="px-2.5 py-1 rounded text-gray-500 hover:bg-gray-100">6M</button>
                    <button type="button" class="px-2.5 py-1 rounded text-gray-500 hover:bg-gray-100">3M</button>
                </div>
            </div>
            <div class="h-52">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>

        {{-- Cases by Category (Bar Chart) --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Cases by Category</h3>
                <div class="flex items-center gap-2 text-xs">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-red-500"></span> Physical</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-purple-500"></span> Sexual</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-amber-500"></span> Emotional</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-blue-500"></span> Bullying</span>
                </div>
            </div>
            <div class="h-52">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Second Row: Victim Gender + Liberia Map --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Victim Category Donut --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Victim Category</h3>
            <div class="flex items-center gap-6">
                <div class="relative w-36 h-36 shrink-0">
                    <canvas id="genderChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        @php $totalGender = array_sum($byGender) ?: 1; $femalePercent = isset($byGender['female']) ? round(($byGender['female'] / $totalGender) * 100) : 0; @endphp
                        <div class="text-center">
                            <span class="text-xl font-bold text-gray-800">{{ $femalePercent }}%</span>
                            <p class="text-xs text-gray-500">Female</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-3 flex-1">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-sm text-gray-700">Female</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $byGender['female'] ?? 0 }} ({{ $femalePercent }}%)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <span class="text-sm text-gray-700">Male</span>
                        </div>
                        @php $malePercent = isset($byGender['male']) ? round(($byGender['male'] / $totalGender) * 100) : 0; @endphp
                        <span class="text-sm font-semibold text-gray-900">{{ $byGender['male'] ?? 0 }} ({{ $malePercent }}%)</span>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total Victims</span>
                            <span class="text-sm font-semibold text-gray-900">{{ array_sum($byGender) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Liberia County Map (Leaflet + GeoJSON) --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Cases by County</h3>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <span class="w-3 h-3 rounded" style="background:#fee2e2"></span>
                    <span>Low</span>
                    <span class="w-3 h-3 rounded ml-1" style="background:#fca5a5"></span>
                    <span class="w-3 h-3 rounded" style="background:#ef4444"></span>
                    <span class="w-3 h-3 rounded" style="background:#991b1b"></span>
                    <span>High</span>
                </div>
            </div>
            <div class="flex gap-4">
                {{-- Leaflet Map Container --}}
                <div class="flex-1 h-[280px] rounded-lg overflow-hidden border border-gray-200" id="liberiaMap"></div>
                {{-- Top Counties List --}}
                <div class="w-40 shrink-0">
                    <p class="text-xs text-gray-500 mb-2 font-medium">Top Counties</p>
                    <div class="space-y-2">
                        @forelse(array_slice($topCounties, 0, 5, true) as $county => $count)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-700 truncate">{{ $county }}</span>
                            <span class="font-semibold text-gray-900">{{ $count }}</span>
                        </div>
                        @empty
                        <p class="text-xs text-gray-400">No data yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SRGBV Incident Reports --}}
    <div class="bg-white border border-gray-200 rounded-lg">
        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('sir.srgbv.dashboard') }}" class="p-4 border-b border-gray-200">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Number, title, name, school..."
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm w-52 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Source</label>
                    <select name="source" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Sources</option>
                        @foreach(\App\Models\Incident::SOURCES as $key => $label)
                        <option value="{{ $key }}" {{ request('source') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Status</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Statuses</option>
                        @foreach(\App\Models\Incident::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Priority</label>
                    <select name="priority" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Priorities</option>
                        @foreach(\App\Models\Incident::PRIORITIES as $key => $label)
                        <option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700 rounded-md">Filter</button>
                @if(request()->hasAny(['search', 'source', 'status', 'priority', 'date_from', 'date_to']))
                <a href="{{ route('sir.srgbv.dashboard') }}" class="px-4 py-2 text-gray-500 text-sm hover:text-gray-700">Clear</a>
                @endif
            </div>
        </form>

        {{-- Case Cards List --}}
        <div class="divide-y divide-gray-100">
            @forelse($recentIncidents as $incident)
            <a href="{{ route('sir.srgbv.cases.show', $incident) }}" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        {{-- Badges Row --}}
                        <div class="flex flex-wrap items-center gap-1.5 mb-2">
                            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-gray-100 text-gray-600 rounded">{{ $incident->incident_number }}</span>
                            @php
                                $priorityColors = [
                                    'low' => 'bg-green-100 text-green-700',
                                    'medium' => 'bg-blue-100 text-blue-700',
                                    'high' => 'bg-orange-100 text-orange-700',
                                    'critical' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="text-[10px] px-1.5 py-0.5 font-medium rounded {{ $priorityColors[$incident->priority] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($incident->priority) }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-blue-100 text-blue-700 rounded">{{ $incident->source_label }}</span>
                            @if($incident->is_confidential)
                            <span class="text-[10px] px-1.5 py-0.5 font-medium text-red-600 rounded">Confidential</span>
                            @endif
                            @if($incident->immediate_action_required)
                            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-red-500 text-white rounded">URGENT</span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h3 class="text-sm font-semibold text-gray-800">{{ $incident->title }}</h3>

                        {{-- Meta Info --}}
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $incident->category_label }}
                            · {{ $incident->incident_date?->format('M d, Y') ?? 'Date unknown' }}
                            @if($incident->school_name) · {{ $incident->school_name }} @endif
                            @if($incident->school_county) ({{ $incident->school_county }}) @endif
                        </p>

                        {{-- Reporter --}}
                        <p class="text-xs text-gray-400 mt-1">
                            Reported by: {{ $incident->reporter_role ?? 'Unknown' }}
                            · {{ $incident->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Status Badge (right side) --}}
                    <div class="shrink-0">
                        @php
                            $statusColors = [
                                'reported' => 'text-blue-600',
                                'under_review' => 'text-amber-600',
                                'under_investigation' => 'text-orange-600',
                                'action_taken' => 'text-purple-600',
                                'referred' => 'text-indigo-600',
                                'resolved' => 'text-green-600',
                                'closed' => 'text-gray-500',
                            ];
                        @endphp
                        <span class="text-xs font-medium {{ $statusColors[$incident->status] ?? 'text-gray-600' }}">{{ $incident->status_label }}</span>
                    </div>
                </div>
            </a>
            @empty
            <div class="px-4 py-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-sm text-gray-500">No SRGBV cases found.</p>
                    @if($canManage)
                    <a href="{{ route('sir.srgbv.cases.create') }}" class="mt-2 text-sm text-red-600 hover:text-red-700 font-medium">Create first report →</a>
                    @endif
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($recentIncidents->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $recentIncidents->links() }}
        </div>
        @elseif($recentIncidents->count() > 0)
        <div class="p-4 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-500">Showing {{ $recentIncidents->count() }} of {{ $totalIncidents }} cases</p>
        </div>
        @endif
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-container { background: #f8fafc; font-family: inherit; }
    .county-label { font-size: 9px; font-weight: 500; fill: #374151; }
    .info-box { padding: 8px 12px; background: white; border-radius: 6px; box-shadow: 0 1px 5px rgba(0,0,0,0.2); }
    .info-box h4 { margin: 0 0 4px 0; font-size: 13px; font-weight: 600; color: #111827; }
    .info-box p { margin: 0; font-size: 12px; color: #6b7280; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countyData = @json($countyData ?? []);
    
    // Initialize Leaflet map centered on Liberia
    const map = L.map('liberiaMap', {
        zoomControl: false,
        attributionControl: false,
        dragging: true,
        scrollWheelZoom: false
    }).setView([6.5, -9.5], 7);
    
    // Add zoom control to top-right
    L.control.zoom({ position: 'topright' }).addTo(map);

    // Liberia Counties GeoJSON (simplified official boundaries)
    const liberiaGeoJSON = {
        "type": "FeatureCollection",
        "features": [
            {"type":"Feature","properties":{"name":"Bomi"},"geometry":{"type":"Polygon","coordinates":[[[-11.0,6.9],[-10.8,6.9],[-10.7,6.75],[-10.8,6.55],[-10.95,6.5],[-11.1,6.6],[-11.0,6.9]]]}},
            {"type":"Feature","properties":{"name":"Bong"},"geometry":{"type":"Polygon","coordinates":[[[-10.3,7.4],[-9.8,7.5],[-9.4,7.3],[-9.3,6.9],[-9.6,6.7],[-10.1,6.7],[-10.4,6.9],[-10.5,7.2],[-10.3,7.4]]]}},
            {"type":"Feature","properties":{"name":"Gbarpolu"},"geometry":{"type":"Polygon","coordinates":[[[-10.8,7.5],[-10.3,7.4],[-10.5,7.2],[-10.4,6.9],[-10.7,6.75],[-10.8,6.9],[-11.0,6.9],[-11.2,7.2],[-10.8,7.5]]]}},
            {"type":"Feature","properties":{"name":"Grand Bassa"},"geometry":{"type":"Polygon","coordinates":[[[-10.1,6.7],[-9.6,6.7],[-9.3,6.4],[-9.1,6.1],[-9.4,5.9],[-9.8,5.95],[-10.05,6.15],[-10.2,6.4],[-10.1,6.7]]]}},
            {"type":"Feature","properties":{"name":"Grand Cape Mount"},"geometry":{"type":"Polygon","coordinates":[[[-11.5,7.4],[-11.2,7.2],[-11.0,6.9],[-11.1,6.6],[-11.4,6.9],[-11.5,7.1],[-11.5,7.4]]]}},
            {"type":"Feature","properties":{"name":"Grand Gedeh"},"geometry":{"type":"Polygon","coordinates":[[[-8.5,7.0],[-8.0,6.9],[-7.8,6.5],[-8.0,6.0],[-8.3,5.7],[-8.7,5.9],[-9.0,6.3],[-9.3,6.4],[-9.3,6.9],[-8.9,7.1],[-8.5,7.0]]]}},
            {"type":"Feature","properties":{"name":"Grand Kru"},"geometry":{"type":"Polygon","coordinates":[[[-8.3,5.1],[-8.0,5.0],[-7.7,4.9],[-7.5,4.55],[-7.8,4.4],[-8.2,4.5],[-8.5,4.8],[-8.3,5.1]]]}},
            {"type":"Feature","properties":{"name":"Lofa"},"geometry":{"type":"Polygon","coordinates":[[[-10.8,8.5],[-10.2,8.6],[-9.5,8.4],[-9.3,8.0],[-9.4,7.6],[-9.8,7.5],[-10.3,7.4],[-10.8,7.5],[-11.2,7.8],[-11.0,8.2],[-10.8,8.5]]]}},
            {"type":"Feature","properties":{"name":"Margibi"},"geometry":{"type":"Polygon","coordinates":[[[-10.5,6.55],[-10.2,6.4],[-10.05,6.15],[-10.3,6.05],[-10.6,6.2],[-10.6,6.4],[-10.5,6.55]]]}},
            {"type":"Feature","properties":{"name":"Maryland"},"geometry":{"type":"Polygon","coordinates":[[[-7.7,4.9],[-7.4,4.7],[-7.35,4.35],[-7.7,4.3],[-7.8,4.4],[-7.5,4.55],[-7.7,4.9]]]}},
            {"type":"Feature","properties":{"name":"Montserrado"},"geometry":{"type":"Polygon","coordinates":[[[-10.95,6.5],[-10.8,6.55],[-10.6,6.4],[-10.6,6.2],[-10.8,6.1],[-11.0,6.2],[-10.95,6.5]]]}},
            {"type":"Feature","properties":{"name":"Nimba"},"geometry":{"type":"Polygon","coordinates":[[[-9.4,7.6],[-9.3,8.0],[-8.8,8.3],[-8.3,7.8],[-8.3,7.3],[-8.5,7.0],[-8.9,7.1],[-9.3,6.9],[-9.4,7.3],[-9.4,7.6]]]}},
            {"type":"Feature","properties":{"name":"River Cess"},"geometry":{"type":"Polygon","coordinates":[[[-9.4,5.9],[-9.1,6.1],[-8.7,5.9],[-8.5,5.5],[-8.7,5.3],[-9.1,5.4],[-9.4,5.6],[-9.4,5.9]]]}},
            {"type":"Feature","properties":{"name":"River Gee"},"geometry":{"type":"Polygon","coordinates":[[[-8.3,5.7],[-8.0,6.0],[-7.8,5.7],[-7.7,5.3],[-8.0,5.0],[-8.3,5.1],[-8.5,5.5],[-8.3,5.7]]]}},
            {"type":"Feature","properties":{"name":"Sinoe"},"geometry":{"type":"Polygon","coordinates":[[[-8.7,5.3],[-8.5,5.5],[-8.3,5.1],[-8.5,4.8],[-9.0,4.9],[-9.2,5.2],[-8.7,5.3]]]}}
        ]
    };

    // Color scale function (red theme for SRGBV)
    const maxCount = Math.max(...Object.values(countyData), 1);
    function getColor(count) {
        if (!count || count === 0) return '#fef2f2';
        const ratio = count / maxCount;
        if (ratio < 0.25) return '#fecaca';
        if (ratio < 0.5) return '#f87171';
        if (ratio < 0.75) return '#ef4444';
        return '#991b1b';
    }

    function style(feature) {
        const count = countyData[feature.properties.name] || 0;
        return {
            fillColor: getColor(count),
            weight: 1.5,
            opacity: 1,
            color: '#ffffff',
            fillOpacity: 0.85
        };
    }

    // Info control
    const info = L.control({ position: 'bottomleft' });
    info.onAdd = function() {
        this._div = L.DomUtil.create('div', 'info-box');
        this.update();
        return this._div;
    };
    info.update = function(props) {
        const count = props ? (countyData[props.name] || 0) : null;
        this._div.innerHTML = props
            ? `<h4>${props.name}</h4><p>${count} case${count !== 1 ? 's' : ''} reported</p>`
            : '<p style="color:#9ca3af">Hover over a county</p>';
    };
    info.addTo(map);

    // Interaction handlers
    function highlightFeature(e) {
        const layer = e.target;
        layer.setStyle({ weight: 3, color: '#1f2937', fillOpacity: 0.95 });
        layer.bringToFront();
        info.update(layer.feature.properties);
    }
    function resetHighlight(e) {
        geojsonLayer.resetStyle(e.target);
        info.update();
    }
    function onEachFeature(feature, layer) {
        layer.on({ mouseover: highlightFeature, mouseout: resetHighlight });
    }

    // Add GeoJSON layer
    const geojsonLayer = L.geoJSON(liberiaGeoJSON, { style, onEachFeature }).addTo(map);
    
    // Fit bounds to Liberia
    map.fitBounds(geojsonLayer.getBounds(), { padding: [10, 10] });

    const trendsCtx = document.getElementById('trendsChart');
    if (trendsCtx) {
        const trendsData = @json($monthlyTrend);
        const labels = Object.keys(trendsData).map(m => {
            const [year, month] = m.split('-');
            return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short' });
        });
        const values = Object.values(trendsData);

        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['No data'],
                datasets: [{
                    data: values.length ? values : [0],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3B82F6',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const categoryData = @json($byCategory ?? []);
        const categories = ['physical_violence', 'sexual_violence', 'emotional_abuse', 'bullying'];
        const colors = ['#EF4444', '#8B5CF6', '#F59E0B', '#3B82F6'];
        const categoryLabels = ['Physical', 'Sexual', 'Emotional', 'Bullying'];

        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categories.map(c => categoryData[c] || 0),
                    backgroundColor: colors,
                    borderRadius: 4,
                    barThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        const genderData = @json($byGender);
        const genderValues = [genderData['female'] || 0, genderData['male'] || 0];

        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Female', 'Male'],
                datasets: [{
                    data: genderValues.some(v => v > 0) ? genderValues : [1, 1],
                    backgroundColor: ['#3B82F6', '#FBBF24'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
@endsection
