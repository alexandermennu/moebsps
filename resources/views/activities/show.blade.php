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

                {{-- Upload Form with Preview --}}
                @if($canUploadFiles)
                <div id="file_upload_section" class="hidden px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <form id="file_upload_form" method="POST" action="{{ route('activities.files', $activity) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Files</label>
                            <div id="drop_zone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer"
                                 onclick="document.getElementById('file_input').click()">
                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Click to select files or drag & drop</p>
                                <p class="text-xs text-gray-400 mt-1">Max 10MB per file. PDF, Word, Excel, Images, etc.</p>
                            </div>
                            <input type="file" name="files[]" id="file_input" multiple required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.txt,.csv,.zip"
                                   class="hidden" onchange="previewFiles(this)">
                        </div>
                        
                        {{-- File Preview Area --}}
                        <div id="file_preview_area" class="hidden mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-medium text-gray-700">Selected Files <span id="file_count_badge" class="ml-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">0</span></label>
                                <button type="button" onclick="clearAllFiles()" class="text-xs text-red-600 hover:text-red-800 font-medium">Clear All</button>
                            </div>
                            <div id="file_preview_list" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                            <p class="text-xs text-gray-500 mt-2">Click <strong>+</strong> or drag more files to add. Click × on a file to remove it.</p>
                        </div>

                        {{-- Upload Progress --}}
                        <div id="upload_progress_area" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-blue-800">Uploading files...</span>
                                <span id="upload_progress_text" class="text-sm font-bold text-blue-600">0%</span>
                            </div>
                            <div class="w-full bg-blue-200 rounded-full h-3 overflow-hidden">
                                <div id="upload_progress_bar" class="bg-blue-600 h-3 rounded-full transition-all duration-200" style="width: 0%"></div>
                            </div>
                            <p id="upload_status_text" class="text-xs text-blue-600 mt-2">Preparing upload...</p>
                        </div>

                        <div class="mb-3">
                            <label for="file_description" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                            <input type="text" name="description" id="file_description" placeholder="Brief description of the files..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" id="upload_btn" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 rounded-md inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Upload
                            </button>
                            <button type="button" onclick="cancelUpload()"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">Cancel</button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- Files List with Thumbnails --}}
                <div class="divide-y divide-gray-100">
                    @forelse($activity->files as $file)
                        <div class="px-6 py-4 flex items-start gap-4 hover:bg-gray-50">
                            {{-- Thumbnail/Icon --}}
                            <div class="shrink-0">
                                @if($file->is_image)
                                    <a href="{{ route('activities.files.view', [$activity, $file]) }}" target="_blank" class="block">
                                        <img src="{{ route('activities.files.view', [$activity, $file]) }}" 
                                             alt="{{ $file->original_filename }}"
                                             class="w-16 h-16 object-cover rounded-lg border border-gray-200 hover:border-blue-400 transition-colors cursor-pointer">
                                    </a>
                                @elseif($file->is_pdf)
                                    <a href="{{ route('activities.files.view', [$activity, $file]) }}" target="_blank" 
                                       class="w-16 h-16 flex items-center justify-center rounded-lg bg-red-50 border border-red-200 hover:border-red-400 transition-colors">
                                        <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                @else
                                    <div class="w-16 h-16 flex items-center justify-center rounded-lg bg-gray-100 border border-gray-200">
                                        @if(in_array($file->mime_type, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']))
                                            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                        @elseif(in_array($file->mime_type, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']))
                                            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- File Info --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $file->original_filename }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $file->human_file_size }} · Uploaded by {{ $file->uploader->name }} · {{ $file->created_at->diffForHumans() }}
                                </p>
                                @if($file->description)
                                    <p class="text-xs text-gray-400 mt-1">{{ $file->description }}</p>
                                @endif
                                
                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-3 mt-2">
                                    @if($file->is_image || $file->is_pdf)
                                        <a href="{{ route('activities.files.view', [$activity, $file]) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View
                                        </a>
                                    @endif
                                    <a href="{{ route('activities.files.download', [$activity, $file]) }}"
                                       class="inline-flex items-center gap-1 text-xs text-gray-600 hover:text-gray-800 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Download
                                    </a>
                                    @if($file->uploaded_by === $user->id || $user->canManageDivision())
                                        <form method="POST" action="{{ route('activities.files.delete', [$activity, $file]) }}" onsubmit="return confirm('Delete this file?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No files attached yet.</p>
                            @if($canUploadFiles)
                                <button type="button" onclick="document.getElementById('file_upload_section').classList.remove('hidden')"
                                        class="mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium">Add the first file</button>
                            @endif
                        </div>
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

// File Upload Preview & Progress
let selectedFiles = [];

function previewFiles(input, append = true) {
    const newFiles = Array.from(input.files);
    
    if (append && newFiles.length > 0) {
        // Add new files to existing selection (avoid duplicates by name)
        newFiles.forEach(file => {
            if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                selectedFiles.push(file);
            }
        });
    } else if (!append) {
        selectedFiles = newFiles;
    }
    
    // Update the file input with all accumulated files
    updateFileInput();
    renderPreviews();
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateFileInput();
    renderPreviews();
}

function updateFileInput() {
    const fileInput = document.getElementById('file_input');
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
}

function renderPreviews() {
    const previewArea = document.getElementById('file_preview_area');
    const previewList = document.getElementById('file_preview_list');
    
    if (selectedFiles.length === 0) {
        previewArea.classList.add('hidden');
        previewList.innerHTML = '';
        return;
    }
    
    previewArea.classList.remove('hidden');
    previewList.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const card = document.createElement('div');
        card.className = 'relative bg-white border border-gray-200 rounded-lg overflow-hidden group';
        
        const previewContent = document.createElement('div');
        previewContent.className = 'h-24 flex items-center justify-center bg-gray-50';
        
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.className = 'w-full h-24 object-cover';
            const reader = new FileReader();
            reader.onload = (e) => { img.src = e.target.result; };
            reader.readAsDataURL(file);
            previewContent.appendChild(img);
        } else if (file.type === 'application/pdf') {
            previewContent.innerHTML = '<svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>';
        } else if (file.name.match(/\.(doc|docx)$/i)) {
            previewContent.innerHTML = '<svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>';
        } else if (file.name.match(/\.(xls|xlsx)$/i)) {
            previewContent.innerHTML = '<svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>';
        } else {
            previewContent.innerHTML = '<svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>';
        }
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600';
        removeBtn.innerHTML = '×';
        removeBtn.onclick = () => removeFile(index);
        
        const info = document.createElement('div');
        info.className = 'p-2';
        info.innerHTML = `
            <p class="text-xs font-medium text-gray-700 truncate" title="${file.name}">${file.name}</p>
            <p class="text-xs text-gray-400">${formatFileSize(file.size)}</p>
        `;
        
        card.appendChild(previewContent);
        card.appendChild(removeBtn);
        card.appendChild(info);
        previewList.appendChild(card);
    });
    
    // Update file count badge
    document.getElementById('file_count_badge').textContent = selectedFiles.length;
}

