@extends('layouts.app')
@section('title', 'Other Incidents Dashboard')
@section('page-title', 'Other Incidents')
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
                <span class="text-gray-600">Other Incidents</span>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Other Incidents Dashboard</h2>
            <p class="text-sm text-gray-500">Managing Disciplinary, Safety, Infrastructure, Academic, Health & General incidents.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sir.dashboard') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to SIR
            </a>
            <a href="{{ route('sir.other.public-reporters') }}" class="inline-flex items-center gap-2 bg-white border border-green-300 hover:bg-green-50 text-green-700 font-medium px-4 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Public Reporters
            </a>
            <a href="{{ route('sir.other.incidents.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                View All Cases
            </a>
            @if($canManage)
            <a href="{{ route('sir.other.incidents.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Report
            </a>
            @endif
        </div>
    </div>

    {{-- Alert Banner --}}
    @if($immediateAction > 0)
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-orange-800">{{ $immediateAction }} {{ Str::plural('Incident', $immediateAction) }} Require Immediate Attention</p>
            <p class="text-xs text-orange-600">These incidents have been flagged as critical and need urgent response.</p>
        </div>
        <a href="{{ route('sir.other.dashboard', ['urgent' => '1']) }}" class="shrink-0 text-sm font-medium text-orange-700 hover:text-orange-800 flex items-center gap-1">
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

        {{-- Critical --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="text-xs text-orange-600 font-medium">Critical</span>
            </div>
            <p class="text-2xl font-bold text-orange-600">{{ $criticalIncidents }}</p>
            <p class="text-xs text-gray-500">Urgent Cases</p>
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

    {{-- Recent Incidents Preview --}}
    <div class="bg-white border border-gray-200 rounded-lg">
        {{-- Section Header --}}
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Recent Incidents</h3>
                <p class="text-xs text-gray-500">Latest 3 Other Incident reports</p>
            </div>
            <a href="{{ route('sir.other.incidents.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                View All Incidents
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Case Cards List --}}
        <div class="divide-y divide-gray-100">
            @forelse($recentIncidents as $incident)
            <div class="p-4 hover:bg-gray-50 transition group">
                <div class="flex items-start justify-between gap-4">
                    <a href="{{ route('sir.other.incidents.show', $incident) }}" class="flex-1 min-w-0">
                        {{-- Badges Row --}}
                        <div class="flex flex-wrap items-center gap-1.5 mb-2">
                            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-gray-100 text-gray-600 rounded">{{ $incident->incident_number }}</span>
                            @php
                                $typeColors = [
                                    'disciplinary' => 'bg-amber-100 text-amber-700',
                                    'safety' => 'bg-orange-100 text-orange-700',
                                    'infrastructure' => 'bg-blue-100 text-blue-700',
                                    'academic' => 'bg-purple-100 text-purple-700',
                                    'health' => 'bg-teal-100 text-teal-700',
                                    'other' => 'bg-gray-100 text-gray-700',
                                ];
                                $priorityColors = [
                                    'low' => 'bg-green-100 text-green-700',
                                    'medium' => 'bg-blue-100 text-blue-700',
                                    'high' => 'bg-orange-100 text-orange-700',
                                    'critical' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="text-[10px] px-1.5 py-0.5 font-medium rounded {{ $typeColors[$incident->type] ?? 'bg-gray-100 text-gray-700' }}">{{ $incident->type_label }}</span>
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
                            Reported by: {{ $incident->reporter?->role_label ?? ($incident->isPublicReport() ? 'Public Report' : 'Unknown') }}
                            @if($incident->reporter) ({{ $incident->reporter->name }}) @elseif($incident->public_reporter_name) ({{ $incident->public_reporter_name }}) @endif
                            · {{ $incident->created_at->diffForHumans() }}
                        </p>
                    </a>

                    {{-- Right side: Status + Actions --}}
                    <div class="shrink-0 flex items-center gap-3">
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
                        
                        {{-- Actions Dropdown --}}
                        @if($canManage)
                        <div class="relative group/menu">
                            <button class="p-1.5 rounded hover:bg-gray-200 text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                            </button>
                            <div class="absolute right-0 mt-1 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-10 py-1 hidden group-hover/menu:block">
                                <a href="{{ route('sir.other.incidents.show', $incident) }}" class="block px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50">View</a>
                                <a href="{{ route('sir.other.incidents.edit', $incident) }}" class="block px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50">Edit</a>
                                <form method="POST" action="{{ route('sir.other.incidents.destroy', $incident) }}" onsubmit="return confirm('Delete this incident permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full text-left px-3 py-1.5 text-sm text-red-600 hover:bg-red-50">Delete</button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="px-4 py-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-sm text-gray-500">No incidents found.</p>
                    @if($canManage)
                    <a href="{{ route('sir.other.incidents.create') }}" class="mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium">Create first report →</a>
                    @endif
                </div>
            </div>
            @endforelse
        </div>

        {{-- View All Link (only if there are more incidents) --}}
        @if($totalIncidents > 3)
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/50">
            <a href="{{ route('sir.other.incidents.index') }}" class="flex items-center justify-center gap-2 text-sm text-gray-600 hover:text-blue-600 font-medium transition">
                View all {{ $totalIncidents }} incidents
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        @endif
    </div>

    {{-- Charts Row: Trends + Incidents by Type --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Monthly Trends Chart --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Incident Trends</h3>
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

        {{-- Incidents by Category (Bar Chart) --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Incidents by Category</h3>
                <div class="flex items-center gap-3 text-xs flex-wrap" id="categoryLegend">
                    {{-- Legend populated by JS --}}
                </div>
            </div>
            <div class="h-52">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Second Row: Status Breakdown + Liberia Map --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Status Breakdown --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Status Breakdown</h3>
            <div class="flex items-center gap-6">
                <div class="relative w-36 h-36 shrink-0">
                    <canvas id="statusChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <span class="text-xl font-bold text-gray-800">{{ $totalIncidents }}</span>
                            <p class="text-xs text-gray-500">Total</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 flex-1">
                    @php
                        $statusColors = [
                            'reported' => 'bg-red-500',
                            'under_review' => 'bg-amber-500',
                            'under_investigation' => 'bg-orange-500',
                            'action_taken' => 'bg-blue-500',
                            'referred' => 'bg-purple-500',
                            'resolved' => 'bg-green-500',
                            'closed' => 'bg-gray-400',
                        ];
                    @endphp
                    @foreach(array_slice($byStatus, 0, 4, true) as $status => $count)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $statusColors[$status] ?? 'bg-gray-400' }}"></span>
                            <span class="text-gray-700">{{ \App\Models\Incident::STATUSES[$status] ?? ucfirst($status) }}</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Liberia County Map (Leaflet + GeoJSON) --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Incidents by County</h3>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <span class="w-3 h-3 rounded" style="background:#dbeafe"></span>
                    <span>Low</span>
                    <span class="w-3 h-3 rounded ml-1" style="background:#93c5fd"></span>
                    <span class="w-3 h-3 rounded" style="background:#3b82f6"></span>
                    <span class="w-3 h-3 rounded" style="background:#1e40af"></span>
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
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-container { background: #f8fafc; font-family: inherit; }
    .info-box { padding: 8px 12px; background: white; border-radius: 6px; box-shadow: 0 1px 5px rgba(0,0,0,0.2); }
    .info-box h4 { margin: 0 0 4px 0; font-size: 13px; font-weight: 600; color: #111827; }
    .info-box p { margin: 0; font-size: 12px; color: #6b7280; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawCountyData = @json($countyData ?? []);
    
    // Map to normalize GeoJSON county names to database county names
    const countyNameMap = {
        'Bomi': 'Bomi County',
        'Bong': 'Bong County',
        'Gbarpolu': 'Gbarpolu County',
        'Grand Bassa': 'Grand Bassa County',
        'Grand Cape Mount': 'Grand Cape Mount County',
        'Grand Gedeh': 'Grand Gedeh County',
        'Grand Kru': 'Grand Kru County',
        'Lofa': 'Lofa County',
        'Margibi': 'Margibi County',
        'Maryland': 'Maryland County',
        'Montserrado': 'Montserrado County',
        'Nimba': 'Nimba County',
        'River Cess': 'River Cess County',
        'River Gee': 'River Gee County',
        'Sinoe': 'Sinoe County'
    };
    
    // Initialize Leaflet map centered on Liberia
    const map = L.map('liberiaMap', {
        zoomControl: false,
        attributionControl: false,
        dragging: true,
        scrollWheelZoom: false
    }).setView([6.5, -9.5], 7);
    
    // Add zoom control to top-right
    L.control.zoom({ position: 'topright' }).addTo(map);

    // Helper to get county count from data
    function getCountyCount(geoJsonName) {
        const dbName = countyNameMap[geoJsonName];
        return rawCountyData[dbName] || rawCountyData[geoJsonName] || rawCountyData[geoJsonName + ' County'] || 0;
    }

    // Color scale function (blue theme for Other Incidents)
    const maxCount = Math.max(...Object.values(rawCountyData), 1);
    function getColor(count) {
        if (!count || count === 0) return '#eff6ff';
        const ratio = count / maxCount;
        if (ratio < 0.25) return '#bfdbfe';
        if (ratio < 0.5) return '#60a5fa';
        if (ratio < 0.75) return '#3b82f6';
        return '#1e40af';
    }

    function style(feature) {
        const count = getCountyCount(feature.properties.name);
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
        const count = props ? getCountyCount(props.name) : null;
        const displayName = props ? (countyNameMap[props.name] || props.name) : null;
        this._div.innerHTML = props
            ? `<h4>${displayName}</h4><p>${count} incident${count !== 1 ? 's' : ''} reported</p>`
            : '<p style="color:#9ca3af">Hover over a county</p>';
    };
    info.addTo(map);

    // Interaction handlers
    let geojsonLayer = null;
    
    function highlightFeature(e) {
        const layer = e.target;
        layer.setStyle({ weight: 3, color: '#1f2937', fillOpacity: 0.95 });
        layer.bringToFront();
        info.update(layer.feature.properties);
    }
    function resetHighlight(e) {
        if (geojsonLayer) geojsonLayer.resetStyle(e.target);
        info.update();
    }
    function onEachFeature(feature, layer) {
        layer.on({ mouseover: highlightFeature, mouseout: resetHighlight });
    }

    // Fetch and add GeoJSON layer from external file
    fetch('/data/liberia-counties.json')
        .then(response => response.json())
        .then(liberiaGeoJSON => {
            geojsonLayer = L.geoJSON(liberiaGeoJSON, { style, onEachFeature }).addTo(map);
            map.fitBounds(geojsonLayer.getBounds(), { padding: [10, 10] });
        })
        .catch(error => {
            console.error('Error loading Liberia GeoJSON:', error);
            map.setView([6.5, -9.5], 7);
        });

    // Trends Line Chart
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

    // Incidents by Category Bar Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const categoryData = @json($byCategory ?? []);
        
        // All categories for Other Incidents
        const allCategories = [
            { key: 'student_misconduct', label: 'Misconduct' },
            { key: 'teacher_misconduct', label: 'Staff Misconduct' },
            { key: 'substance_abuse', label: 'Substance' },
            { key: 'fighting', label: 'Fighting' },
            { key: 'vandalism', label: 'Vandalism' },
            { key: 'theft', label: 'Theft' },
            { key: 'fire', label: 'Fire' },
            { key: 'structural_hazard', label: 'Structural' },
            { key: 'sanitation', label: 'Sanitation' },
            { key: 'accident_injury', label: 'Accident' },
            { key: 'bullying', label: 'Bullying' },
            { key: 'truancy', label: 'Truancy' },
            { key: 'other', label: 'Other' }
        ];
        
        const labels = allCategories.map(c => c.label);
        const values = allCategories.map(c => categoryData[c.key] || 0);
        
        // Colors for each category
        const colors = ['#EF4444', '#8B5CF6', '#F59E0B', '#3B82F6', '#14B8A6', '#10B981', '#F97316', '#EC4899', '#6366F1', '#84CC16', '#06B6D4', '#A855F7', '#6B7280'];
        
        // Build legend - only show categories that have data
        const legendContainer = document.getElementById('categoryLegend');
        if (legendContainer) {
            legendContainer.innerHTML = allCategories
                .filter((c, i) => values[i] > 0)
                .map((c, i) => {
                    const originalIndex = allCategories.findIndex(cat => cat.key === c.key);
                    return `<span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full" style="background:${colors[originalIndex]}"></span> ${c.label}</span>`;
                }).join('');
        }

        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderRadius: 4,
                    barThickness: 20,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 8 }, maxRotation: 45, minRotation: 45 } }
                }
            }
        });
    }

    // Status Donut Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const statusData = @json($byStatus ?? []);
        const statuses = ['reported', 'under_review', 'under_investigation', 'action_taken', 'resolved', 'closed'];
        const statusColors = ['#EF4444', '#F59E0B', '#F97316', '#3B82F6', '#10B981', '#6B7280'];
        const statusValues = statuses.map(s => statusData[s] || 0);

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Reported', 'Under Review', 'Investigation', 'Action Taken', 'Resolved', 'Closed'],
                datasets: [{
                    data: statusValues.some(v => v > 0) ? statusValues : [1, 1, 1, 1, 1, 1],
                    backgroundColor: statusColors,
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
