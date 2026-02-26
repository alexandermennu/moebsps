@extends('layouts.app')

@section('title', $activity->title)
@section('page-title', 'Activity Details')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('activities.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Activities</a>
        <div class="flex gap-2">
            @if($user->isDirector() || $user->isBureauHead() || $user->isAdmin())
                <a href="{{ route('activities.edit', $activity) }}" class="px-3 py-1.5 bg-slate-800 text-white text-sm rounded-md hover:bg-slate-700">Edit</a>
            @endif
            @if($user->isAdmin() || $user->isBureauHead())
                <form method="POST" action="{{ route('activities.destroy', $activity) }}" onsubmit="return confirm('Are you sure you want to delete this activity?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Delete</button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ $activity->title }}</h2>
                    <div class="flex gap-2">
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $activity->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $activity->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $activity->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $activity->status === 'not_started' ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ str_replace('_', ' ', ucfirst($activity->status)) }}
                        </span>
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $activity->priority === 'critical' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $activity->priority === 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $activity->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $activity->priority === 'low' ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ ucfirst($activity->priority) }}
                        </span>
                    </div>
                </div>

                @if($activity->description)
                    <div class="text-sm text-gray-600 whitespace-pre-line mb-4">{{ $activity->description }}</div>
                @endif

                {{-- Progress Bar --}}
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">Progress</span>
                        <span class="font-medium text-gray-700">{{ $activity->progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-slate-600 h-2.5 rounded-full transition-all" style="width: {{ $activity->progress_percentage }}%"></div>
                    </div>
                </div>

                @if($activity->is_escalated)
                    <div class="bg-orange-50 border border-orange-200 rounded-md p-3 mb-4">
                        <p class="text-sm text-orange-800 font-medium">🔺 This activity has been escalated to {{ str_replace('_', ' ', ucfirst($activity->escalated_to)) }}</p>
                        <p class="text-xs text-orange-600 mt-1">Escalated {{ $activity->escalated_at?->diffForHumans() }}</p>
                    </div>
                @endif

                @if($activity->remarks)
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Remarks</h3>
                        <p class="text-sm text-gray-600">{{ $activity->remarks }}</p>
                    </div>
                @endif
            </div>

            {{-- Comments Section --}}
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Comments ({{ $activity->comments->count() }})</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($activity->comments as $comment)
                        <div class="px-6 py-4">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 bg-slate-200 rounded-full flex items-center justify-center text-xs font-bold text-slate-600">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-800">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-600 ml-8">{{ $comment->comment }}</p>
                        </div>
                    @empty
                        <div class="px-6 py-6 text-center text-sm text-gray-500">No comments yet.</div>
                    @endforelse
                </div>

                {{-- Add Comment --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('activities.comment', $activity) }}">
                        @csrf
                        <div class="flex gap-3">
                            <input type="text" name="comment" required placeholder="Add a comment..."
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-md hover:bg-slate-700">Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-4">
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Details</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Division</dt>
                        <dd class="font-medium text-gray-800">{{ $activity->division->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Assigned To</dt>
                        <dd class="font-medium text-gray-800">{{ $activity->assignee?->name ?? 'Unassigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Created By</dt>
                        <dd class="font-medium text-gray-800">{{ $activity->creator->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Start Date</dt>
                        <dd class="font-medium text-gray-800">{{ $activity->start_date?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Due Date</dt>
                        <dd class="font-medium {{ $activity->is_overdue ? 'text-red-600' : 'text-gray-800' }}">{{ $activity->due_date->format('M d, Y') }}</dd>
                    </div>
                    @if($activity->completed_date)
                        <div>
                            <dt class="text-gray-500">Completed</dt>
                            <dd class="font-medium text-green-600">{{ $activity->completed_date->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-gray-500">Created</dt>
                        <dd class="text-gray-600">{{ $activity->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
