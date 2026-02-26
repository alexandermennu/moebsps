@extends('layouts.app')

@section('title', 'Weekly Update Details')
@section('page-title', 'Weekly Update')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('weekly-updates.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Weekly Updates</a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Week of {{ $weeklyUpdate->week_start->format('M d') }} - {{ $weeklyUpdate->week_end->format('M d, Y') }}
                </h2>
                <p class="text-sm text-gray-500">{{ $weeklyUpdate->division->name }} · By {{ $weeklyUpdate->submitter->name }}</p>
            </div>
            <span class="text-xs px-3 py-1 rounded-full
                {{ $weeklyUpdate->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                {{ $weeklyUpdate->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $weeklyUpdate->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                {{ $weeklyUpdate->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}">
                {{ ucfirst($weeklyUpdate->status) }}
            </span>
        </div>

        <div class="space-y-5">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Accomplishments</h3>
                <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 rounded-md p-4">{{ $weeklyUpdate->accomplishments }}</div>
            </div>

            @if($weeklyUpdate->challenges)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Challenges</h3>
                    <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 rounded-md p-4">{{ $weeklyUpdate->challenges }}</div>
                </div>
            @endif

            @if($weeklyUpdate->support_needed)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Support Needed</h3>
                    <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 rounded-md p-4">{{ $weeklyUpdate->support_needed }}</div>
                </div>
            @endif

            @if($weeklyUpdate->key_metrics)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Key Metrics</h3>
                    <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 rounded-md p-4">{{ $weeklyUpdate->key_metrics }}</div>
                </div>
            @endif
        </div>

        {{-- Review Info --}}
        @if($weeklyUpdate->reviewed_by)
            <div class="mt-6 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500">Reviewed by <span class="font-medium text-gray-700">{{ $weeklyUpdate->reviewer->name }}</span> on {{ $weeklyUpdate->reviewed_at->format('M d, Y \a\t H:i') }}</p>
                @if($weeklyUpdate->review_comments)
                    <div class="mt-2 text-sm text-gray-600 bg-yellow-50 border border-yellow-200 rounded-md p-3">
                        <span class="font-medium">Review Comments:</span> {{ $weeklyUpdate->review_comments }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Actions --}}
        <div class="mt-6 pt-4 border-t border-gray-200 flex gap-3">
            @if($user->isDirector() && in_array($weeklyUpdate->status, ['draft', 'rejected']))
                <a href="{{ route('weekly-updates.edit', $weeklyUpdate) }}" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">Edit</a>
            @endif

            @if(($user->isBureauHead() || $user->isMinister()) && $weeklyUpdate->status === 'submitted')
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
</div>
@endsection
