@extends('layouts.app')

@section('title', 'Tracked Activities')
@section('page-title', 'Submission Activity Tracker')

@section('content')
<div class="max-w-7xl space-y-6">

    {{-- Header --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Submission Activity Tracker</h2>
        <p class="text-sm text-gray-500">Activities automatically tracked from approved weekly updates. Stale ({{ $settings['stale_weeks'] }}+ weeks unchanged) and repeated ({{ $settings['repeat_threshold'] }}+ submissions) activities are highlighted.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="{{ route('tracked-activities.index') }}" class="bg-white border border-gray-200 p-4 hover:border-blue-400 transition {{ !request('flag') && !request('status') ? 'ring-2 ring-slate-300' : '' }}">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Tracked</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
        </a>
        <a href="{{ route('tracked-activities.index', array_merge(request()->except('flag', 'status'), ['status' => ''])) }}" class="bg-blue-50 border border-blue-200 p-4 hover:border-blue-400 transition">
            <p class="text-xs text-blue-600 uppercase tracking-wide font-semibold">Active</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['active'] }}</p>
        </a>
        <a href="{{ route('tracked-activities.index', array_merge(request()->except('flag', 'status'), ['flag' => 'stale'])) }}" class="bg-amber-50 border border-amber-200 p-4 hover:border-amber-400 transition {{ request('flag') === 'stale' ? 'ring-2 ring-amber-300' : '' }}">
            <p class="text-xs text-amber-600 uppercase tracking-wide font-semibold">Stale</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $stats['stale'] }}</p>
        </a>
        <a href="{{ route('tracked-activities.index', array_merge(request()->except('flag', 'status'), ['flag' => 'repeated'])) }}" class="bg-purple-50 border border-purple-200 p-4 hover:border-purple-400 transition {{ request('flag') === 'repeated' ? 'ring-2 ring-purple-300' : '' }}">
            <p class="text-xs text-purple-600 uppercase tracking-wide font-semibold">Repeated</p>
            <p class="text-2xl font-bold text-purple-700 mt-1">{{ $stats['repeated'] }}</p>
        </a>
        <a href="{{ route('tracked-activities.index', array_merge(request()->except('flag', 'status'), ['status' => 'completed'])) }}" class="bg-green-50 border border-green-200 p-4 hover:border-green-400 transition {{ request('status') === 'completed' ? 'ring-2 ring-green-300' : '' }}">
            <p class="text-xs text-green-600 uppercase tracking-wide font-semibold">Completed</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $stats['completed'] }}</p>
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 p-4">
        <form method="GET" action="{{ route('tracked-activities.index') }}" class="flex flex-wrap items-end gap-3">
            @if(!$user->isDivisionScoped())
            <div>
                <label class="text-xs font-medium text-gray-600 mb-1 block">Division</label>
                <select name="division_id" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Divisions</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="text-xs font-medium text-gray-600 mb-1 block">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All Statuses</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Not Started</option>
                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="na" {{ request('status') === 'na' ? 'selected' : '' }}>N/A</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600 mb-1 block">Flag</label>
                <select name="flag" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All</option>
                    <option value="stale" {{ request('flag') === 'stale' ? 'selected' : '' }}>Stale</option>
                    <option value="repeated" {{ request('flag') === 'repeated' ? 'selected' : '' }}>Repeated</option>
                    <option value="flagged" {{ request('flag') === 'flagged' ? 'selected' : '' }}>Any Flag</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600 mb-1 block">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search activities..."
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm w-48">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">Filter</button>
            <a href="{{ route('tracked-activities.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Clear</a>
        </form>
    </div>

    {{-- Activities Table --}}
    <div class="bg-white border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 280px;">Activity</th>
                        @if(!$user->isDivisionScoped())
                        <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-36">Division</th>
                        @endif
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-28">Status</th>
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-20">Times</th>
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-28">Weeks Same</th>
                        <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-28">First Seen</th>
                        <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-28">Last Seen</th>
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-24">Flags</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activities as $tracked)
                        @php
                            $rowBg = '';
                            if ($tracked->is_stale && $tracked->is_repeated) $rowBg = 'bg-red-50';
                            elseif ($tracked->is_stale) $rowBg = 'bg-amber-50';
                            elseif ($tracked->is_repeated) $rowBg = 'bg-purple-50';
                        @endphp
                        <tr class="{{ $rowBg }} hover:bg-gray-50">
                            <td class="px-4 py-3 align-top">
                                @if($tracked->latest_weekly_update_id)
                                    <a href="{{ route('weekly-updates.show', ['weeklyUpdate' => $tracked->latest_weekly_update_id, 'from' => 'tracker']) }}" class="text-gray-800 font-medium hover:text-blue-700 hover:underline">{{ Str::limit($tracked->activity_text, 100) }}</a>
                                @else
                                    <p class="text-gray-800 font-medium">{{ Str::limit($tracked->activity_text, 100) }}</p>
                                @endif
                                @if($tracked->responsible_persons)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $tracked->responsible_persons }}</p>
                                @endif
                            </td>
                            @if(!$user->isDivisionScoped())
                            <td class="px-4 py-3 align-top">
                                <span class="text-xs text-gray-600">{{ $tracked->division?->name }}</span>
                            </td>
                            @endif
                            <td class="px-4 py-3 text-center align-top">
                                @php
                                    $statusColors = [
                                        'not_started' => 'bg-red-100 text-red-700 border-red-200',
                                        'ongoing' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'completed' => 'bg-green-100 text-green-700 border-green-200',
                                        'na' => 'bg-gray-100 text-gray-600 border-gray-200',
                                    ];
                                    $statusDots = [
                                        'not_started' => 'bg-red-500',
                                        'ongoing' => 'bg-yellow-400',
                                        'completed' => 'bg-green-500',
                                        'na' => 'bg-gray-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-[10px] px-1.5 py-0.5 font-medium border {{ $statusColors[$tracked->current_status] ?? $statusColors['na'] }}">
                                    <span class="w-2 h-2 rounded-full {{ $statusDots[$tracked->current_status] ?? $statusDots['na'] }}"></span>
                                    {{ $tracked->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center align-top">
                                <span class="text-sm font-bold {{ $tracked->times_reported >= ($settings['repeat_threshold'] ?? 2) ? 'text-purple-600' : 'text-gray-600' }}">
                                    {{ $tracked->times_reported }}×
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center align-top">
                                <span class="text-sm font-bold {{ $tracked->weeks_unchanged >= ($settings['stale_weeks'] ?? 3) ? 'text-amber-600' : 'text-gray-600' }}">
                                    {{ $tracked->weeks_unchanged }}w
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 align-top">{{ $tracked->first_reported_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500 align-top">{{ $tracked->last_reported_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-center align-top">
                                <div class="flex items-center justify-center gap-1">
                                    @if($tracked->is_stale)
                                        <span class="inline-block px-1.5 py-0.5 text-[10px] font-medium bg-amber-200 text-amber-800" title="Status unchanged for {{ $tracked->weeks_unchanged }} weeks">Stale</span>
                                    @endif
                                    @if($tracked->is_repeated)
                                        <span class="inline-block px-1.5 py-0.5 text-[10px] font-medium bg-purple-200 text-purple-800" title="Reported {{ $tracked->times_reported }} times">Repeated</span>
                                    @endif
                                    @if(!$tracked->is_stale && !$tracked->is_repeated)
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $user->isDivisionScoped() ? 7 : 8 }}" class="px-4 py-12 text-center text-sm text-gray-500">
                                No tracked activities found. Activities are tracked automatically when weekly updates are approved.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

    {{-- Legend --}}
    <div class="bg-white border border-gray-200 p-4">
        <p class="text-xs font-semibold text-gray-600 mb-2">How it works</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-gray-500">
            <div>Activities are automatically extracted from <strong>approved weekly updates</strong> and matched by text across submissions.</div>
            <div><strong>Stale</strong> — Activity has been in the same non-completed status for <strong>{{ $settings['stale_weeks'] }}+ weeks</strong>.</div>
            <div><strong>Repeated</strong> — Activity has appeared in <strong>{{ $settings['repeat_threshold'] }}+ weekly submissions</strong> without being completed.</div>
            <div>Thresholds can be configured in <a href="{{ route('admin.settings.index') }}" class="text-xs text-blue-700 hover:underline">System Settings</a>.</div>
        </div>
    </div>
</div>
@endsection
