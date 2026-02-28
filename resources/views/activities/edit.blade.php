@extends('layouts.app')

@section('title', 'Edit Assignment')
@section('page-title', 'Edit Assignment')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('activities.show', $activity) }}" class="text-xs text-blue-700 hover:underline">Back to Assignment</a>
    </div>

    <div class="bg-white border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-6">Edit Assignment</h2>

        <form method="POST" action="{{ route('activities.update', $activity) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $activity->title) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">{{ old('description', $activity->description) }}</textarea>
            </div>

            @if(!$user->isDirector())
                <div class="mb-4">
                    <label for="division_id" class="block text-sm font-medium text-gray-700 mb-1">Division *</label>
                    <select name="division_id" id="division_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('division_id', $activity->division_id) == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="division_id" value="{{ $user->division_id }}">
            @endif

            <div class="mb-4">
                <label for="assignee_select" class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                <select id="assignee_select" onchange="handleAssigneeChange(this)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                    <option value="">Unassigned</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" data-division="{{ $u->division_id }}" {{ old('assigned_to', $assigneeIsCounselor ? '' : $activity->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role_label }})</option>
                    @endforeach
                    @if($canAssignCounselor && $counselors->count() > 0)
                        <option value="__counselor__" data-division="__counselor__" {{ $assigneeIsCounselor ? 'selected' : '' }}>A Counselor ({{ $counselors->count() }} available)</option>
                    @endif
                </select>
                <input type="hidden" name="assigned_to" id="assigned_to_hidden" value="{{ old('assigned_to', $activity->assigned_to) }}">
                @if(!$user->isDirector())
                    <p id="division_hint" class="text-xs text-gray-400 mt-1 hidden">Showing staff from the selected division</p>
                @endif
            </div>

            @if($canAssignCounselor && $counselors->count() > 0)
            <div id="counselor_dropdown_wrapper" class="mb-4 {{ $assigneeIsCounselor ? '' : 'hidden' }}">
                <label for="counselor_select" class="block text-sm font-medium text-gray-700 mb-1">Select Counselor</label>
                <select id="counselor_select" onchange="handleCounselorChange(this)"
                        class="w-full px-3 py-2 border border-blue-400 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-blue-50">
                    <option value="">— Choose a counselor —</option>
                    @foreach($counselors as $c)
                        <option value="{{ $c->id }}" {{ old('assigned_to', $activity->assigned_to) == $c->id ? 'selected' : '' }}>
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
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                        <option value="not_started" {{ old('status', $activity->status) === 'not_started' ? 'selected' : '' }}>Not Started</option>
                        <option value="in_progress" {{ old('status', $activity->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $activity->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="overdue" {{ old('status', $activity->status) === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                    <select name="priority" id="priority" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                        <option value="low" {{ old('priority', $activity->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $activity->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $activity->priority) === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority', $activity->priority) === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $activity->start_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                </div>
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $activity->due_date->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                </div>
            </div>

            <div class="mb-4">
                <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-1">Progress ({{ old('progress_percentage', $activity->progress_percentage) }}%)</label>
                <input type="range" name="progress_percentage" id="progress_percentage" min="0" max="100" step="5"
                       value="{{ old('progress_percentage', $activity->progress_percentage) }}"
                       class="w-full h-2 bg-gray-200 appearance-none cursor-pointer"
                       oninput="this.previousElementSibling.textContent = 'Progress (' + this.value + '%)'">
            </div>

            <div class="mb-6">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" id="remarks" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">{{ old('remarks', $activity->remarks) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">Update Assignment</button>
                <a href="{{ route('activities.show', $activity) }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</a>
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

function handleDivisionChange(divisionId) {
    const assigneeSelect = document.getElementById('assignee_select');
    const hiddenInput = document.getElementById('assigned_to_hidden');
    const hint = document.getElementById('division_hint');
    const options = assigneeSelect.querySelectorAll('option');

    options.forEach(option => {
        if (option.value === '' || option.value === '__counselor__') {
            option.style.display = '';
            return;
        }
        const optDivision = option.getAttribute('data-division');
        if (!divisionId || optDivision == divisionId) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
            if (option.selected) {
                assigneeSelect.value = '';
                hiddenInput.value = '';
                const wrapper = document.getElementById('counselor_dropdown_wrapper');
                if (wrapper) wrapper.classList.add('hidden');
            }
        }
    });

    if (hint) {
        hint.classList.toggle('hidden', !divisionId);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const assigneeSelect = document.getElementById('assignee_select');
    const hiddenInput = document.getElementById('assigned_to_hidden');
    const divisionSelect = document.getElementById('division_id');

    if (divisionSelect) {
        divisionSelect.addEventListener('change', function() {
            handleDivisionChange(this.value);
        });
        if (divisionSelect.value) {
            handleDivisionChange(divisionSelect.value);
        }
    }

    if (assigneeSelect.value === '__counselor__') {
        const wrapper = document.getElementById('counselor_dropdown_wrapper');
        if (wrapper) wrapper.classList.remove('hidden');
        const counselorSelect = document.getElementById('counselor_select');
        if (counselorSelect) hiddenInput.value = counselorSelect.value;
    } else {
        hiddenInput.value = assigneeSelect.value;
    }
});
</script>
@endsection
