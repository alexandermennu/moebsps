@extends('layouts.app')

@section('title', 'Weekly Updates')
@section('page-title', 'Weekly Updates')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between border-b border-gray-300 pb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                Weekly Updates: {{ $reportingWeekLabel }} 
                <span class="font-normal text-gray-500">
                    ({{ $reportingWeekStart->format('M d') }} – {{ $reportingWeekEnd->format('M d') }})
                </span>
            </h2>
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

    {{-- Current Week Updates Table --}}
    <div class="bg-white border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Division</th>
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
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-[10px] px-1.5 py-0.5 font-medium
                                    {{ $divStatus->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $divStatus->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $divStatus->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $divStatus->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $divStatus->status === 'not_submitted' ? 'bg-gray-100 text-gray-500' : '' }}">
                                    {{ $divStatus->status === 'not_submitted' ? 'Not Submitted' : ucfirst($divStatus->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">
                                @if($divStatus->update)
                                    {{ $divStatus->update->created_at->isToday() ? 'Today' : $divStatus->update->created_at->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                @if($divStatus->update && $divStatus->status === 'draft')
                                    <a href="{{ route('weekly-updates.edit', $divStatus->update) }}" class="text-xs text-blue-700 hover:underline">Edit</a>
                                @elseif($divStatus->update)
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
                            <td colspan="4" class="px-5 py-8 text-center text-gray-500">No divisions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Previous Weeks - Collapsible --}}
    @if($previousWeeksGrouped->count() > 0)
    <div class="bg-white border border-gray-200" x-data="{ expanded: false }">
        <button @click="expanded = !expanded" type="button" class="w-full px-5 py-3 flex items-center justify-between hover:bg-gray-50 border-b border-gray-200">
            <span class="text-[11px] text-gray-500 uppercase tracking-wide font-medium">Previous Weeks</span>
            <span class="text-xs text-gray-500 flex items-center gap-2">
                <span x-text="expanded ? 'Collapse' : 'Expand'">Expand</span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </span>
        </button>

        <div x-show="expanded" x-cloak class="divide-y divide-gray-100">
            @foreach($previousWeeksGrouped as $weekData)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 text-left flex-1">
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <div>
                            <span class="font-medium text-gray-800">{{ $weekData->week_label }}</span>
                            <span class="text-gray-500">({{ $weekData->week_start->format('M d') }} – {{ $weekData->week_end->format('M d') }})</span>
                        </div>
                    </button>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ $weekData->submitted_count }} updates</span>
                        @if($weekData->approved_count === $weekData->submitted_count && $weekData->submitted_count > 0)
                            <span class="text-xs px-1.5 py-0.5 bg-green-100 text-green-700 font-medium">All Approved</span>
                        @elseif($weekData->approved_count > 0)
                            <span class="text-xs px-1.5 py-0.5 bg-blue-100 text-blue-700 font-medium">{{ $weekData->approved_count }}/{{ $weekData->submitted_count }} Approved</span>
                        @else
                            <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-600 font-medium">Pending</span>
                        @endif
                    </div>
                </div>

                {{-- Expanded week details --}}
                <template x-if="open">
                    <div class="bg-gray-50 border-t border-gray-100">
                        @foreach($weekData->updates as $update)
                            <div class="px-5 py-2 pl-12 flex items-center justify-between border-b border-gray-100 last:border-0">
                                <span class="text-sm text-gray-700">{{ $update->division->name }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium
                                        {{ $update->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $update->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $update->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $update->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}">
                                        {{ ucfirst($update->status) }}
                                    </span>
                                    <a href="{{ route('weekly-updates.show', $update) }}" class="text-xs text-blue-700 hover:underline">View</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </template>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
