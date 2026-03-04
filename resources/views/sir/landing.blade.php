@extends('layouts.app')
@section('title', 'School Incident Reporter')
@section('page-title', 'School Incident Reporter')
@section('content')
<div class="min-h-[calc(100vh-200px)] flex flex-col">
    {{-- Welcome Banner --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 relative overflow-hidden">
        {{-- Liberia Map Silhouette Background --}}
        <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-10 pointer-events-none hidden sm:block">
            <svg width="180" height="160" viewBox="0 0 100 90" fill="currentColor" class="text-blue-900">
                <path d="M15,10 Q20,5 35,8 Q50,10 60,5 Q70,3 80,10 Q85,15 88,25 Q90,35 85,45 Q80,55 75,60 Q70,65 60,70 Q50,75 40,78 Q30,80 20,75 Q15,70 10,60 Q5,50 8,40 Q10,30 12,20 Q13,15 15,10 Z"/>
            </svg>
        </div>
        <div class="absolute right-20 top-4 hidden sm:block">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>
        <div class="relative">
            <div class="flex items-start gap-3">
                <div class="w-1 h-16 bg-red-600 rounded-full"></div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Welcome to SIR.</h1>
                    <p class="text-gray-600 font-medium">Strengthening accountability in our schools.</p>
                    <p class="text-gray-500 text-sm mt-1">Choose a module below to review and manage cases efficiently.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Module Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl">
        {{-- SRGBV Cases Card --}}
        @if($canAccessSrgbv)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">SRGBV Cases</h2>
                        <p class="text-gray-500 text-xs">Sexual and Gender-Based Violence Reports</p>
                    </div>
                </div>
                
                {{-- Stats --}}
                <div class="grid grid-cols-3 divide-x divide-gray-200 mb-4">
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-gray-900">{{ $srgbvStats['total'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-blue-600">{{ $srgbvStats['open'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Open</p>
                    </div>
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-red-600">{{ $srgbvStats['critical'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Critical</p>
                    </div>
                </div>
                
                {{-- Action Button --}}
                <a href="{{ route('sir.srgbv.cases.index') }}" class="flex items-center justify-center gap-2 w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition">
                    View Cases
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
        @else
        {{-- SRGBV Restricted Card --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl overflow-hidden opacity-60">
            <div class="p-5">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 bg-gray-200 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-500">SRGBV Cases</h2>
                        <p class="text-gray-400 text-xs">Sexual and Gender-Based Violence Reports</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 divide-x divide-gray-200 mb-4">
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-gray-400">—</p>
                        <p class="text-xs text-gray-400">Total</p>
                    </div>
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-gray-400">—</p>
                        <p class="text-xs text-gray-400">Open</p>
                    </div>
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-gray-400">—</p>
                        <p class="text-xs text-gray-400">Critical</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-center gap-2 w-full bg-gray-300 text-gray-500 font-medium py-2.5 px-4 rounded-lg text-sm cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Access Restricted
                </div>
            </div>
        </div>
        @endif

        {{-- Other Incidents Card --}}
        @if($canAccessOther)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Other Incidents</h2>
                        <p class="text-gray-500 text-xs">Disciplinary, Safety, Infrastructure & General Reports</p>
                    </div>
                </div>
                
                {{-- Stats --}}
                <div class="grid grid-cols-3 divide-x divide-gray-200 mb-4">
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-gray-900">{{ $otherStats['total'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-blue-600">{{ $otherStats['open'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Open</p>
                    </div>
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-red-600">{{ $otherStats['critical'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Critical</p>
                    </div>
                </div>
                
                {{-- Action Button --}}
                <a href="{{ route('sir.other.incidents.index') }}" class="flex items-center justify-center gap-2 w-full bg-blue-800 hover:bg-blue-900 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition">
                    View Reports
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
        @else
        {{-- Other Incidents Restricted Card --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl overflow-hidden opacity-60">
            <div class="p-5">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-11 h-11 bg-gray-200 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-500">Other Incidents</h2>
                        <p class="text-gray-400 text-xs">Disciplinary, Safety, Infrastructure & General Reports</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 divide-x divide-gray-200 mb-4">
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-gray-400">—</p>
                        <p class="text-xs text-gray-400">Total</p>
                    </div>
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-gray-400">—</p>
                        <p class="text-xs text-gray-400">Open</p>
                    </div>
                    <div class="text-center px-2">
                        <p class="text-xl font-bold text-gray-400">—</p>
                        <p class="text-xs text-gray-400">Critical</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-center gap-2 w-full bg-gray-300 text-gray-500 font-medium py-2.5 px-4 rounded-lg text-sm cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Access Restricted
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-gray-500 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Data is secure and confidential</span>
        </div>
        <div class="flex items-center gap-2 text-gray-500 text-sm">
            <img src="{{ asset('images/liberia-seal.png') }}" alt="Liberia Seal" class="w-5 h-5" onerror="this.style.display='none'">
            <span>Powered by Ministry of Education, Liberia</span>
        </div>
    </div>
</div>
@endsection