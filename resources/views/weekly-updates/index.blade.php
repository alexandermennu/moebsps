@extends('layouts.app')

@section('title', 'Weekly Updates')
@section('page-title', 'Weekly Updates')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between border-b border-gray-300 pb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Weekly Updates</h2>
            <p class="text-sm text-gray-500">Review and manage weekly activity reports</p>
        </div>
        <div class="flex items-center gap-2">
            @if($user->hasFullAccess() || $user->isDirector())
                <a href="{{ route('weekly-updates.consolidated') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">
                    Consolidated Reports
                </a>
            @endif
            @if($user->canManageDivision())
                <a href="{{ route('weekly-updates.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">
                    + New Update
                </a>
            @endif
        </div>
    </div>

    {{-- Updates Due This Week (for last week's activities) --}}
    <div>
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-[11px] text-gray-500 uppercase tracking-wide font-medium">
                Updates Due This Week (for 
                @if($reportingWeekStart->format('F') === $reportingWeekEnd->format('F'))
                    {{ $reportingWeekStart->format('M d') }} – {{ $reportingWeekEnd->format('d, Y') }}
                @else
                    {{ $reportingWeekStart->format('M d') }} – {{ $reportingWeekEnd->format('M d, Y') }}
                @endif
                )
            </h3>
            @php
                $submittedCount = $dueThisWeekStatus->filter(fn($s) => $s->status !== 'not_submitted')->count();
                $totalCount = $dueThisWeekStatus->count();
            @endphp
            <span class="text-xs text-gray-500">{{ $submittedCount }}/{{ $totalCount }} divisions submitted</span>
        </div>

        <div class="bg-white border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Division</th>
                            <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Submitted By</th>
                            <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Status</th>
                            <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Submitted</th>
                            <th class="text-right px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dueThisWeekStatus as $divStatus)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-800">{{ $divStatus->division->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $divStatus->division->code }}</div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{ $divStatus->update ? $divStatus->update->submitter->name : '—' }}
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium
                                        {{ $divStatus->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $divStatus->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $divStatus->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $divStatus->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                                        {{ $divStatus->status === 'not_submitted' ? 'bg-gray-100 text-gray-500' : '' }}">
                                        {{ $divStatus->status === 'not_submitted' ? 'Not Submitted' : ucfirst($divStatus->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-500">
                                    {{ $divStatus->update ? $divStatus->update->created_at->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @if($divStatus->update)
                                        <a href="{{ route('weekly-updates.show', $divStatus->update) }}" class="text-xs text-blue-700 hover:underline">View</a>
                                    @elseif($user->canManageDivision() && $divStatus->division->id === $user->division_id)
                                        <a href="{{ route('weekly-updates.create') }}" class="text-xs text-blue-700 hover:underline">Submit</a>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500">No divisions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Previous Weeks --}}
    <div>
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-[11px] text-gray-500 uppercase tracking-wide font-medium">Previous Weeks</h3>
            <form method="GET" class="flex items-center gap-2">
                <select name="status" class="px-3 py-1.5 border border-gray-300 text-sm">
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-gray-100 border border-gray-300 text-sm hover:bg-gray-200">Filter</button>
            </form>
        </div>

        <div class="bg-white border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Week</th>
                            <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Division</th>
                            <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Submitted By</th>
                            <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Status</th>
                            <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Submitted</th>
                            <th class="text-right px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php $hasUpdates = false; @endphp
                        @foreach($previousWeeksGrouped as $weekData)
                            @foreach($weekData->updates as $update)
                                @php $hasUpdates = true; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3">
                                        <div class="font-medium text-gray-800">{{ $weekData->week_label }}</div>
                                        <div class="text-xs text-gray-500">{{ $weekData->week_start->format('M d') }} - {{ $weekData->week_end->format('M d') }}</div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ $update->division->name }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $update->submitter->name }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="text-[10px] px-1.5 py-0.5 font-medium
                                            {{ $update->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $update->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $update->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $update->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}">
                                            {{ ucfirst($update->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-500">{{ $update->created_at->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('weekly-updates.show', $update) }}" class="text-xs text-blue-700 hover:underline">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        @if(!$hasUpdates)
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500">No previous weekly updates found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
