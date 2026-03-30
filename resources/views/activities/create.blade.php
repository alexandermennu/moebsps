@extends('layouts.app')

@section('title', 'New Assignment')
@section('page-title', 'Create Assignment')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('activities.index') }}" class="text-xs text-blue-700 hover:underline">Back to Assignments</a>
    </div>

    <div class="bg-white border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-6">Create New Assignment</h2>

        <form method="POST" action="{{ route('activities.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500"
                       placeholder="Assignment title">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500"
                          placeholder="Detailed description of the assignment...">{{ old('description') }}</textarea>
            </div>

            @if($user->isDirector())
                {{-- Directors use their own division --}}
                <input type="hidden" name="division_id" value="{{ $user->division_id }}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                    <p class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-600">
                        {{ $user->division?->name }}
                    </p>
                </div>
            @else
                {{-- Minister's Office staff and others can select any division --}}
                <div class="mb-4" id="division_wrapper">
                    <label for="division_id" id="division_label" class="block text-sm font-medium text-gray-700 mb-1">Division *</label>
                    <select name="division_id" id="division_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                        <option value="">Select Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="mb-4">
                <label for="assignee_select" class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                <select id="assignee_select" onchange="handleAssigneeChange(this)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                    <option value="">Unassigned</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" data-division="{{ $u->division_id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role_label }})</option>
                    @endforeach
                    @if($canAssignCounselor && $counselors->count() > 0)
                        <option value="__counselor__" data-division="__counselor__" {{ old('assigned_to') && $counselors->pluck('id')->contains(old('assigned_to')) ? 'selected' : '' }}>A Counselor ({{ $counselors->count() }} available)</option>
                    @endif
                </select>
                <input type="hidden" name="assigned_to" id="assigned_to_hidden" value="{{ old('assigned_to') }}">
                <p id="division_filter_hint" class="text-xs text-gray-400 mt-1 hidden">Showing staff from the selected division</p>
            </div>

            @if($canAssignCounselor && $counselors->count() > 0)
            <div id="counselor_dropdown_wrapper" class="mb-4 hidden">
                <label for="counselor_select" class="block text-sm font-medium text-gray-700 mb-1">Select Counselor</label>
                <select id="counselor_select" onchange="handleCounselorChange(this)"
                        class="w-full px-3 py-2 border border-blue-400 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-blue-50">
                    <option value="">— Choose a counselor —</option>
                    @foreach($counselors as $c)
                        <option value="{{ $c->id }}" {{ old('assigned_to') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                            @if($c->counselor_county) — {{ $c->counselor_county }} @endif
                            @if($c->counselor_school) ({{ $c->counselor_school }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                    <select name="priority" id="priority" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                </div>
            </div>

            <div class="mb-4">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
            </div>

            <div class="mb-6">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" id="remarks" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500"
                          placeholder="Any additional notes...">{{ old('remarks') }}</textarea>
            </div>

            {{-- File Attachments --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Attach Files (Optional)</label>
                <div id="drop_zone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer"
                     onclick="document.getElementById('file_input').click()">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">Click to select files or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">Max 10MB per file. PDF, Word, Excel, Images, etc.</p>
                </div>
                <input type="file" name="files[]" id="file_input" multiple
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.txt,.csv,.zip"
                       class="hidden" onchange="previewFiles(this)">
                
                {{-- File Preview Area --}}
                <div id="file_preview_area" class="hidden mt-3">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700">Selected Files <span id="file_count_badge" class="ml-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">0</span></label>
                        <button type="button" onclick="clearAllFiles()" class="text-xs text-red-600 hover:text-red-800 font-medium">Clear All</button>
                    </div>
                    <div id="file_preview_list" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                    <p class="text-xs text-gray-500 mt-2">Click the drop area to add more files. Click × on a file to remove it.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" id="submit_btn" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">Create Assignment</button>
                <a href="{{ route('activities.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
function handleAssigneeChange(select) {
    const wrapper = document.getElementById('counselor_dropdown_wrapper');
    const hiddenInput = document.getElementById('assigned_to_hidden');
    const counselorSelect = document.getElementById('counselor_select');

    if (select.value === '__counselor__') {
        if (wrapper) wrapper.classList.remove('hidden');
        hiddenInput.value = counselorSelect ? counselorSelect.value : '';
    } else {
        if (wrapper) wrapper.classList.add('hidden');
        hiddenInput.value = select.value;
        if (counselorSelect) counselorSelect.value = '';
    }
}

function handleCounselorChange(select) {
    document.getElementById('assigned_to_hidden').value = select.value;
}

function filterAssigneesByDivision(divisionId) {
    const assigneeSelect = document.getElementById('assignee_select');
    const hiddenInput = document.getElementById('assigned_to_hidden');
    const hint = document.getElementById('division_filter_hint');
    
    if (!assigneeSelect) return;
    
    const options = assigneeSelect.querySelectorAll('option');
    let hasVisibleOptions = false;
    
    options.forEach(option => {
        // Always show "Unassigned" and "A Counselor" options
        if (option.value === '' || option.value === '__counselor__') {
            option.style.display = '';
            return;
        }
        
        const optionDivision = option.getAttribute('data-division');
        
        if (!divisionId || optionDivision == divisionId) {
            option.style.display = '';
            hasVisibleOptions = true;
        } else {
            option.style.display = 'none';
            // If currently selected option is now hidden, reset selection
            if (option.selected) {
                assigneeSelect.value = '';
                hiddenInput.value = '';
            }
        }
    });
    
    // Show/hide hint
    if (hint) {
        hint.classList.toggle('hidden', !divisionId);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const assigneeSelect = document.getElementById('assignee_select');
    const hiddenInput = document.getElementById('assigned_to_hidden');
    const divisionSelect = document.getElementById('division_id');

    // Set up division change listener for filtering assignees
    if (divisionSelect) {
        divisionSelect.addEventListener('change', function() {
            filterAssigneesByDivision(this.value);
        });
        // Apply filter on page load if division is pre-selected
        if (divisionSelect.value) {
            filterAssigneesByDivision(divisionSelect.value);
        }
    }

    // Handle form submission - ensure hidden input is synced
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (assigneeSelect && assigneeSelect.value && assigneeSelect.value !== '__counselor__') {
                hiddenInput.value = assigneeSelect.value;
            }
        });
    }

    // Handle initial assignee selection state
    if (assigneeSelect && assigneeSelect.value === '__counselor__') {
        const wrapper = document.getElementById('counselor_dropdown_wrapper');
        if (wrapper) wrapper.classList.remove('hidden');
        const counselorSelect = document.getElementById('counselor_select');
        if (counselorSelect) hiddenInput.value = counselorSelect.value;
    } else if (assigneeSelect) {
        hiddenInput.value = assigneeSelect.value;
    }
    
    // File upload drag and drop
    const dropZone = document.getElementById('drop_zone');
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });
        
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
            const droppedFiles = Array.from(e.dataTransfer.files);
            droppedFiles.forEach(file => {
                if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    selectedFiles.push(file);
                }
            });
            updateFileInput();
            renderPreviews();
        }, false);
    }
});

// File upload functionality
let selectedFiles = [];

function previewFiles(input) {
    const newFiles = Array.from(input.files);
    newFiles.forEach(file => {
        if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
            selectedFiles.push(file);
        }
    });
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
    
    document.getElementById('file_count_badge').textContent = selectedFiles.length;
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateFileInput();
    renderPreviews();
}

function clearAllFiles() {
    selectedFiles = [];
    updateFileInput();
    renderPreviews();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endsection