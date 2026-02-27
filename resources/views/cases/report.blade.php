@extends('layouts.app')

@section('title', 'Cases Report')
@section('page-title', 'Cases Report')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    {{-- Header --}}
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-800">Cases Report & Management</h2>
        <p class="text-sm text-gray-500 mt-2">Select a case category to view, report, and manage cases</p>
    </div>

    {{-- Case Type Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- SRGBV Cases Card --}}
        @if($canAccessSrgbv)
        <a href="{{ route('srgbv.dashboard') }}" class="group block bg-white rounded-xl border-2 border-red-200 hover:border-red-500 hover:shadow-lg transition-all p-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-red-200 transition">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800 group-hover:text-red-700 transition">SRGBV</h3>
                    <p class="text-sm text-gray-500 mt-1">School-Related Gender-Based Violence case management, reporting, and tracking</p>

                    {{-- Quick Stats --}}
                    <div class="flex items-center gap-4 mt-4">
                        <div class="text-center">
                            <p class="text-xl font-bold text-gray-800">{{ $srgbvTotalCount }}</p>
                            <p class="text-xs text-gray-400">Total</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xl font-bold text-red-600">{{ $srgbvOpenCount }}</p>
                            <p class="text-xs text-gray-400">Open</p>
                        </div>
                        @if($srgbvCriticalCount > 0)
                        <div class="text-center">
                            <p class="text-xl font-bold text-amber-600">{{ $srgbvCriticalCount }}</p>
                            <p class="text-xs text-gray-400">Critical</p>
                        </div>
                        @endif
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500 transition flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
        @else
        <div class="bg-gray-50 rounded-xl border-2 border-gray-200 p-6 opacity-60">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-500">SRGBV</h3>
                    <p class="text-sm text-gray-400 mt-1">School-Related Gender-Based Violence — Access restricted to authorized personnel</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Other Cases Card --}}
        <div class="group block bg-white rounded-xl border-2 border-blue-200 p-6 relative">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800">Other Cases</h3>
                    <p class="text-sm text-gray-500 mt-1">General case tracking, complaints, administrative cases, and other incident reports</p>

                    <div class="mt-4">
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Coming Soon
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
