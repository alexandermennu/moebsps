@extends('layouts.app')

@section('title', 'Weekly Update Details')
@section('page-title', 'Weekly Update')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('weekly-updates.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Weekly Updates</a>
    </div>

    {{-- Header --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Week of {{ $weeklyUpdate->week_start->format('M d') }} – {{ $weeklyUpdate->week_end->format('M d, Y') }}
                </h2>
                <p class="text-sm text-gray-500">{{ $weeklyUpdate->division->name }} · Submitted by {{ $weeklyUpdate->submitter->name }}</p>
            </div>
            <span class="text-xs px-3 py-1 rounded-full font-medium
                {{ $weeklyUpdate->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                {{ $weeklyUpdate->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $weeklyUpdate->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                {{ $weeklyUpdate->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}">
                {{ ucfirst($weeklyUpdate->status) }}
            </span>
        </div>

        {{-- Status Legend --}}
        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-xs font-semibold text-gray-600 mb-2">Legend: Status</p>
            <div class="flex flex-wrap gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Red = Not Started</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> Yellow = Ongoing</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Green = Completed</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span> N/A = Not Available</span>
            </div>
        </div>
    </div>

    {{-- Activities Table --}}
    @if($weeklyUpdate->activities->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800">Activities / Tasks</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $weeklyUpdate->activities->count() }} {{ Str::plural('activity', $weeklyUpdate->activities->count()) }} recorded</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium w-12">No.</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium" style="min-width: 280px;">Activities/Task</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium" style="min-width: 150px;">Responsible Persons</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium w-40">Status</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium" style="min-width: 180px;">Status Comment</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium" style="min-width: 180px;">Challenges</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($weeklyUpdate->activities as $index => $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-400 font-medium text-center align-top">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-800 align-top whitespace-pre-line">{{ $activity->activity }}</td>
                                <td class="px-4 py-3 text-gray-600 align-top">{{ $activity->responsible_persons ?? '—' }}</td>
                                <td class="px-4 py-3 align-top">
                                    @php
                                        $colors = [
                                            'not_started' => 'bg-red-100 text-red-700 border-red-200',
                                            'ongoing'     => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'completed'   => 'bg-green-100 text-green-700 border-green-200',
                                            'na'          => 'bg-gray-100 text-gray-600 border-gray-200',
                                        ];
                                        $dots = [
                                            'not_started' => 'bg-red-500',
                                            'ongoing'     => 'bg-yellow-400',
                                            'completed'   => 'bg-green-500',
                                            'na'          => 'bg-gray-400',
                                        ];
                                        $labels = [
                                            'not_started' => 'Not Started',
                                            'ongoing'     => 'Ongoing',
                                            'completed'   => 'Completed',
                                            'na'          => 'N/A',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $colors[$activity->status_flag] ?? $colors['na'] }}">
                                        <span class="w-2 h-2 rounded-full {{ $dots[$activity->status_flag] ?? $dots['na'] }}"></span>
                                        {{ $labels[$activity->status_flag] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 align-top whitespace-pre-line">{{ $activity->status_comment ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 align-top whitespace-pre-line">{{ $activity->challenges ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Legacy fields / Additional Notes --}}
    @if($weeklyUpdate->accomplishments || $weeklyUpdate->support_needed || $weeklyUpdate->key_metrics || $weeklyUpdate->challenges)
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Additional Notes</h3>
            <div class="space-y-4">
                @if($weeklyUpdate->accomplishments)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Accomplishments</h4>
                        <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 rounded-md p-4">{{ $weeklyUpdate->accomplishments }}</div>
                    </div>
                @endif

                @if($weeklyUpdate->challenges && $weeklyUpdate->activities->count() === 0)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Challenges</h4>
                        <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 rounded-md p-4">{{ $weeklyUpdate->challenges }}</div>
                    </div>
                @endif

                @if($weeklyUpdate->support_needed)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Support Needed</h4>
                        <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 rounded-md p-4">{{ $weeklyUpdate->support_needed }}</div>
                    </div>
                @endif

                @if($weeklyUpdate->key_metrics)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Key Metrics</h4>
                        <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 rounded-md p-4">{{ $weeklyUpdate->key_metrics }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Review Info --}}
    @if($weeklyUpdate->reviewed_by)
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-2">Review</h3>
            <p class="text-sm text-gray-500">Reviewed by <span class="font-medium text-gray-700">{{ $weeklyUpdate->reviewer->name }}</span> on {{ $weeklyUpdate->reviewed_at->format('M d, Y \a\t H:i') }}</p>
            @if($weeklyUpdate->review_comments)
                <div class="mt-3 text-sm text-gray-600 bg-yellow-50 border border-yellow-200 rounded-md p-3">
                    <span class="font-medium">Comments:</span> {{ $weeklyUpdate->review_comments }}
                </div>
            @endif
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex gap-3">
        @if($user->canManageDivision() && in_array($weeklyUpdate->status, ['draft', 'rejected']))
            <a href="{{ route('weekly-updates.edit', $weeklyUpdate) }}" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">Edit</a>
        @endif

        @if($user->canReview() && $weeklyUpdate->status === 'submitted')
            <form method="POST" action="{{ route('weekly-updates.review', $weeklyUpdate) }}" class="flex gap-3 items-end flex-1">
                @csrf
                <div class="flex-1">
                    <input type="text" name="review_comments" placeholder="Review comments (optional)" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                </div>
                <button type="submit" name="action" value="approved" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">Approve</button>
                <button type="submit" name="action" value="rejected" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Reject</button>
            </form>
        @endif
    </div>
</div>
@endsection
