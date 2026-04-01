@extends('layouts.app')

@section('title', 'Weekly Updates')
@section('page-title', 'Weekly Updates')

@section('content')
<div class="space-y-4">
    {{-- Header Section --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-gray-900">
                Reporting Week: {{ $reportingWeekLabel }} 
                <span class="font-normal text-gray-500 text-sm">({{ $reportingWeekStart->format('M d') }} – {{ $reportingWeekEnd->format('M d') }})</span>
            </h2>
            <p class="text-xs text-gray-600 mt-0.5">
                {{ $submittedCount }}/{{ $allDivisions->count() }} divisions submitted
                @if($overdueCount > 0) | <span class="text-red-600">{{ $overdueCount }} overdue</span>@endif
                @if($pendingCount > 0) | <span class="text-orange-600">{{ $pendingCount }} pending</span>@endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($user->hasFullAccess() || $user->isDirector())
                <button type="button" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50">
                    Send Reminder
                </button>
                <a href="{{ route('weekly-updates.consolidated') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium hover:bg-blue-700">
                    View Consolidated Report
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
            @endif
        </div>
    </div>

    {{-- Status Summary Pills --}}
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
            <span class="text-xs text-gray-700">{{ $onTimeCount }} On Time</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
            <span class="text-xs text-gray-700">{{ $lateCount }} Late</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
            <span class="text-xs text-gray-700">{{ $notSubmittedCount }} Not Submitted</span>
        </div>
    </div>

    {{-- Main Division Table --}}
    <div class="bg-white border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-2.5 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Division</th>
                    <th class="text-left px-4 py-2.5 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Status</th>
                    <th class="text-left px-4 py-2.5 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Submission Details</th>
                    <th class="text-left px-4 py-2.5 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Content</th>
                    <th class="text-center px-4 py-2.5 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Action</th>
                    <th class="text-right px-4 py-2.5 text-[11px] text-gray-500 uppercase tracking-wide font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($divisionStatuses as $divStatus)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5">
                            <span class="font-medium text-gray-900">{{ $divStatus->division->name }}</span>
                        </td>
                        <td class="px-4 py-2.5">
                            @if($divStatus->status_color === 'green')
                                <span class="inline-flex items-center gap-1.5 text-green-600 font-medium text-xs">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    {{ $divStatus->status_label }}
                                </span>
                            @elseif($divStatus->status_color === 'orange')
                                <span class="inline-flex items-center gap-1.5 text-orange-600 font-medium text-xs">
                                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                    {{ $divStatus->status_label }}
                                </span>
                            @elseif($divStatus->status_color === 'red')
                                <span class="inline-flex items-center gap-1.5 text-red-600 font-medium text-xs">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    {{ $divStatus->status_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-gray-500 font-medium text-xs">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    {{ $divStatus->status_label }}
                                </span>
                            @endif
                            <p class="text-[11px] text-gray-500">{{ $divStatus->status_detail }}</p>
                        </td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">
                            {{ $divStatus->submission_details }}
                        </td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">
                            @if($divStatus->has_content)
                                {{ $divStatus->activity_count }} {{ Str::plural('activity', $divStatus->activity_count) }}
                            @else
                                <span class="text-gray-400">No data</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            @if($divStatus->update)
                                <a href="{{ route('weekly-updates.show', $divStatus->update) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 text-white text-[11px] font-medium hover:bg-blue-700">
                                    View Report
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @elseif($user->hasFullAccess() || $user->isDirector())
                                <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 bg-orange-50 text-orange-700 text-[11px] font-medium hover:bg-orange-100 border border-orange-200">
                                    Request Submission
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <a href="{{ $divStatus->update ? route('weekly-updates.show', $divStatus->update) : route('activities.index', ['division_id' => $divStatus->division->id]) }}" class="text-blue-600 hover:underline text-xs">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">No divisions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Past Reports Section --}}
    @if($previousWeeksGrouped->count() > 0)
    <div>
        <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-3">Past Reports</h3>
        
        <div class="space-y-1.5">
            @foreach($previousWeeksGrouped as $weekData)
                <div class="flex items-center justify-between bg-white border border-gray-200 px-4 py-2 hover:bg-gray-50">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-blue-600 text-sm">{{ $weekData->week_label }}</span>
                        <span class="text-gray-500 text-xs">({{ $weekData->week_start->format('M d') }} – {{ $weekData->week_end->format('M d') }})</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-600">{{ $weekData->submitted_count }}/{{ $weekData->total_divisions }} submitted</span>
                        @if($weekData->is_complete)
                            <span class="inline-flex items-center gap-1 text-[11px] text-green-700">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Complete
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] text-orange-600">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Incomplete
                            </span>
                        @endif
                        <a href="{{ route('weekly-updates.consolidated', ['week_start' => $weekData->week_start->toDateString()]) }}" class="px-2.5 py-1 bg-blue-600 text-white text-[11px] font-medium hover:bg-blue-700">View</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
