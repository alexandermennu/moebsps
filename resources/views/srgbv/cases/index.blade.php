@extends('layouts.app')

@section('title', 'SRGBV Cases')
@section('page-title', 'SRGBV Case Management')

@section('content')
<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">SRGBV Cases</h2>
            <p class="text-sm text-gray-500">Track and manage school-related gender-based violence cases</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('srgbv.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">
                Dashboard
            </a>
            <a href="{{ route('srgbv.cases.create') }}" class="inline-flex items-center px-4 py-2 bg-red-700 text-white text-sm font-medium hover:bg-red-800">
                + Report New Case
            </a>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $cases->total() }}</p>
            <p class="text-xs text-gray-500">Total Cases</p>
        </div>
        @php
            $openCount = \App\Models\SrgbvCase::open()->count();
            $criticalCount = \App\Models\SrgbvCase::critical()->open()->count();
            $reportedCount = \App\Models\SrgbvCase::where('status', 'reported')->count();
            $resolvedCount = \App\Models\SrgbvCase::closed()->count();
        @endphp
        <div class="bg-red-50 border border-red-200 p-4 text-center">
            <p class="text-2xl font-bold text-red-700">{{ $openCount }}</p>
            <p class="text-xs text-red-600">Open</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 p-4 text-center">
            <p class="text-2xl font-bold text-amber-700">{{ $criticalCount }}</p>
            <p class="text-xs text-amber-600">Critical</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 p-4 text-center">
            <p class="text-2xl font-bold text-blue-700">{{ $reportedCount }}</p>
            <p class="text-xs text-blue-600">Awaiting Action</p>
        </div>
        <div class="bg-green-50 border border-green-200 p-4 text-center">
            <p class="text-2xl font-bold text-green-700">{{ $resolvedCount }}</p>
            <p class="text-xs text-green-600">Resolved</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white border border-gray-200 p-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Case #, title, victim..."
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm w-52">
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\SrgbvCase::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Priority</label>
                <select name="priority" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Priorities</option>
                    @foreach(\App\Models\SrgbvCase::PRIORITIES as $key => $label)
                        <option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Category</label>
                <select name="category" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Categories</option>
                    @foreach(\App\Models\SrgbvCase::CATEGORIES as $key => $label)
                        <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm hover:bg-gray-200">Filter</button>
            @if(request()->hasAny(['search', 'status', 'priority', 'category', 'date_from', 'date_to']))
                <a href="{{ route('srgbv.cases.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
            @endif
        </div>
    </form>

    {{-- Cases List --}}
    @if($cases->isEmpty())
        <div class="bg-white border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <p class="text-gray-500 mb-2">No cases found.</p>
            <a href="{{ route('srgbv.cases.create') }}" class="text-sm text-red-600 hover:text-red-800">+ Report a new case</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($cases as $case)
                <a href="{{ route('srgbv.cases.show', $case) }}" class="block bg-white border border-gray-200 hover:border-gray-300 hover:shadow-sm transition-all p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-xs font-mono text-gray-400">{{ $case->case_number }}</span>
                                {{-- Priority badge --}}
                                <span class="text-[10px] px-1.5 py-0.5
                                    @switch($case->priority)
                                        @case('critical') bg-red-100 text-red-700 @break
                                        @case('high') bg-amber-100 text-amber-700 @break
                                        @case('medium') bg-blue-100 text-blue-700 @break
                                        @case('low') bg-gray-100 text-gray-600 @break
                                    @endswitch
                                ">{{ $case->priority_label }}</span>
                                {{-- Status badge --}}
                                <span class="text-[10px] px-1.5 py-0.5
                                    @switch($case->status)
                                        @case('reported') bg-red-100 text-red-700 @break
                                        @case('under_investigation') bg-amber-100 text-amber-700 @break
                                        @case('action_taken') bg-blue-100 text-blue-700 @break
                                        @case('referred') bg-purple-100 text-purple-700 @break
                                        @case('resolved') bg-green-100 text-green-700 @break
                                        @case('closed') bg-gray-100 text-gray-600 @break
                                    @endswitch
                                ">{{ $case->status_label }}</span>
                                @if($case->immediate_action_required)
                                    <span class="text-[10px] px-1.5 py-0.5 bg-red-600 text-white animate-pulse">URGENT</span>
                                @endif
                            </div>

                            <h3 class="text-sm font-semibold text-gray-800">{{ $case->title }}</h3>

                            <div class="flex flex-wrap gap-x-5 gap-y-1 mt-2 text-xs text-gray-500">
                                <span>{{ $case->category_label }}</span>
                                <span>{{ $case->incident_date->format('M d, Y') }}</span>
                                <span>Reported by {{ $case->reporter?->name ?? '—' }}</span>
                                @if($case->assignee)
                                    <span>Assigned: {{ $case->assignee->name }}</span>
                                @endif
                                <span>{{ $case->days_since_reported }}d ago</span>
                            </div>
                        </div>

                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>

        {{ $cases->links() }}
    @endif
</div>
@endsection
