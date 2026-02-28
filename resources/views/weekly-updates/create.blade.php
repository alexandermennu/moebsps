@extends('layouts.app')

@section('title', 'New Weekly Update')
@section('page-title', 'Submit Weekly Update')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('weekly-updates.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Weekly Updates</a>
    </div>

    <form method="POST" action="{{ route('weekly-updates.store') }}" id="weeklyUpdateForm">
        @csrf

        {{-- Header Section --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">New Weekly Update</h2>
            <p class="text-sm text-gray-500 mb-6">{{ $user->division?->name }} · Submitted by {{ $user->name }} ({{ $user->role_label }})</p>

            {{-- Status Legend --}}
            <div class="mb-5 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs font-semibold text-gray-600 mb-2">Legend: Status</p>
                <div class="flex flex-wrap gap-4 text-xs">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Red = Not Started</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> Yellow = Ongoing</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Green = Completed</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span> N/A = Not Available</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="week_start" class="block text-sm font-medium text-gray-700 mb-1">Week Start</label>
                    <input type="date" name="week_start" id="week_start" value="{{ old('week_start', now()->startOfWeek()->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="week_end" class="block text-sm font-medium text-gray-700 mb-1">Week End</label>
                    <input type="date" name="week_end" id="week_end" value="{{ old('week_end', now()->endOfWeek()->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>
        </div>

        {{-- Activities Table --}}
        <div class="bg-white rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Activities / Tasks</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Add each activity or task performed during the week</p>
                </div>
                <button type="button" onclick="addActivityRow()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-md hover:bg-slate-700">
                    + Add Row
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="activitiesTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium w-10">No.</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium" style="min-width: 280px;">Activities/Task *</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium" style="min-width: 160px;">Responsible Persons</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium w-36">Status *</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium" style="min-width: 180px;">Status Comment</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-medium" style="min-width: 180px;">Challenges</th>
                            <th class="text-center px-4 py-3 text-gray-600 font-medium w-20">Track This</th>
                            <th class="text-center px-4 py-3 text-gray-600 font-medium w-16"></th>
                        </tr>
                    </thead>
                    <tbody id="activitiesBody" class="divide-y divide-gray-100">
                        {{-- Rows populated by JS --}}
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                <button type="button" onclick="addActivityRow()"
                        class="text-sm text-slate-600 hover:text-slate-800 font-medium">+ Add another activity</button>
            </div>
        </div>

        {{-- Additional Notes (optional) --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Additional Notes (Optional)</h3>

            <div class="mb-4">
                <label for="support_needed" class="block text-sm font-medium text-gray-700 mb-1">Support Needed</label>
                <textarea name="support_needed" id="support_needed" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="Any support or resources needed from the bureau...">{{ old('support_needed') }}</textarea>
            </div>

            <div>
                <label for="key_metrics" class="block text-sm font-medium text-gray-700 mb-1">Key Metrics</label>
                <textarea name="key_metrics" id="key_metrics" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="Key performance metrics or numbers for the week...">{{ old('key_metrics') }}</textarea>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Submit Buttons --}}
        <div class="flex gap-3">
            <button type="submit" name="status" value="submitted" class="px-5 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">
                Submit for Review
            </button>
            <button type="submit" name="status" value="draft" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Save as Draft
            </button>
        </div>
    </form>
</div>

<script>
    let rowCount = 0;

    const statusColors = {
        not_started: 'border-red-300 bg-red-50',
        ongoing: 'border-yellow-300 bg-yellow-50',
        completed: 'border-green-300 bg-green-50',
        na: 'border-gray-300 bg-gray-50',
    };

    function addActivityRow(data = {}) {
        rowCount++;
        const tbody = document.getElementById('activitiesBody');
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.id = `activity-row-${rowCount}`;

        const activity = data.activity || '';
        const responsible = data.responsible_persons || '';
        const statusFlag = data.status_flag || 'not_started';
        const statusComment = data.status_comment || '';
        const challenges = data.challenges || '';
        const trackThis = data.track_this ? 'checked' : '';

        row.innerHTML = `
            <td class="px-4 py-3 text-gray-400 font-medium text-center align-top row-number">${rowCount}</td>
            <td class="px-4 py-2 align-top">
                <textarea name="activities[${rowCount}][activity]" rows="3" required
                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500 resize-y"
                    placeholder="Describe the activity or task...">${activity}</textarea>
            </td>
            <td class="px-4 py-2 align-top">
                <input type="text" name="activities[${rowCount}][responsible_persons]" value="${responsible}"
                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500"
                    placeholder="e.g. SHD / SPS">
            </td>
            <td class="px-4 py-2 align-top">
                <select name="activities[${rowCount}][status_flag]" required
                    class="w-full px-2 py-1.5 border rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500 status-select ${statusColors[statusFlag] || ''}"
                    onchange="updateStatusColor(this)">
                    <option value="not_started" ${statusFlag === 'not_started' ? 'selected' : ''}>🔴 Not Started</option>
                    <option value="ongoing" ${statusFlag === 'ongoing' ? 'selected' : ''}>🟡 Ongoing</option>
                    <option value="completed" ${statusFlag === 'completed' ? 'selected' : ''}>🟢 Completed</option>
                    <option value="na" ${statusFlag === 'na' ? 'selected' : ''}>⚪ N/A</option>
                </select>
            </td>
            <td class="px-4 py-2 align-top">
                <textarea name="activities[${rowCount}][status_comment]" rows="2"
                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500 resize-y"
                    placeholder="Additional status details...">${statusComment}</textarea>
            </td>
            <td class="px-4 py-2 align-top">
                <textarea name="activities[${rowCount}][challenges]" rows="2"
                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500 resize-y"
                    placeholder="Any challenges...">${challenges}</textarea>
            </td>
            <td class="px-4 py-2 text-center align-top">
                <input type="hidden" name="activities[${rowCount}][track_this]" value="0">
                <input type="checkbox" name="activities[${rowCount}][track_this]" value="1" ${trackThis}
                    class="w-4 h-4 text-slate-600 border-gray-300 rounded focus:ring-slate-500 mt-1"
                    title="Track this activity">
            </td>
            <td class="px-4 py-2 text-center align-top">
                <button type="button" onclick="removeActivityRow(${rowCount})"
                    class="text-red-400 hover:text-red-600 text-lg" title="Remove row">✕</button>
            </td>
        `;

        tbody.appendChild(row);
        renumberRows();
    }

    function removeActivityRow(id) {
        const tbody = document.getElementById('activitiesBody');
        if (tbody.children.length <= 1) {
            alert('You must have at least one activity.');
            return;
        }
        const row = document.getElementById(`activity-row-${id}`);
        if (row) {
            row.remove();
            renumberRows();
        }
    }

    function renumberRows() {
        const rows = document.querySelectorAll('#activitiesBody tr');
        rows.forEach((row, index) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.textContent = index + 1;
        });
    }

    function updateStatusColor(select) {
        // Remove all status color classes
        select.classList.remove(
            'border-red-300', 'bg-red-50',
            'border-yellow-300', 'bg-yellow-50',
            'border-green-300', 'bg-green-50',
            'border-gray-300', 'bg-gray-50'
        );
        const colors = statusColors[select.value];
        if (colors) {
            colors.split(' ').forEach(cls => select.classList.add(cls));
        }
    }

    // Initialize with old form data or one empty row
    document.addEventListener('DOMContentLoaded', function() {
        @if(old('activities'))
            @foreach(old('activities') as $i => $act)
                addActivityRow({
                    activity: @json($act['activity'] ?? ''),
                    responsible_persons: @json($act['responsible_persons'] ?? ''),
                    status_flag: @json($act['status_flag'] ?? 'not_started'),
                    status_comment: @json($act['status_comment'] ?? ''),
                    challenges: @json($act['challenges'] ?? ''),
                    track_this: @json(!empty($act['track_this'])),
                });
            @endforeach
        @else
            addActivityRow();
        @endif
    });
</script>
@endsection
