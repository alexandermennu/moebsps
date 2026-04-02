@extends('layouts.app')

@section('title', 'Weekly Updates')
@section('page-title', 'Weekly Updates')

@section('content')
<div class="space-y-5">
    {{-- Header Section --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                Reporting Week: {{ $reportingWeekLabel }} 
                <span class="font-normal text-gray-500">({{ $reportingWeekStart->format('M d') }} – {{ $reportingWeekEnd->format('M d') }})</span>
            </h2>
            <p class="text-sm text-gray-600 mt-0.5">
                {{ $submittedCount }}/{{ $allDivisions->count() }} divisions submitted
                @if($lateCount > 0) | <span class="text-orange-600">{{ $lateCount }} late</span>@endif
                @if($overdueCount > 0) | <span class="text-red-600">{{ $overdueCount }} not submitted</span>@endif
                <span class="text-gray-400 ml-2">·</span>
                <span class="text-gray-500 ml-2">Due: {{ $dueDate->format('l, M d') }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($user->canManageDivision() && !$user->hasFullAccess())
                <a href="{{ route('weekly-updates.create', ['week_start' => $reportingWeekStart->toDateString()]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium hover:bg-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Report
                </a>
            @endif
            @if($user->hasFullAccess())
                <a href="{{ route('weekly-updates.create', ['week_start' => $reportingWeekStart->toDateString()]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium hover:bg-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Report
                </a>
                @if($notSubmittedCount > 0)
                    <form action="{{ route('weekly-updates.send-reminder') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50" onclick="return confirm('Send reminder to {{ $notSubmittedCount }} division(s) that have not submitted?')">
                            Send Reminder
                        </button>
                    </form>
                @else
                    <button type="button" class="px-4 py-2 bg-gray-100 border border-gray-200 text-gray-400 text-sm font-medium cursor-not-allowed" disabled>
                        Send Reminder
                    </button>
                @endif
                <a href="{{ route('weekly-updates.consolidated') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                    View Consolidated Report
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
            @endif
        </div>
    </div>

    {{-- Filter/Search Section --}}
    <div class="bg-white border border-gray-200 p-4">
        <form method="GET" action="{{ route('weekly-updates.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Month Picker --}}
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Month</label>
                <input type="month" name="month" value="{{ request('month', $reportingWeekStart->format('Y-m')) }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            {{-- Week Dropdown --}}
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Week</label>
                <select name="week" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    @php
                        $selectedMonth = request('month', $reportingWeekStart->format('Y-m'));
                        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
                        $weeksInMonth = \App\Models\WeeklyUpdate::getWeeksInMonth($monthDate->year, $monthDate->month);
                    @endphp
                    <option value="">All Weeks</option>
                    @foreach($weeksInMonth as $week)
                        <option value="{{ $week['start_formatted'] }}" {{ request('week') == $week['start_formatted'] ? 'selected' : '' }}>
                            {{ $week['label'] }} ({{ $week['start']->format('M d') }} - {{ $week['end']->format('M d') }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Monday to Friday working days</p>
            </div>

            {{-- Division Filter --}}
            @if($user->hasFullAccess())
            <div>
                <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Division</label>
                <select name="division" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <option value="">All Divisions</option>
                    @foreach($allDivisionsForDropdown as $division)
                        <option value="{{ $division->id }}" {{ request('division') == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Search --}}
            <div class="{{ $user->hasFullAccess() ? '' : 'md:col-span-2' }}">
                <label class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Search</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by division or submitter..."
                            class="w-full px-3 py-2 pl-9 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700">
                        Search
                    </button>
                    @if(request('search') || request('week') || request('division') || (request('month') && request('month') != $reportingWeekStart->format('Y-m')))
                    <a href="{{ route('weekly-updates.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50" title="Reset all filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Status Summary Pills --}}
    <div class="flex items-center gap-5">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-green-500"></span>
            <span class="text-sm text-gray-700">{{ $onTimeCount }} On Time</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-orange-500"></span>
            <span class="text-sm text-gray-700">{{ $lateCount }} Late</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            <span class="text-sm text-gray-700">{{ $notSubmittedCount }} Not Submitted</span>
        </div>
    </div>

    {{-- Main Division Table --}}
    <div class="bg-white border border-gray-200">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs text-gray-500 uppercase tracking-wide font-medium">Division</th>
                    <th class="text-left px-4 py-3 text-xs text-gray-500 uppercase tracking-wide font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-xs text-gray-500 uppercase tracking-wide font-medium">Submitted</th>
                    <th class="text-left px-4 py-3 text-xs text-gray-500 uppercase tracking-wide font-medium">Content</th>
                    <th class="text-center px-4 py-3 text-xs text-gray-500 uppercase tracking-wide font-medium">Report Status</th>
                    <th class="text-center px-4 py-3 text-xs text-gray-500 uppercase tracking-wide font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($divisionStatuses as $divStatus)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-900">{{ $divStatus->division->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($divStatus->status_color === 'green')
                                <span class="inline-flex items-center gap-1.5 text-green-600 font-medium text-sm">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    {{ $divStatus->status_label }}
                                </span>
                            @elseif($divStatus->status_color === 'orange')
                                <span class="inline-flex items-center gap-1.5 text-orange-600 font-medium text-sm">
                                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                    {{ $divStatus->status_label }}
                                </span>
                            @elseif($divStatus->status_color === 'red')
                                <span class="inline-flex items-center gap-1.5 text-red-600 font-medium text-sm">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    {{ $divStatus->status_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-gray-500 font-medium text-sm">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    {{ $divStatus->status_label }}
                                </span>
                            @endif
                            <p class="text-xs text-gray-500">{{ $divStatus->status_detail }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-sm">
                            @if($divStatus->update)
                                {{ $divStatus->update->created_at->format('M d, Y') }}
                                <p class="text-xs text-gray-400">{{ $divStatus->update->created_at->format('g:i A') }}</p>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-sm">
                            @if($divStatus->has_content)
                                {{ $divStatus->activity_count }} {{ Str::plural('activity', $divStatus->activity_count) }}
                            @else
                                <span class="text-gray-400">No data</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($divStatus->update)
                                @php
                                    $reportStatus = $divStatus->update->status;
                                    $statusStyles = match($reportStatus) {
                                        'approved' => 'bg-green-100 text-green-700',
                                        'submitted' => 'bg-blue-100 text-blue-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'draft' => 'bg-gray-100 text-gray-600',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusLabel = match($reportStatus) {
                                        'approved' => 'Approved',
                                        'submitted' => 'Under Review',
                                        'rejected' => 'Rejected',
                                        'draft' => 'Draft',
                                        default => ucfirst($reportStatus),
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $statusStyles }}">
                                    {{ $statusLabel }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($divStatus->update)
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('weekly-updates.show', $divStatus->update) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium hover:bg-blue-700">
                                        View
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    @if($user->hasFullAccess() || ($user->canManageDivision() && $divStatus->update->submitted_by === $user->id && in_array($divStatus->update->status, ['draft', 'rejected'])))
                                    <form action="{{ route('weekly-updates.destroy', $divStatus->update) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2 py-1.5 bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 border border-red-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            @elseif($user->hasFullAccess())
                                <form action="{{ route('weekly-updates.request-submission', $divStatus->division) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-50 text-orange-700 text-xs font-medium hover:bg-orange-100 border border-orange-200">
                                        Request Submission
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </form>
                            @elseif($user->canManageDivision() && $user->division_id == $divStatus->division->id)
                                <a href="{{ route('weekly-updates.create', ['week_start' => $reportingWeekStart->toDateString()]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-xs font-medium hover:bg-green-700">
                                    Submit Update
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">No divisions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Division Summary Cards --}}
    <div class="grid grid-cols-5 gap-3">
        @foreach($divisionStatuses as $divStatus)
            @php
                $bgColor = match($divStatus->status_color) {
                    'green' => 'bg-green-50 border-green-200',
                    'orange' => 'bg-orange-50 border-orange-200',
                    'red' => 'bg-red-50 border-red-200',
                    default => 'bg-gray-50 border-gray-200',
                };
                $textColor = match($divStatus->status_color) {
                    'green' => 'text-green-700',
                    'orange' => 'text-orange-700',
                    'red' => 'text-red-700',
                    default => 'text-gray-700',
                };
                $dotColor = match($divStatus->status_color) {
                    'green' => 'bg-green-500',
                    'orange' => 'bg-orange-500',
                    'red' => 'bg-red-500',
                    default => 'bg-gray-400',
                };
            @endphp
            <div class="border {{ $bgColor }} p-3.5">
                <div class="flex items-center justify-between mb-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
                    <span class="text-xs font-semibold {{ $textColor }}">{{ $divStatus->status_label }}</span>
                </div>
                <h4 class="text-sm font-semibold text-gray-900 leading-tight">{{ $divStatus->division->name }}</h4>
                <p class="text-xs text-gray-500 mt-1.5">{{ $divStatus->has_content ? $divStatus->activity_count . ' ' . Str::plural('activity', $divStatus->activity_count) : 'No activities reported' }}</p>
                @if($divStatus->update)
                    <a href="{{ route('weekly-updates.show', $divStatus->update) }}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline mt-2">
                        View Report
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <p class="text-xs {{ $textColor }} mt-2">{{ $divStatus->status_detail }}</p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Past Reports Section --}}
    @if($previousWeeksGrouped->count() > 0)
    <div>
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-3">Past Reports</h3>
        
        @php
            $groupedByMonth = $previousWeeksGrouped->groupBy(function($week) {
                return $week->week_start->format('F Y');
            });
        @endphp
        
        <div class="grid grid-cols-4 gap-3">
            @foreach($groupedByMonth as $monthLabel => $weeks)
                @php
                    $totalSubmissions = $weeks->sum('submitted_count');
                    $totalPossible = $weeks->sum('total_divisions');
                    $hasData = $totalSubmissions > 0;
                    $firstWeek = $weeks->first();
                @endphp
                <div class="bg-white border border-gray-200 p-3.5 hover:shadow-sm transition-shadow">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-base font-semibold text-gray-900">{{ $monthLabel }}</h4>
                        @if($hasData)
                            <a href="{{ route('weekly-updates.index', ['month' => $firstWeek->week_start->format('Y-m')]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 text-white text-xs font-medium hover:bg-blue-700 whitespace-nowrap">
                                View Details
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <button type="button" onclick="showNoDataAlert('{{ $monthLabel }}')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-400 text-white text-xs font-medium hover:bg-gray-500 whitespace-nowrap cursor-pointer">
                                View Details
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        @endif
                    </div>
                    <div class="mt-2.5 flex items-center gap-1.5 text-sm text-gray-500">
                        @if($hasData)
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ $totalSubmissions }} {{ Str::plural('submission', $totalSubmissions) }}</span>
                        @else
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            <span>No submissions yet</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-3 text-center">
            <a href="{{ route('weekly-updates.index', ['view' => 'all']) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium text-sm">
                View All Reports
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
    @endif
</div>

{{-- No Data Alert Modal --}}
<div id="noDataModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl border border-gray-200 max-w-sm w-full mx-4 overflow-hidden">
        <div class="bg-amber-50 px-5 py-3 border-b border-amber-200">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-sm font-semibold text-amber-800">No Reports Available</h3>
            </div>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-600" id="noDataMessage"></p>
        </div>
        <div class="bg-gray-50 px-5 py-3 flex justify-end">
            <button type="button" onclick="closeNoDataModal()" class="px-3 py-1.5 bg-gray-800 text-white text-xs font-medium rounded hover:bg-gray-700">
                OK
            </button>
        </div>
    </div>
</div>

<script>
    function showNoDataAlert(monthLabel) {
        const modal = document.getElementById('noDataModal');
        const message = document.getElementById('noDataMessage');
        
        message.textContent = `No submissions found for ${monthLabel}.`;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeNoDataModal() {
        const modal = document.getElementById('noDataModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNoDataModal();
        }
    });

    // Auto-submit form when month changes (to refresh week options)
    document.addEventListener('DOMContentLoaded', function() {
        const monthInput = document.querySelector('input[name="month"]');
        const weekSelect = document.querySelector('select[name="week"]');
        const divisionSelect = document.querySelector('select[name="division"]');
        
        if (monthInput) {
            monthInput.addEventListener('change', function() {
                // Clear the week selection when month changes
                if (weekSelect) {
                    weekSelect.value = '';
                }
                this.form.submit();
            });
        }
        
        // Auto-submit when week or division changes
        if (weekSelect) {
            weekSelect.addEventListener('change', function() {
                this.form.submit();
            });
        }
        
        if (divisionSelect) {
            divisionSelect.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
</script>
@endsection
