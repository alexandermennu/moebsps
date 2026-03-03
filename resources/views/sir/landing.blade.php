@extends('layouts.app')
@section('title', 'SIR Dashboard')
@section('page-title', 'School Incident Reporter')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">School Incident Reporter</h2>
            <p class="text-sm text-gray-500">Track, manage, and resolve school-related incidents across Liberia.</p>
        </div>
        <a href="{{ route('sir.incidents.create') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Report Incident
        </a>
    </div>

    {{-- Urgent Alert Banner --}}
    @if($urgentCount > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-red-800">{{ $urgentCount }} {{ Str::plural('Case', $urgentCount) }} Require Immediate Attention</p>
            <p class="text-xs text-red-600">Critical incidents flagged for urgent review and action.</p>
        </div>
        <a href="{{ route('sir.incidents.index', ['priority' => 'critical']) }}" class="shrink-0 text-sm font-medium text-red-700 hover:text-red-800 flex items-center gap-1">
            View All
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    @endif

    {{-- Combined Quick Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $combinedStats['total'] }}</p>
            <p class="text-xs text-gray-500">Total Incidents</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $combinedStats['open'] }}</p>
            <p class="text-xs text-gray-500">Open Cases</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $combinedStats['critical'] }}</p>
            <p class="text-xs text-gray-500">Critical Cases</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <p class="text-2xl font-bold text-green-600">{{ $combinedStats['newToday'] }}</p>
            <p class="text-xs text-gray-500">New Today</p>
        </div>
    </div>

    {{-- Module Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- SRGBV Module Card --}}
        @if($canAccessSrgbv)
        <a href="{{ route('sir.srgbv.dashboard') }}" class="group bg-white border border-gray-200 hover:border-red-300 rounded-lg overflow-hidden transition hover:shadow-md">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">SRGBV</h3>
                        <p class="text-sm text-red-100">School-Related Gender-Based Violence</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-white/70 group-hover:text-white group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <div class="p-5">
                <p class="text-sm text-gray-600 mb-4">Track and manage reports of physical violence, sexual violence, emotional abuse, and bullying in schools.</p>
                @if($srgbvStats)
                <div class="grid grid-cols-4 gap-3 text-center">
                    <div class="bg-gray-50 rounded-lg py-2">
                        <p class="text-lg font-bold text-gray-800">{{ $srgbvStats['total'] }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div class="bg-red-50 rounded-lg py-2">
                        <p class="text-lg font-bold text-red-600">{{ $srgbvStats['open'] }}</p>
                        <p class="text-xs text-gray-500">Open</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg py-2">
                        <p class="text-lg font-bold text-orange-600">{{ $srgbvStats['critical'] }}</p>
                        <p class="text-xs text-gray-500">Critical</p>
                    </div>
                    <div class="bg-green-50 rounded-lg py-2">
                        <p class="text-lg font-bold text-green-600">{{ $srgbvStats['newToday'] }}</p>
                        <p class="text-xs text-gray-500">Today</p>
                    </div>
                </div>
                @endif
            </div>
        </a>
        @endif

        {{-- Other Incidents Module Card --}}
        @if($canAccessOther)
        <a href="{{ route('sir.other.dashboard') }}" class="group bg-white border border-gray-200 hover:border-blue-300 rounded-lg overflow-hidden transition hover:shadow-md">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Other Incidents</h3>
                        <p class="text-sm text-blue-100">General School Incident Reports</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-white/70 group-hover:text-white group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <div class="p-5">
                <p class="text-sm text-gray-600 mb-4">Manage disciplinary issues, safety concerns, infrastructure problems, academic incidents, and health emergencies.</p>
                @if($otherStats)
                <div class="grid grid-cols-4 gap-3 text-center">
                    <div class="bg-gray-50 rounded-lg py-2">
                        <p class="text-lg font-bold text-gray-800">{{ $otherStats['total'] }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg py-2">
                        <p class="text-lg font-bold text-blue-600">{{ $otherStats['open'] }}</p>
                        <p class="text-xs text-gray-500">Open</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg py-2">
                        <p class="text-lg font-bold text-orange-600">{{ $otherStats['critical'] }}</p>
                        <p class="text-xs text-gray-500">Critical</p>
                    </div>
                    <div class="bg-green-50 rounded-lg py-2">
                        <p class="text-lg font-bold text-green-600">{{ $otherStats['newToday'] }}</p>
                        <p class="text-xs text-gray-500">Today</p>
                    </div>
                </div>
                @endif
            </div>
        </a>
        @endif
    </div>

    {{-- Recent Activity --}}
    @if($recentIncidents->count() > 0)
    <div class="bg-white border border-gray-200 rounded-lg">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Recent Activity</h3>
                <p class="text-xs text-gray-500">Latest incidents across all modules</p>
            </div>
            <a href="{{ route('sir.incidents.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View All →</a>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentIncidents as $incident)
            <a href="{{ route('sir.incidents.show', $incident) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 {{ $incident->type === 'srgbv' ? 'bg-red-100' : 'bg-blue-100' }}">
                    @if($incident->type === 'srgbv')
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                    @else
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-500">{{ $incident->incident_number }}</span>
                        @php
                            $priorityColors = [
                                'low' => 'bg-green-100 text-green-700',
                                'medium' => 'bg-yellow-100 text-yellow-700',
                                'high' => 'bg-orange-100 text-orange-700',
                                'critical' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded {{ $priorityColors[$incident->priority] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($incident->priority) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-800 truncate">{{ $incident->title }}</p>
                </div>
                <div class="text-right shrink-0">
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
                    <p class="text-xs font-medium {{ $statusColors[$incident->status] ?? 'text-gray-600' }}">{{ $incident->status_label }}</p>
                    <p class="text-xs text-gray-400">{{ $incident->created_at->diffForHumans() }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick Links Footer --}}
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-700">Quick Actions</h3>
                <p class="text-xs text-gray-500">Common tasks and reports</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('sir.incidents.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50 rounded-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    All Incidents
                </a>
                <a href="{{ route('sir.incidents.index', ['status' => 'reported']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50 rounded-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pending Review
                </a>
                <a href="{{ route('sir.incidents.index', ['priority' => 'critical']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50 rounded-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Critical Cases
                </a>
                <a href="{{ route('sir.incidents.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white text-xs font-medium hover:bg-red-700 rounded-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Report Incident
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
