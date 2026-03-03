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
            <a href="{{ route('sir.incidents.create', ['type' => 'srgbv']) }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition">
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
        <a href="{{ route('sir.incidents.index', ['module' => 'srgbv', 'priority' => 'critical']) }}" class="shrink-0 text-sm font-medium text-red-700 hover:text-red-800 flex items-center gap-1">
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

        {{-- Liberia County Map --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Cases by County</h3>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <span class="w-3 h-3 rounded bg-blue-100"></span>
                    <span>Low</span>
                    <span class="w-3 h-3 rounded bg-blue-300 ml-1"></span>
                    <span class="w-3 h-3 rounded bg-blue-500"></span>
                    <span class="w-3 h-3 rounded bg-blue-700"></span>
                    <span>High</span>
                </div>
            </div>
            <div class="flex gap-4">
                {{-- SVG Map of Liberia (Accurate county boundaries) --}}
                <div class="flex-1 min-h-[220px]" id="liberiaMapContainer">
                    <svg viewBox="0 0 500 450" class="w-full h-full" id="liberiaMap">
                        {{-- Lofa County (Northwest, borders Guinea & Sierra Leone) --}}
                        <path id="county-lofa" d="M95,20 L145,15 L195,25 L235,45 L245,85 L225,115 L185,125 L145,115 L105,95 L85,55 Z" class="county-path" data-county="Lofa"/>
                        {{-- Gbarpolu County (West-central) --}}
                        <path id="county-gbarpolu" d="M45,95 L85,55 L105,95 L145,115 L155,155 L125,185 L75,175 L35,135 Z" class="county-path" data-county="Gbarpolu"/>
                        {{-- Bong County (Central) --}}
                        <path id="county-bong" d="M145,115 L185,125 L225,115 L265,135 L275,175 L245,205 L195,215 L155,195 L155,155 Z" class="county-path" data-county="Bong"/>
                        {{-- Nimba County (Northeast, largest county, borders Guinea & Ivory Coast) --}}
                        <path id="county-nimba" d="M225,115 L245,85 L285,55 L345,45 L385,75 L395,135 L365,185 L315,195 L275,175 L265,135 Z" class="county-path" data-county="Nimba"/>
                        {{-- Grand Cape Mount County (Far west coast) --}}
                        <path id="county-grandcapemount" d="M5,135 L35,135 L75,175 L65,215 L35,235 L5,215 L0,175 Z" class="county-path" data-county="Grand Cape Mount"/>
                        {{-- Bomi County (West, small) --}}
                        <path id="county-bomi" d="M35,135 L75,175 L125,185 L115,225 L65,215 Z" class="county-path" data-county="Bomi"/>
                        {{-- Montserrado County (Capital Monrovia, west coast) --}}
                        <path id="county-montserrado" d="M65,215 L115,225 L135,255 L115,285 L75,275 L45,255 L35,235 Z" class="county-path" data-county="Montserrado"/>
                        {{-- Margibi County (Central coast) --}}
                        <path id="county-margibi" d="M115,225 L155,195 L195,215 L185,255 L135,255 Z" class="county-path" data-county="Margibi"/>
                        {{-- Grand Bassa County (Central coast) --}}
                        <path id="county-grandbassa" d="M135,255 L185,255 L245,275 L235,315 L175,335 L115,315 L115,285 Z" class="county-path" data-county="Grand Bassa"/>
                        {{-- River Cess County (Southeast coast) --}}
                        <path id="county-rivercess" d="M175,335 L235,315 L285,325 L295,365 L245,385 L195,375 Z" class="county-path" data-county="River Cess"/>
                        {{-- Sinoe County (Southeast coast) --}}
                        <path id="county-sinoe" d="M245,385 L295,365 L355,355 L375,395 L335,425 L275,425 L245,405 Z" class="county-path" data-county="Sinoe"/>
                        {{-- Grand Gedeh County (East, borders Ivory Coast) --}}
                        <path id="county-grandgedeh" d="M315,195 L365,185 L415,195 L435,255 L405,305 L345,315 L295,295 L285,245 L275,205 Z" class="county-path" data-county="Grand Gedeh"/>
                        {{-- River Gee County (Southeast) --}}
                        <path id="county-rivergee" d="M295,295 L345,315 L365,355 L355,355 L295,365 L285,325 Z" class="county-path" data-county="River Gee"/>
                        {{-- Grand Kru County (South coast) --}}
                        <path id="county-grandkru" d="M355,355 L365,355 L405,365 L425,405 L395,435 L355,435 L335,425 L375,395 Z" class="county-path" data-county="Grand Kru"/>
                        {{-- Maryland County (Southeast tip) --}}
                        <path id="county-maryland" d="M395,435 L425,405 L465,415 L485,445 L455,465 L415,455 Z" class="county-path" data-county="Maryland"/>
                        {{-- Ocean indication --}}
                        <path d="M0,280 Q50,270 115,285 Q175,335 245,385 Q335,425 455,465 L485,445 L500,450 L500,480 L0,480 Z" fill="#E0F2FE" stroke="none" opacity="0.5"/>
                        {{-- Country label --}}
                        <text x="200" y="170" class="text-[10px] fill-gray-400 font-medium" text-anchor="middle">LIBERIA</text>
                    </svg>
                </div>
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

    {{-- SRGBV Incident Reports Table --}}
    <div class="bg-white border border-gray-200 rounded-lg">
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-sm font-semibold text-gray-900">SRGBV Incident Reports</h3>
                    <span class="text-xs text-gray-500">({{ $totalIncidents }} total)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" placeholder="Search cases..." class="text-xs border border-gray-300 rounded-md pl-8 pr-3 py-1.5 w-40 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-3">
                <select class="text-xs border border-gray-300 rounded-md px-2.5 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option>All Counties</option>
                    @foreach($topCounties as $county => $count)
                    <option value="{{ $county }}">{{ $county }}</option>
                    @endforeach
                </select>
                <select class="text-xs border border-gray-300 rounded-md px-2.5 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option>All Categories</option>
                    @foreach(\App\Models\Incident::CATEGORIES_BY_TYPE[\App\Models\Incident::TYPE_SRGBV] as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select class="text-xs border border-gray-300 rounded-md px-2.5 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option>All Risk Levels</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
                <select class="text-xs border border-gray-300 rounded-md px-2.5 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option>All Statuses</option>
                    @foreach(\App\Models\Incident::STATUSES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button type="button" class="text-xs border border-gray-300 rounded-md px-2.5 py-1.5 bg-white text-gray-600 hover:bg-gray-50 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Date Range
                </button>
                <button type="button" class="text-xs text-blue-600 hover:text-blue-700 font-medium ml-auto">Reset Filters</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Case ID</th>
                        <th class="px-4 py-3 text-left font-medium">Category</th>
                        <th class="px-4 py-3 text-left font-medium">School</th>
                        <th class="px-4 py-3 text-left font-medium">County</th>
                        <th class="px-4 py-3 text-left font-medium">Risk</th>
                        <th class="px-4 py-3 text-left font-medium">Status</th>
                        <th class="px-4 py-3 text-left font-medium">Assigned</th>
                        <th class="px-4 py-3 text-left font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentIncidents as $incident)
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('sir.incidents.show', $incident) }}'">
                        <td class="px-4 py-3 font-medium text-blue-600">{{ $incident->incident_number }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $incident->category_label }}</td>
                        <td class="px-4 py-3 text-gray-700 max-w-[150px] truncate">{{ $incident->school_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $incident->school_county ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $riskColors = [
                                    'low' => 'bg-green-100 text-green-700',
                                    'medium' => 'bg-yellow-100 text-yellow-700',
                                    'high' => 'bg-orange-100 text-orange-700',
                                    'critical' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded {{ $riskColors[$incident->priority] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($incident->priority) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusStyles = [
                                    'reported' => 'bg-blue-100 text-blue-700',
                                    'under_review' => 'bg-amber-100 text-amber-700',
                                    'under_investigation' => 'bg-orange-100 text-orange-700',
                                    'action_taken' => 'bg-purple-100 text-purple-700',
                                    'referred' => 'bg-indigo-100 text-indigo-700',
                                    'resolved' => 'bg-green-100 text-green-700',
                                    'closed' => 'bg-gray-100 text-gray-600',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded {{ $statusStyles[$incident->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $incident->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $incident->assignee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $incident->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm text-gray-500">No SRGBV incidents reported yet.</p>
                                @if($canManage)
                                <a href="{{ route('sir.incidents.create', ['type' => 'srgbv']) }}" class="mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium">Create first report →</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentIncidents->count() > 0)
        <div class="p-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-xs text-gray-500">Showing {{ $recentIncidents->count() }} most recent cases</p>
            <a href="{{ route('sir.incidents.index', ['module' => 'srgbv']) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all SRGBV cases →</a>
        </div>
        @endif
    </div>
</div>

<style>
    .county-path {
        fill: #E0E7FF;
        stroke: #fff;
        stroke-width: 2;
        cursor: pointer;
        transition: fill 0.2s ease;
    }
    .county-path:hover {
        fill: #A5B4FC;
    }
    .county-path.level-1 { fill: #DBEAFE; }
    .county-path.level-2 { fill: #93C5FD; }
    .county-path.level-3 { fill: #3B82F6; }
    .county-path.level-4 { fill: #1D4ED8; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countyData = @json($countyData ?? []);
    const maxCount = Math.max(...Object.values(countyData), 1);
    
    document.querySelectorAll('.county-path').forEach(path => {
        const county = path.dataset.county;
        const count = countyData[county] || 0;
        const intensity = count / maxCount;
        
        if (count === 0) {
            path.classList.add('level-1');
        } else if (intensity < 0.25) {
            path.classList.add('level-1');
        } else if (intensity < 0.5) {
            path.classList.add('level-2');
        } else if (intensity < 0.75) {
            path.classList.add('level-3');
        } else {
            path.classList.add('level-4');
        }
        
        path.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute bg-gray-900 text-white text-xs px-2 py-1 rounded pointer-events-none z-50';
            tooltip.id = 'county-tooltip';
            tooltip.textContent = `${county}: ${count} cases`;
            document.body.appendChild(tooltip);
            tooltip.style.left = (e.pageX + 10) + 'px';
            tooltip.style.top = (e.pageY - 25) + 'px';
        });
        path.addEventListener('mousemove', function(e) {
            const tooltip = document.getElementById('county-tooltip');
            if (tooltip) {
                tooltip.style.left = (e.pageX + 10) + 'px';
                tooltip.style.top = (e.pageY - 25) + 'px';
            }
        });
        path.addEventListener('mouseleave', function() {
            const tooltip = document.getElementById('county-tooltip');
            if (tooltip) tooltip.remove();
        });
    });

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
