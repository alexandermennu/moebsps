@extends('layouts.app')
@section('title', 'SIR Dashboard')
@section('page-title', 'SIR (School Incident Reporter)')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">SIR Dashboard</h2>
        <p class="text-sm text-gray-500 mt-1">The School Incident Reporter (SIR) is divided into two modules. Select a module below to access its dashboard.</p>
    </div>

    {{-- Module Plates --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- SRGBV Module --}}
        @if($canAccessSrgbv)
        <a href="{{ route('sir.srgbv.dashboard') }}" class="group block bg-white border-2 border-red-200 hover:border-red-400 rounded-lg p-8 transition-all hover:shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-red-200 transition">
                    <svg class="w-7 h-7 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                </div>
                <svg class="w-5 h-5 text-gray-300 group-hover:text-red-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">SRGBV</h3>
            <p class="text-sm text-gray-500 mb-5">School-Related Gender-Based Violence — Tracks and manages all SRGBV cases including physical, sexual, psychological violence, bullying, harassment, and exploitation.</p>
            @if($srgbvStats)
            <div class="grid grid-cols-3 gap-3 border-t border-gray-100 pt-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total</p>
                    <p class="text-xl font-bold text-gray-800">{{ $srgbvStats['total'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-red-500 uppercase tracking-wide">Open</p>
                    <p class="text-xl font-bold text-red-700">{{ $srgbvStats['open'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-orange-500 uppercase tracking-wide">Critical</p>
                    <p class="text-xl font-bold text-orange-700">{{ $srgbvStats['critical'] }}</p>
                </div>
            </div>
            @endif
        </a>
        @endif

        {{-- Other Incidents Module --}}
        @if($canAccessOther)
        <a href="{{ route('sir.other.dashboard') }}" class="group block bg-white border-2 border-blue-200 hover:border-blue-400 rounded-lg p-8 transition-all hover:shadow-lg">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                    <svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <svg class="w-5 h-5 text-gray-300 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Other Incidents</h3>
            <p class="text-sm text-gray-500 mb-5">All non-SRGBV school incidents — Disciplinary, Safety & Security, Infrastructure, Academic Misconduct, Health & Welfare, and other general incidents.</p>
            @if($otherStats)
            <div class="grid grid-cols-3 gap-3 border-t border-gray-100 pt-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total</p>
                    <p class="text-xl font-bold text-gray-800">{{ $otherStats['total'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-blue-500 uppercase tracking-wide">Open</p>
                    <p class="text-xl font-bold text-blue-700">{{ $otherStats['open'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-orange-500 uppercase tracking-wide">Critical</p>
                    <p class="text-xl font-bold text-orange-700">{{ $otherStats['critical'] }}</p>
                </div>
            </div>
            @endif
        </a>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-700">Quick Actions</h3>
                <p class="text-xs text-gray-400 mt-0.5">Jump to common tasks across both modules.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('sir.incidents.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">View All Incidents</a>
                <a href="{{ route('sir.incidents.create') }}" class="px-4 py-2 bg-red-700 text-white text-sm font-medium hover:bg-red-800 rounded-md">Report Incident</a>
            </div>
        </div>
    </div>
</div>
@endsection
