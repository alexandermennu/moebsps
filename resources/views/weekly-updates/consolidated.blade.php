@extends('layouts.app')

@section('title', 'Consolidated Weekly Updates')
@section('page-title', 'Consolidated Reports')

@section('content')
<div class="max-w-7xl">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-xs text-blue-700 hover:underline">Back to Dashboard</a>
    </div>

    {{-- Header --}}
    <div class="bg-white border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Consolidated Weekly Updates</h2>
                <p class="text-sm text-gray-500">All division reports in one view</p>
            </div>

            {{-- Download Buttons --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('weekly-updates.download-consolidated', ['week_start' => $weekStart, 'week_end' => $weekEnd, 'format' => 'pdf']) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 text-white text-xs font-medium hover:bg-red-700">
                    Download PDF
                </a>
                <a href="{{ route('weekly-updates.download-consolidated', ['week_start' => $weekStart, 'week_end' => $weekEnd, 'format' => 'word']) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-xs font-medium hover:bg-blue-700">
                    Download Word
                </a>
            </div>
        </div>

        {{-- Week Filter --}}
        <form method="GET" action="{{ route('weekly-updates.consolidated') }}" class="flex items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Week Start</label>
                <input type="date" name="week_start" value="{{ $weekStart }}"
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Week End</label>
                <input type="date" name="week_end" value="{{ $weekEnd }}"
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <option value="" {{ $statusFilter === '' ? 'selected' : '' }}>Submitted & Approved</option>
                    <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Approved Only</option>
                    <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Submitted Only</option>
                    <option value="draft" {{ $statusFilter === 'draft' ? 'selected' : '' }}>Drafts</option>
                    <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">
                Filter
            </button>
        </form>
    </div>

    {{-- Division Analytics Overview --}}
    @if(count($divisionAnalytics) > 0)
    <div class="bg-white border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Division Summary</h3>
            <p class="text-xs text-gray-500 mt-0.5">Quick analytics across all divisions for the selected period</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Division</th>
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Updates</th>
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Activities</th>
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">
                            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span>Done</span>
                        </th>
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">
                            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-400"></span>Ongoing</span>
                        </th>
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">
                            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span>Not Started</span>
                        </th>
                        <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($divisionAnalytics as $divName => $analytics)
                        @if($analytics['total_updates'] > 0)
                        @php $rate = $analytics['total_activities'] > 0 ? round(($analytics['completed'] / $analytics['total_activities']) * 100) : 0; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $divName }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $analytics['total_updates'] }}</td>
                            <td class="px-4 py-3 text-center font-medium text-gray-800">{{ $analytics['total_activities'] }}</td>
                            <td class="px-4 py-3 text-center text-green-600 font-medium">{{ $analytics['completed'] }}</td>
                            <td class="px-4 py-3 text-center text-yellow-600 font-medium">{{ $analytics['ongoing'] }}</td>
                            <td class="px-4 py-3 text-center text-red-600 font-medium">{{ $analytics['not_started'] }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-full bg-gray-100 h-2 min-w-[60px]">
                                        <div class="h-2 {{ $rate >= 70 ? 'bg-green-500' : ($rate >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 w-8">{{ $rate }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Full Reports by Division --}}
    @forelse($groupedUpdates as $divisionName => $updates)
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <h3 class="text-md font-bold text-gray-800">{{ $divisionName }}</h3>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5">{{ $updates->count() }} {{ Str::plural('report', $updates->count()) }}</span>
            </div>

            @foreach($updates as $update)
                <div class="bg-white border border-gray-200 mb-4">
                    {{-- Update Header --}}
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h4 class="text-sm font-semibold text-gray-800">
                                    Week of {{ $update->week_start->format('M d') }} – {{ $update->week_end->format('M d, Y') }}
                                </h4>
                                <span class="text-[10px] px-1.5 py-0.5 font-medium
                                    {{ $update->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $update->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $update->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $update->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst($update->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">Submitted by {{ $update->submitter->name }}
                                @if($update->reviewer) · Reviewed by {{ $update->reviewer->name }}@endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('weekly-updates.download', [$update, 'format' => 'pdf']) }}" target="_blank"
                               class="text-xs text-red-600 hover:text-red-800 font-medium" title="Download PDF">PDF</a>
                            <a href="{{ route('weekly-updates.download', [$update, 'format' => 'word']) }}"
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium" title="Download Word">Word</a>
                            <a href="{{ route('weekly-updates.show', $update) }}"
                               class="text-xs text-blue-700 hover:underline">View</a>
                        </div>
                    </div>

                    {{-- Activities Table --}}
                    @if($update->activities->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left px-4 py-2 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-10">No.</th>
                                        <th class="text-left px-4 py-2 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 250px;">Activities/Task</th>
                                        <th class="text-left px-4 py-2 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 130px;">Responsible</th>
                                        <th class="text-left px-4 py-2 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-32">Status</th>
                                        <th class="text-left px-4 py-2 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 160px;">Comment</th>
                                        <th class="text-left px-4 py-2 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 160px;">Challenges</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($update->activities as $index => $activity)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2.5 text-gray-400 font-medium text-center align-top">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2.5 text-gray-800 align-top whitespace-pre-line text-xs">{{ $activity->activity }}</td>
                                            <td class="px-4 py-2.5 text-gray-600 align-top text-xs">{{ $activity->responsible_persons ?? '—' }}</td>
                                            <td class="px-4 py-2.5 align-top">
                                                @php
                                                    $statusColors = [
                                                        'not_started' => 'bg-red-100 text-red-700',
                                                        'ongoing'     => 'bg-yellow-100 text-yellow-700',
                                                        'completed'   => 'bg-green-100 text-green-700',
                                                        'na'          => 'bg-gray-100 text-gray-600',
                                                    ];
                                                    $statusDots = [
                                                        'not_started' => 'bg-red-500',
                                                        'ongoing'     => 'bg-yellow-400',
                                                        'completed'   => 'bg-green-500',
                                                        'na'          => 'bg-gray-400',
                                                    ];
                                                    $statusLabels = [
                                                        'not_started' => 'Not Started',
                                                        'ongoing'     => 'Ongoing',
                                                        'completed'   => 'Completed',
                                                        'na'          => 'N/A',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium {{ $statusColors[$activity->status_flag] ?? $statusColors['na'] }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusDots[$activity->status_flag] ?? $statusDots['na'] }}"></span>
                                                    {{ $statusLabels[$activity->status_flag] ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2.5 text-gray-600 align-top whitespace-pre-line text-xs">{{ $activity->status_comment ?? '—' }}</td>
                                            <td class="px-4 py-2.5 text-gray-600 align-top whitespace-pre-line text-xs">{{ $activity->challenges ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Additional Notes --}}
                    @if($update->support_needed || $update->key_metrics)
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                            @if($update->support_needed)
                                <p class="text-xs text-gray-600"><span class="font-medium">Support Needed:</span> {{ Str::limit($update->support_needed, 200) }}</p>
                            @endif
                            @if($update->key_metrics)
                                <p class="text-xs text-gray-600 mt-1"><span class="font-medium">Key Metrics:</span> {{ Str::limit($update->key_metrics, 200) }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Review Info --}}
                    @if($update->reviewed_by)
                        <div class="px-6 py-3 border-t border-gray-100">
                            <p class="text-xs text-gray-500">
                                Reviewed by <span class="font-medium text-gray-700">{{ $update->reviewer->name }}</span>
                                on {{ $update->reviewed_at->format('M d, Y') }}
                                @if($update->review_comments)
                                    — <span class="italic">{{ Str::limit($update->review_comments, 150) }}</span>
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @empty
        <div class="bg-white border border-gray-200 p-12 text-center">
            <p class="text-sm text-gray-500">No weekly updates found for the selected period.</p>
            <p class="text-xs text-gray-400 mt-1">Try adjusting the date range or status filter.</p>
        </div>
    @endforelse
</div>
@endsection
