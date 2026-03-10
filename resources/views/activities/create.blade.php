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

        <form method="POST" action="{{ route('activities.store') }}">
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

            @if(!$user->isDirector())
                <div class="mb-4">
                    <label for="division_id" class="block text-sm font-medium text-gray-700 mb-1">Division *</label>
                    <select name="division_id" id="division_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                        <option value="">Select Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
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
                        <option value="{{ $u->id }}" data-division="{{ $u->division_id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role_label }})</option>
                    @endforeach
                    @if($canAssignCounselor && $counselors->count() > 0)
                        <option value="__counselor__" data-division="__counselor__" {{ old('assigned_to') && $counselors->pluck('id')->contains(old('assigned_to')) ? 'selected' : '' }}>A Counselor ({{ $counselors->count() }} available)</option>
                    @endif
                </select>
                <input type="hidden" name="assigned_to" id="assigned_to_hidden" value="{{ old('assigned_to') }}">
                @if(!$user->isDirector())
                    <p id="division_hint" class="text-xs text-gray-400 mt-1 hidden">Showing staff from the selected division</p>
                @endif
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
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
                </div>
            </div>

            <div class="mb-4">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500">
            </div>

            <div class="mb-6">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" id="remarks" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-500"
                          placeholder="Any additional notes...">{{ old('remarks') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">Create Assignment</button>
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

function filterStaffByDivision(divisionId) {
    const assigneeSelect = document.getElementById('assignee_select');
    const hiddenInput = document.getElementById('assigned_to_hidden');
    const hint = document.getElementById('division_hint');
    
    if (!assigneeSelect) return;
    
    const options = assigneeSelect.querySelectorAll('option');
    let hasVisibleOptions = false;
    let currentSelectionHidden = false;

    options.forEach(option => {
        // Always show empty and counselor options
        if (option.value === '' || option.value === '__counselor__') {
            option.hidden = false;
            return;
        }
        
        const optDivision = option.getAttribute('data-division');
        
        // If no division selected, show all
        // If division selected, only show matching staff
        if (!divisionId || optDivision == divisionId) {
            option.hidden = false;
            hasVisibleOptions = true;
        } else {
            option.hidden = true;
            if (option.selected) {
                currentSelectionHidden = true;
            }
        }
    });

    // Reset selection if current selection was hidden
    if (currentSelectionHidden) {
        assigneeSelect.value = '';
        hiddenInput.value = '';
        const wrapper = document.getElementById('counselor_dropdown_wrapper');
        if (wrapper) wrapper.classList.add('hidden');
    }

    // Show/hide hint
    if (hint) {
        hint.classList.toggle('hidden', !divisionId);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const assigneeSelect = document.getElementById('assignee_select');
    const hiddenInput = document.getElementById('assigned_to_hidden');
    const divisionSelect = document.getElementById('division_id');

    // Set up division change listener
    if (divisionSelect) {
        divisionSelect.addEventListener('change', function() {
            filterStaffByDivision(this.value);
        });
        // Filter on page load if division is pre-selected
        if (divisionSelect.value) {
            filterStaffByDivision(divisionSelect.value);
        }
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
});
</script>
@endsection
