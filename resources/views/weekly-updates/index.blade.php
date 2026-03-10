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
                <a href="{{ route('weekly-updates.consolidated') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">
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

    {{-- Filters --}}
    <form method="GET" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm hover:bg-gray-200">Filter</button>
    </form>

    {{-- Updates Table --}}
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
                    @forelse($updates as $update)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-800">{{ $update->week_label_short }}</div>
                                <div class="text-xs text-gray-500">{{ $update->week_start->format('M d') }} - {{ $update->week_end->format('M d') }}</div>
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
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-500">No weekly updates found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $updates->links() }}

    {{-- Division Reports Summary Cards --}}
    @if($divisionSummaries->count() > 0)
    <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2">Division Reports</h3>
                <p class="text-sm text-gray-500 mt-2">Click a division to view its full reports</p>
            </div>
            <a href="{{ route('weekly-updates.consolidated') }}" class="text-xs text-blue-700 hover:underline">View all consolidated</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($divisionSummaries as $divSummary)
                <a href="{{ route('weekly-updates.consolidated', ['division_id' => $divSummary->id]) }}"
                   class="bg-white border border-gray-200 p-5 hover:border-blue-400 transition group block">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-700">{{ $divSummary->name }}</h4>
                            <p class="text-xs text-gray-400">{{ $divSummary->code }}</p>
                        </div>
                        @if($divSummary->latest_update)
                            <span class="text-[10px] px-1.5 py-0.5 font-medium
                                {{ $divSummary->latest_update->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $divSummary->latest_update->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $divSummary->latest_update->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $divSummary->latest_update->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ ucfirst($divSummary->latest_update->status) }}
                            </span>
                        @endif
                    </div>

                    {{-- Quick stats --}}
                    <div class="grid grid-cols-4 gap-2 mb-3">
                        <div class="text-center p-1.5 bg-gray-50">
                            <p class="text-sm font-bold text-gray-800">{{ $divSummary->total_updates }}</p>
                            <p class="text-[10px] text-gray-400">Total</p>
                        </div>
                        <div class="text-center p-1.5 bg-green-50">
                            <p class="text-sm font-bold text-green-700">{{ $divSummary->approved_updates }}</p>
                            <p class="text-[10px] text-green-500">Approved</p>
                        </div>
                        <div class="text-center p-1.5 bg-blue-50">
                            <p class="text-sm font-bold text-blue-700">{{ $divSummary->submitted_updates }}</p>
                            <p class="text-[10px] text-blue-500">Pending</p>
                        </div>
                        <div class="text-center p-1.5 bg-red-50">
                            <p class="text-sm font-bold text-red-700">{{ $divSummary->rejected_updates }}</p>
                            <p class="text-[10px] text-red-500">Rejected</p>
                        </div>
                    </div>

                    {{-- Latest update info --}}
                    @if($divSummary->latest_update)
                        <p class="text-xs text-gray-500 mb-1.5">Latest: {{ $divSummary->latest_update->week_label_short }}</p>
                        @if(array_sum($divSummary->activity_stats) > 0)
                            <div class="flex items-center gap-1.5 text-xs">
                                @if($divSummary->activity_stats['completed'] > 0)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ $divSummary->activity_stats['completed'] }} done
                                    </span>
                                @endif
                                @if($divSummary->activity_stats['ongoing'] > 0)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>{{ $divSummary->activity_stats['ongoing'] }} ongoing
                                    </span>
                                @endif
                                @if($divSummary->activity_stats['not_started'] > 0)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ $divSummary->activity_stats['not_started'] }} pending
                                    </span>
                                @endif
                            </div>
                        @endif
                    @else
                        <p class="text-xs text-gray-400 italic">No updates submitted yet.</p>
                    @endif

                    <p class="text-xs text-blue-700 mt-3 group-hover:underline font-medium">View division reports</p>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
