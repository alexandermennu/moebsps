@extends('layouts.app')

@section('title', 'Edit Weekly Plan')
@section('page-title', 'Edit Weekly Plan')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('weekly-plans.show', $weeklyPlan) }}" class="text-xs text-blue-700 hover:underline">Back to Plan</a>
    </div>

    <form method="POST" action="{{ route('weekly-plans.update', $weeklyPlan) }}" id="weeklyPlanForm">
        @csrf
        @method('PUT')

        {{-- Header Section --}}
        <div class="bg-white border border-gray-200 p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-6">Edit Weekly Plan</h2>
            <p class="text-sm text-gray-500 mb-6">{{ $user->division?->name }} · Submitted by {{ $user->name }} ({{ $user->role_label }})</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Week Selector --}}
                <div class="md:col-span-2">
                    <label for="week_select" class="block text-sm font-medium text-gray-700 mb-1">Select Week</label>
                    <select id="week_select" onchange="updateWeekDates()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        @php
                            $upcomingWeeks = \App\Models\WeeklyPlan::getUpcomingWeeks();
                            $selectedStart = old('week_start', $weeklyPlan->week_start->format('Y-m-d'));
                            
                            // Also include the plan's current week if it's not in the upcoming weeks
                            $planWeekStart = $weeklyPlan->week_start->format('Y-m-d');
                            $planWeekEnd = $weeklyPlan->week_end->format('Y-m-d');
                            $planInList = collect($upcomingWeeks)->contains('start_formatted', $planWeekStart);
                        @endphp
                        
                        @if(!$planInList)
                            <optgroup label="Current Plan Week">
                                <option value="{{ $planWeekStart }}|{{ $planWeekEnd }}" 
                                    {{ $selectedStart == $planWeekStart ? 'selected' : '' }}>
                                    {{ $weeklyPlan->week_label }} ({{ $weeklyPlan->week_start->format('M d') }} - {{ $weeklyPlan->week_end->format('M d') }})
                                </option>
                            </optgroup>
                        @endif
                        
                        <optgroup label="Upcoming Weeks">
                            @foreach($upcomingWeeks as $week)
                                <option value="{{ $week['start_formatted'] }}|{{ $week['end_formatted'] }}"
                                    {{ $selectedStart == $week['start_formatted'] ? 'selected' : '' }}>
                                    {{ $week['label'] }} ({{ $week['start']->format('M d') }} - {{ $week['end']->format('M d') }})
                                    @if($week['is_next_week']) — Next Week @endif
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Working days: Monday to Friday</p>
                </div>

                {{-- Hidden fields for actual dates --}}
                <input type="hidden" name="week_start" id="week_start" value="{{ old('week_start', $weeklyPlan->week_start->format('Y-m-d')) }}">
                <input type="hidden" name="week_end" id="week_end" value="{{ old('week_end', $weeklyPlan->week_end->format('Y-m-d')) }}">
            </div>

            <script>
                function updateWeekDates() {
                    const select = document.getElementById('week_select');
                    const [start, end] = select.value.split('|');
                    document.getElementById('week_start').value = start;
                    document.getElementById('week_end').value = end;
                }
                // Initialize on page load
                document.addEventListener('DOMContentLoaded', updateWeekDates);
            </script>
        </div>

        {{-- Activities Table --}}
        <div class="bg-white border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Planned Activities</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Add each activity planned for the coming week</p>
                </div>
                <button type="button" onclick="addActivityRow()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-800 text-white text-xs font-medium hover:bg-gray-700">
                    + Add Row
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="activitiesTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-10">No.</th>
                            <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 320px;">Activities *</th>
                            <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 180px;">Responsible Persons</th>
                            <th class="text-left px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium" style="min-width: 200px;">Status / Comment</th>
                            <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-20">Track This</th>
                            <th class="text-center px-4 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium w-16"></th>
                        </tr>
                    </thead>
                    <tbody id="activitiesBody" class="divide-y divide-gray-100">
                        {{-- Rows populated by JS --}}
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                <button type="button" onclick="addActivityRow()"
                        class="text-sm text-slate-600 hover:text-slate-800 font-medium">+ Add another activity</button>
            </div>
        </div>

        {{-- Additional Notes (optional) --}}
        <div class="bg-white border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Additional Notes (Optional)</h3>

            <div class="mb-4">
                <label for="objectives" class="block text-sm font-medium text-gray-700 mb-1">Objectives</label>
                <textarea name="objectives" id="objectives" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="What are the key objectives for this week?">{{ old('objectives', $weeklyPlan->objectives) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="expected_outcomes" class="block text-sm font-medium text-gray-700 mb-1">Expected Outcomes</label>
                <textarea name="expected_outcomes" id="expected_outcomes" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="What outcomes do you expect to achieve?">{{ old('expected_outcomes', $weeklyPlan->expected_outcomes) }}</textarea>
            </div>

            <div>
                <label for="resources_needed" class="block text-sm font-medium text-gray-700 mb-1">Resources Needed</label>
                <textarea name="resources_needed" id="resources_needed" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="Any resources or support needed?">{{ old('resources_needed', $weeklyPlan->resources_needed) }}</textarea>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200">
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
            <button type="submit" name="status" value="submitted" class="px-5 py-2.5 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">
                Submit for Review
            </button>
            <button type="submit" name="status" value="draft" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">
                Save as Draft
            </button>
        </div>
    </form>
</div>

<script>
    let rowCount = 0;

    function addActivityRow(data = {}) {
        rowCount++;
        const tbody = document.getElementById('activitiesBody');
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.id = `activity-row-${rowCount}`;

        const activity = data.activity || '';
        const responsible = data.responsible_persons || '';
        const statusComment = data.status_comment || '';
        const trackThis = data.track_this ? 'checked' : '';

        row.innerHTML = `
            <td class="px-4 py-3 text-gray-400 font-medium text-center align-top row-number">${rowCount}</td>
            <td class="px-4 py-2 align-top">
                <textarea name="activities[${rowCount}][activity]" rows="3" required
                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500 resize-y"
                    placeholder="Describe the planned activity...">${activity}</textarea>
            </td>
            <td class="px-4 py-2 align-top">
                <input type="text" name="activities[${rowCount}][responsible_persons]" value="${responsible}"
                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500"
                    placeholder="e.g. SHD / SPS">
            </td>
            <td class="px-4 py-2 align-top">
                <textarea name="activities[${rowCount}][status_comment]" rows="2"
                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-slate-500 resize-y"
                    placeholder="Status or comments...">${statusComment}</textarea>
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

    // Initialize with old form data or existing plan activities
    document.addEventListener('DOMContentLoaded', function() {
        @if(old('activities'))
            @foreach(old('activities') as $i => $act)
                addActivityRow({
                    activity: @json($act['activity'] ?? ''),
                    responsible_persons: @json($act['responsible_persons'] ?? ''),
                    status_comment: @json($act['status_comment'] ?? ''),
                    track_this: @json(!empty($act['track_this'])),
                });
            @endforeach
        @elseif($weeklyPlan->activities->count())
            @foreach($weeklyPlan->activities as $activity)
                addActivityRow({
                    activity: @json($activity->activity),
                    responsible_persons: @json($activity->responsible_persons ?? ''),
                    status_comment: @json($activity->status_comment ?? ''),
                    track_this: @json((bool) $activity->track_this),
                });
            @endforeach
        @else
            addActivityRow();
        @endif
    });
</script>
@endsection
