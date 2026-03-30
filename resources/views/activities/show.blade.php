@extends('layouts.app')

@section('title', $activity->title)
@section('page-title', 'Assignment Details')

@section('content')
@php
    $isAssignee = $activity->assigned_to === $user->id;
    $canEdit = $user->canManageDivision();
    $canUpdateProgress = $isAssignee || $canEdit;
    $canUploadFiles = $isAssignee || $canEdit;
    $isNotStarted = $activity->status === 'not_started';
@endphp

<div class="max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('activities.index') }}" class="text-xs text-blue-700 hover:underline">Back to Assignments</a>
        <div class="flex gap-2">
            @if($canEdit)
                <a href="{{ route('activities.edit', $activity) }}" class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">Edit</a>
            @endif
            @if($user->hasFullAccess())
                <form method="POST" action="{{ route('activities.destroy', $activity) }}" onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 bg-red-700 text-white text-sm font-medium hover:bg-red-800">Delete</button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $activity->title }}</h2>
                    <div class="flex gap-2">
                        <span class="text-[10px] px-1.5 py-0.5 font-medium
                            {{ $activity->status === 'completed' ? 'bg-green-50 text-green-700' : '' }}
                            {{ $activity->status === 'in_progress' ? 'bg-blue-50 text-blue-700' : '' }}
                            {{ $activity->status === 'overdue' ? 'bg-red-50 text-red-700' : '' }}
                            {{ $activity->status === 'not_started' ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ str_replace('_', ' ', ucfirst($activity->status)) }}
                        </span>
                        <span class="text-[10px] px-1.5 py-0.5 font-medium
                            {{ $activity->priority === 'critical' ? 'bg-red-50 text-red-700' : '' }}
                            {{ $activity->priority === 'high' ? 'bg-orange-50 text-orange-700' : '' }}
                            {{ $activity->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : '' }}
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
                    <div class="w-full bg-gray-200 h-2">
                        <div class="bg-gray-600 h-2 transition-all" style="width: {{ $activity->progress_percentage }}%"></div>
                    </div>
                </div>

                @if($activity->is_escalated)
                    <div class="bg-orange-50 border border-orange-200 p-3 mb-4">
                        <p class="text-sm text-orange-800 font-medium">This assignment has been escalated to {{ str_replace('_', ' ', ucfirst($activity->escalated_to)) }}</p>
                        <p class="text-xs text-orange-600 mt-1">Escalated {{ $activity->escalated_at?->diffForHumans() }}</p>
                    </div>
                @endif

                @if($activity->remarks)
                    <div class="mt-4">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Remarks</h3>
                        <p class="text-sm text-gray-600">{{ $activity->remarks }}</p>
                    </div>
                @endif
            </div>

            {{-- Update Progress Section (for assignees) --}}
            @if($canUpdateProgress && $activity->status !== 'completed')
            <div class="bg-white border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">Update Progress</h3>
                
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('activities.progress', $activity) }}">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" required onchange="handleStatusChange(this)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                                <option value="not_started" {{ $activity->status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                <option value="in_progress" {{ $activity->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ $activity->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="overdue" {{ $activity->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div>
                            <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-1">
                                Progress (<span id="progress_value">{{ $activity->progress_percentage }}</span>%)
                            </label>
                            <input type="range" name="progress_percentage" id="progress_percentage" min="0" max="100" step="5"
                                   value="{{ $activity->progress_percentage }}"
                                   {{ $isNotStarted ? 'disabled' : '' }}
                                   class="w-full h-2 bg-gray-200 appearance-none cursor-pointer {{ $isNotStarted ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   oninput="document.getElementById('progress_value').textContent = this.value">
                            <p id="progress_hint" class="text-xs text-amber-600 mt-1 {{ $isNotStarted ? '' : 'hidden' }}">
                                Change status to "In Progress" to update progress.
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 rounded-md">
                        Save Progress
                    </button>
                </form>
            </div>
            @endif

            {{-- Files Section --}}
            <div class="bg-white border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Attachments ({{ $activity->files->count() }})</h3>
                    @if($canUploadFiles)
                    <button type="button" onclick="document.getElementById('file_upload_section').classList.toggle('hidden')"
                            class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Files
                    </button>
                    @endif
                </div>

                {{-- Upload Form --}}
                @if($canUploadFiles)
                <div id="file_upload_section" class="hidden px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <form method="POST" action="{{ route('activities.files', $activity) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Files</label>
                            <input type="file" name="files[]" id="file_input" multiple required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.txt,.csv,.zip"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-gray-800 file:text-white hover:file:bg-gray-700">
                            <p class="text-xs text-gray-400 mt-1">Max 10MB per file. Supported: PDF, Word, Excel, PowerPoint, Images, Text, CSV, ZIP</p>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                            <input type="text" name="description" id="description" placeholder="Brief description of the files..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 rounded-md">Upload</button>
                            <button type="button" onclick="document.getElementById('file_upload_section').classList.add('hidden')"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">Cancel</button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- Files List --}}
                <div class="divide-y divide-gray-100">
                    @forelse($activity->files as $file)
                        <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                {{-- File Icon --}}
                                <div class="shrink-0 w-8 h-8 flex items-center justify-center rounded bg-gray-100">
                                    @if($file->is_pdf)
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                    @elseif($file->is_image)
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $file->original_filename }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $file->human_file_size }} · Uploaded by {{ $file->uploader->name }} · {{ $file->created_at->diffForHumans() }}
                                    </p>
                                    @if($file->description)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $file->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-4">
                                <a href="{{ route('activities.files.download', [$activity, $file]) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">Download</a>
                                @if($file->uploaded_by === $user->id || $user->canManageDivision())
                                    <form method="POST" action="{{ route('activities.files.delete', [$activity, $file]) }}" onsubmit="return confirm('Delete this file?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-6 text-center text-sm text-gray-500">No files attached yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Comments Section --}}
            <div class="bg-white border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Comments ({{ $activity->comments->count() }})</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($activity->comments as $comment)
                        <div class="px-6 py-4">
                            <div class="flex items-center gap-2 mb-2">
                                <x-user-avatar :user="$comment->user" size="xs" />
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
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-4">
            <div class="bg-white border border-gray-200 p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Details</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Division</dt>
                        <dd class="font-medium text-gray-800">{{ $activity->division?->name ?? 'Office of the Minister' }}</dd>
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

            {{-- Quick Actions for Assignee --}}
            @if($isAssignee && $activity->status !== 'completed')
            <div class="bg-blue-50 border border-blue-200 p-5 rounded">
                <h3 class="text-xs font-semibold text-blue-800 uppercase tracking-wide mb-2">Quick Actions</h3>
                <p class="text-xs text-blue-600 mb-3">You are assigned to this task. Use the Update Progress section to track your work.</p>
                @if($isNotStarted)
                    <form method="POST" action="{{ route('activities.progress', $activity) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="in_progress">
                        <input type="hidden" name="progress_percentage" value="5">
                        <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 rounded">
                            Start Working
                        </button>
                    </form>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function handleStatusChange(select) {
    const progressSlider = document.getElementById('progress_percentage');
    const progressHint = document.getElementById('progress_hint');
    const isNotStarted = select.value === 'not_started';
    
    if (isNotStarted) {
        progressSlider.disabled = true;
        progressSlider.value = 0;
        document.getElementById('progress_value').textContent = '0';
        progressSlider.classList.add('opacity-50', 'cursor-not-allowed');
        progressHint.classList.remove('hidden');
    } else {
        progressSlider.disabled = false;
        progressSlider.classList.remove('opacity-50', 'cursor-not-allowed');
        progressHint.classList.add('hidden');
        
        // If completed, set to 100
        if (select.value === 'completed') {
            progressSlider.value = 100;
            document.getElementById('progress_value').textContent = '100';
        }
    }
}
</script>
@endsection
