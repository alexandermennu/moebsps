@extends('layouts.app')
@section('title', 'SRGBV Dashboard')
@section('page-title', 'SRGBV Dashboard')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('sir.dashboard') }}" class="hover:text-gray-600">SIR</a>
            <span>›</span>
            <span class="text-gray-600">SRGBV</span>
        </div>
        <h2 class="text-xl font-bold text-gray-900">SRGBV Dashboard</h2>
        <p class="text-sm text-gray-500">Managing and addressing reports of School-Related Gender-Based Violence and Bullying.</p>
    </div>

    {{-- Stat Cards (4 cards matching the design) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Reports --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Total Reports</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalIncidents }}</p>
            </div>
            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>

        {{-- Open Cases --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Open Cases</p>
                <p class="text-2xl font-bold text-blue-600">{{ $openIncidents }}</p>
            </div>
            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>

        {{-- Critical Cases --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-400 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Critical Cases</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $criticalIncidents }}</p>
            </div>
            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>

        {{-- Under Investigation --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Under Investigation</p>
                <p class="text-2xl font-bold text-gray-700">{{ $byStatus['under_investigation'] ?? 0 }}</p>
            </div>
            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- SRGBV Trends Chart --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">SRGBV Trends</h3>
                <div class="flex items-center gap-1 text-xs">
                    <button type="button" class="px-3 py-1 rounded bg-blue-100 text-blue-700 font-medium">7 Days</button>
                    <button type="button" class="px-3 py-1 rounded text-gray-500 hover:bg-gray-100">30 Days</button>
                    <button type="button" class="px-3 py-1 rounded text-gray-500 hover:bg-gray-100">Quarter</button>
                    <button type="button" class="px-3 py-1 rounded text-gray-500 hover:bg-gray-100">Year</button>
                </div>
            </div>
            <p class="text-xs text-gray-400 mb-2">Reports Over Time</p>
            <div class="h-48">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>

        {{-- Victim Category Donut Chart --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Victim Category</h3>
            <div class="flex items-center gap-8">
                <div class="relative w-40 h-40 shrink-0">
                    <canvas id="genderChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        @php $totalGender = array_sum($byGender) ?: 1; $femalePercent = isset($byGender['female']) ? round(($byGender['female'] / $totalGender) * 100, 1) : 0; @endphp
                        <span class="text-lg font-bold text-gray-700">{{ $femalePercent }}%</span>
                    </div>
                </div>
                <div class="space-y-3 flex-1">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-sm text-gray-700">Female</span>
                        <span class="text-sm font-semibold text-gray-900 ml-auto">{{ isset($byGender['female']) ? round(($byGender['female'] / $totalGender) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                        <span class="text-sm text-gray-700">Male</span>
                        <span class="text-sm font-semibold text-gray-900 ml-auto">{{ isset($byGender['male']) ? round(($byGender['male'] / $totalGender) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SRGBV Incident Reports Table --}}
    <div class="bg-white border border-gray-200 rounded-lg">
        {{-- Table Header with Filters --}}
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h3 class="text-base font-semibold text-gray-900">SRGBV Incident Reports</h3>
                <div class="flex flex-wrap items-center gap-2">
                    <select class="text-xs border border-gray-300 rounded-md px-3 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option>School: All</option>
                    </select>
                    <select class="text-xs border border-gray-300 rounded-md px-3 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option>County: All</option>
                        @foreach($topCounties as $county => $count)
                        <option value="{{ $county }}">{{ $county }}</option>
                        @endforeach
                    </select>
                    <select class="text-xs border border-gray-300 rounded-md px-3 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option>Risk: All</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                    <select class="text-xs border border-gray-300 rounded-md px-3 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option>Status: All</option>
                        @foreach(\App\Models\Incident::STATUSES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="text-xs border border-gray-300 rounded-md px-3 py-1.5 bg-white text-gray-700 hover:bg-gray-50 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Date Range
                    </button>
                    @if($canManage)
                    <a href="{{ route('sir.incidents.create', ['type' => 'srgbv']) }}" class="text-xs bg-green-500 hover:bg-green-600 text-white font-medium px-4 py-1.5 rounded-md">New Report</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Table --}}
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
                        <th class="px-4 py-3 text-left font-medium">Assigned To</th>
                        <th class="px-4 py-3 text-left font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentIncidents as $incident)
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('sir.incidents.show', $incident) }}'">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $incident->incident_number }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $incident->category_label }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $incident->school_name ?? '—' }}</td>
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
                                    'reported' => 'text-blue-600',
                                    'under_review' => 'text-amber-600',
                                    'under_investigation' => 'text-orange-600',
                                    'action_taken' => 'text-purple-600',
                                    'referred' => 'text-indigo-600',
                                    'resolved' => 'text-green-600',
                                    'closed' => 'text-gray-500',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1 text-xs font-medium {{ $statusStyles[$incident->status] ?? 'text-gray-600' }}">
                                @if(in_array($incident->status, ['reported', 'under_review']))
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                @endif
                                {{ $incident->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $incident->assignee?->name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $incident->created_at->format('m/d/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">No SRGBV incidents reported yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer --}}
        @if($recentIncidents->count() > 0)
        <div class="p-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-xs text-gray-500">Showing {{ $recentIncidents->count() }} most recent cases</p>
            <a href="{{ route('sir.incidents.index', ['module' => 'srgbv']) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all SRGBV cases →</a>
        </div>
        @endif
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trends Line Chart
    const trendsCtx = document.getElementById('trendsChart');
    if (trendsCtx) {
        const trendsData = @json($monthlyTrend);
        const labels = Object.keys(trendsData).map(m => {
            const [year, month] = m.split('-');
            return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
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
                    tension: 0.3,
                    pointBackgroundColor: '#3B82F6',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Gender Donut Chart
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
