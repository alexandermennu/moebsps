@extends('layouts.app')

@section('title', 'Weekly Updates')
@section('page-title', 'Weekly Updates')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-gray-300 pb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Weekly Updates</h2>
            <p class="text-sm text-gray-500">Review and manage weekly activity reports</p>
        </div>
        <div class="flex items-center gap-2">
            @if($user->hasFullAccess() || $user->isDirector())
                <a href="{{ route('weekly-updates.consolidated') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Consolidated Reports
                </a>
            @endif
            @if($user->canManageDivision())
                <a href="{{ route('weekly-updates.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Update
                </a>
            @endif
        </div>
    </div>

    {{-- Current Week Section --}}
    <div class="bg-white border-2 border-blue-200 rounded-lg overflow-hidden">
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Current Week</h3>
                            <p class="text-sm text-gray-600">{{ $currentWeekStart->format('F d') }} – {{ $currentWeekEnd->format('d, Y') }} (Working Days)</p>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    @php
                        $submittedCount = $currentWeekStatus->filter(fn($s) => $s->status !== 'not_submitted')->count();
                        $totalCount = $currentWeekStatus->count();
                    @endphp
                    <p class="text-2xl font-bold text-blue-700">{{ $submittedCount }}/{{ $totalCount }}</p>
                    <p class="text-xs text-gray-500">divisions submitted</p>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($currentWeekStatus as $divStatus)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center gap-4">
                        {{-- Status Icon --}}
                        @if($divStatus->status === 'approved')
                            <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        @elseif($divStatus->status === 'submitted')
                            <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        @elseif($divStatus->status === 'draft')
                            <span class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </span>
                        @elseif($divStatus->status === 'rejected')
                            <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </span>
                        @else
                            <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        @endif

                        <div>
                            <p class="font-medium text-gray-900">{{ $divStatus->division->name }}</p>
                            <p class="text-xs text-gray-500">{{ $divStatus->division->code }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Status Badge --}}
                        <span class="text-xs px-2 py-1 font-medium rounded
                            {{ $divStatus->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $divStatus->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $divStatus->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $divStatus->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $divStatus->status === 'not_submitted' ? 'bg-gray-100 text-gray-500' : '' }}">
                            {{ $divStatus->status === 'not_submitted' ? 'Not Submitted' : ucfirst($divStatus->status) }}
                        </span>

                        {{-- Action --}}
                        @if($divStatus->update)
                            <a href="{{ route('weekly-updates.show', $divStatus->update) }}" 
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">View</a>
                        @elseif($user->canManageDivision() && $divStatus->division->id === $user->division_id)
                            <a href="{{ route('weekly-updates.create') }}" 
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">Submit Now</a>
                        @else
                            <span class="text-sm text-gray-400">—</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <p>No divisions found.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Previous Weeks Section --}}
    @if($previousWeeksGrouped->count() > 0)
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Previous Weeks</h3>
            <form method="GET" class="flex items-center gap-2">
                <select name="status" class="px-3 py-1.5 border border-gray-300 rounded text-sm">
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Pending Review</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-gray-100 border border-gray-300 text-sm hover:bg-gray-200 rounded">Filter</button>
            </form>
        </div>

        <div class="space-y-3">
            @foreach($previousWeeksGrouped as $weekData)
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden" x-data="{ open: false }">
                    {{-- Week Header (Collapsible) --}}
                    <button @click="open = !open" type="button" class="w-full px-5 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-4">
                            <span class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-sm">
                                {{ $weekData->week_start->format('d') }}
                            </span>
                            <div class="text-left">
                                <p class="font-semibold text-gray-900">{{ $weekData->week_label }}</p>
                                <p class="text-xs text-gray-500">{{ $weekData->week_start->format('M d') }} – {{ $weekData->week_end->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            {{-- Progress indicator --}}
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    @php $pct = $weekData->total_divisions > 0 ? ($weekData->approved_count / $weekData->total_divisions) * 100 : 0; @endphp
                                    <div class="h-full bg-green-500 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600 font-medium">{{ $weekData->submitted_count }}/{{ $weekData->total_divisions }}</span>
                            </div>

                            {{-- Expand Icon --}}
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>

                    {{-- Week Details (Expandable) --}}
                    <div x-show="open" x-cloak class="border-t border-gray-200">
                        <div class="divide-y divide-gray-100">
                            @foreach($weekData->updates as $update)
                                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                                    <div class="flex items-center gap-3">
                                        @if($update->status === 'approved')
                                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        @elseif($update->status === 'submitted')
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        @elseif($update->status === 'rejected')
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $update->division->name }}</p>
                                            <p class="text-xs text-gray-500">by {{ $update->submitter->name }} · {{ $update->created_at->format('M d') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] px-1.5 py-0.5 font-medium rounded
                                            {{ $update->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $update->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $update->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $update->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}">
                                            {{ ucfirst($update->status) }}
                                        </span>
                                        <a href="{{ route('weekly-updates.show', $update) }}" class="text-xs text-blue-600 hover:underline">View</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white border border-gray-200 rounded-lg px-6 py-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="mt-2 text-sm text-gray-500">No previous weekly updates found.</p>
    </div>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