function clearAllFiles() {
    selectedFiles = [];
    updateFileInput();
    renderPreviews();
}

function cancelUpload() {
    const fileInput = document.getElementById('file_input');
    const previewArea = document.getElementById('file_preview_area');
    const uploadSection = document.getElementById('file_upload_section');
    
    fileInput.value = '';
    selectedFiles = [];
    previewArea.classList.add('hidden');
    document.getElementById('file_preview_list').innerHTML = '';
    document.getElementById('file_description').value = '';
    uploadSection.classList.add('hidden');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Drag and Drop
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop_zone');
    const fileInput = document.getElementById('file_input');
    
    if (!dropZone) return;
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        }, false);
    });
    
    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const droppedFiles = Array.from(dt.files);
        
        // Accumulate dropped files with existing selection
        droppedFiles.forEach(file => {
            if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                selectedFiles.push(file);
            }
        });
        
        updateFileInput();
        renderPreviews();
    }, false);
    
    // AJAX Upload with Progress
    const form = document.getElementById('file_upload_form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (selectedFiles.length === 0) {
                alert('Please select at least one file to upload.');
                return;
            }
            
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('description', document.getElementById('file_description').value || '');
            
            // Add all accumulated files
            selectedFiles.forEach(file => {
                formData.append('files[]', file);
            });
            
            const xhr = new XMLHttpRequest();
            const progressArea = document.getElementById('upload_progress_area');
            const progressBar = document.getElementById('upload_progress_bar');
            const progressText = document.getElementById('upload_progress_text');
            const uploadBtn = document.getElementById('upload_btn');
            const previewArea = document.getElementById('file_preview_area');
            const statusText = document.getElementById('upload_status_text');
            const fileCount = selectedFiles.length;
            const totalSize = selectedFiles.reduce((sum, f) => sum + f.size, 0);
            
            // Show progress immediately
            progressArea.classList.remove('hidden');
            previewArea.classList.add('hidden');
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
            statusText.textContent = `Uploading ${fileCount} file${fileCount > 1 ? 's' : ''} (${formatFileSize(totalSize)})...`;
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Uploading...';
            uploadBtn.classList.add('opacity-75');
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    progressText.textContent = percentComplete + '%';
                    statusText.textContent = `Uploading: ${formatFileSize(e.loaded)} of ${formatFileSize(e.total)}`;
                }
            });
            
            xhr.addEventListener('load', function() {
                if (xhr.status === 200 || xhr.status === 302) {
                    progressBar.style.width = '100%';
                    progressText.textContent = '100%';
                    statusText.textContent = 'Upload complete! Refreshing...';
                    progressBar.classList.remove('bg-blue-600');
                    progressBar.classList.add('bg-green-500');
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    alert('Upload failed. Please try again.');
                    resetUploadUI();
                }
            });
            
            xhr.addEventListener('error', function() {
                alert('Upload failed. Please check your connection and try again.');
                resetUploadUI();
            });
            
            function resetUploadUI() {
                progressArea.classList.add('hidden');
                previewArea.classList.remove('hidden');
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Upload';
                uploadBtn.classList.remove('opacity-75');
                progressBar.classList.remove('bg-green-500');
                progressBar.classList.add('bg-blue-600');
            }
            
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(formData);
        });
    }
});
</script>
@endsection
