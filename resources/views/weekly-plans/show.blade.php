@extends('layouts.app')

@section('title', 'Weekly Plan Details')
@section('page-title', 'Weekly Plan')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('weekly-plans.index') }}" class="text-xs text-blue-700 hover:underline">Back to Weekly Plans</a>
    </div>

    {{-- Header --}}
    <div class="bg-white border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Week of {{ $weeklyPlan->week_start->format('M d') }} - {{ $weeklyPlan->week_end->format('M d, Y') }}
                </h2>
                <p class="text-sm text-gray-500">{{ $weeklyPlan->division->name }} · By {{ $weeklyPlan->submitter->name }}</p>
            </div>
            <span class="text-[10px] px-1.5 py-0.5 font-medium
                {{ $weeklyPlan->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                {{ $weeklyPlan->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $weeklyPlan->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                {{ $weeklyPlan->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}">
                {{ ucfirst($weeklyPlan->status) }}
            </span>
        </div>
    </div>

    {{-- Activities Table --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-800">Planned Activities</h3>
            <p class="text-xs text-gray-500 mt-0.5">{{ $weeklyPlan->activities->count() }} {{ Str::plural('activity', $weeklyPlan->activities->count()) }} planned</p>
        </div>

        @if($weeklyPlan->activities->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-10">No.</th>
                            <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 320px;">Activities</th>
                            <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 180px;">Responsible Persons</th>
                            <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 200px;">Status / Comment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($weeklyPlan->activities as $index => $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-400 font-medium text-center align-top">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-800 align-top whitespace-pre-line">{{ $activity->activity }}</td>
                                <td class="px-4 py-3 text-gray-600 align-top">{{ $activity->responsible_persons ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 align-top whitespace-pre-line">{{ $activity->status_comment ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- Fallback for legacy plans that used planned_activities text field --}}
            @if($weeklyPlan->planned_activities)
                <div class="p-6">
                    <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 p-4">{{ $weeklyPlan->planned_activities }}</div>
                </div>
            @else
                <div class="px-6 py-8 text-center text-sm text-gray-400">No activities have been added to this plan.</div>
            @endif
        @endif
    </div>

    {{-- Additional Notes --}}
    @if($weeklyPlan->objectives || $weeklyPlan->expected_outcomes || $weeklyPlan->resources_needed)
        <div class="bg-white border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Additional Notes</h3>
            <div class="space-y-4">
                @if($weeklyPlan->objectives)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Objectives</h4>
                        <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 p-3">{{ $weeklyPlan->objectives }}</div>
                    </div>
                @endif

                @if($weeklyPlan->expected_outcomes)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Expected Outcomes</h4>
                        <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 p-3">{{ $weeklyPlan->expected_outcomes }}</div>
                    </div>
                @endif

                @if($weeklyPlan->resources_needed)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Resources Needed</h4>
                        <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 p-3">{{ $weeklyPlan->resources_needed }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Review Section --}}
    @if($weeklyPlan->reviewed_by)
        <div class="bg-white border border-gray-200 p-6 mb-6">
            <p class="text-sm text-gray-500">Reviewed by <span class="font-medium text-gray-700">{{ $weeklyPlan->reviewer->name }}</span> on {{ $weeklyPlan->reviewed_at->format('M d, Y \a\t H:i') }}</p>
            @if($weeklyPlan->review_comments)
                <div class="mt-2 text-sm text-gray-600 bg-yellow-50 border border-yellow-200 p-3">
                    <span class="font-medium">Review Comments:</span> {{ $weeklyPlan->review_comments }}
                </div>
            @endif
        </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex gap-3">
        @if($user->canManageDivision() && in_array($weeklyPlan->status, ['draft', 'rejected']))
            <a href="{{ route('weekly-plans.edit', $weeklyPlan) }}" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">Edit</a>
        @endif

        @if($user->canReviewSubmissions() && $weeklyPlan->status === 'submitted')
            <form method="POST" action="{{ route('weekly-plans.review', $weeklyPlan) }}" class="flex gap-3 items-end flex-1">
                @csrf
                <div class="flex-1">
                    <input type="text" name="review_comments" placeholder="Review comments (optional)" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                </div>
                <button type="submit" name="action" value="approved" class="px-4 py-2 bg-green-600 text-white text-sm font-medium hover:bg-green-700">Approve</button>
                <button type="submit" name="action" value="rejected" class="px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700">Reject</button>
            </form>
        @endif
    </div>
</div>
@endsection
