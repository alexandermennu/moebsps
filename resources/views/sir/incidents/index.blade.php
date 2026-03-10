@extends('layouts.app')
@section('title', $module === 'srgbv' ? 'SRGBV Cases' : 'Other Incidents')
@section('page-title', $module === 'srgbv' ? 'SRGBV Cases' : 'Other Incidents')
@section('content')
@php
    $isSrgbv = $module === 'srgbv';
    $indexRoute = $isSrgbv ? 'sir.srgbv.cases.index' : 'sir.other.incidents.index';
    $createRoute = $isSrgbv ? 'sir.srgbv.cases.create' : 'sir.other.incidents.create';
    $showRoute = $isSrgbv ? 'sir.srgbv.cases.show' : 'sir.other.incidents.show';
    $editRoute = $isSrgbv ? 'sir.srgbv.cases.edit' : 'sir.other.incidents.edit';
    $destroyRoute = $isSrgbv ? 'sir.srgbv.cases.destroy' : 'sir.other.incidents.destroy';
    $exportRoute = $isSrgbv ? 'sir.srgbv.cases.export' : 'sir.other.incidents.export';
    $exportSingleRoute = $isSrgbv ? 'sir.srgbv.cases.export-single' : 'sir.other.incidents.export-single';
    $dashboardRoute = $isSrgbv ? 'sir.srgbv.dashboard' : 'sir.other.dashboard';
    $themeColor = $isSrgbv ? 'red' : 'blue';
@endphp
<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('sir.dashboard') }}" class="hover:text-gray-600">SIR</a>
                <span>›</span>
                <a href="{{ route($dashboardRoute) }}" class="hover:text-gray-600">{{ $isSrgbv ? 'SRGBV' : 'Other' }}</a>
                <span>›</span>
                <span class="text-gray-600">{{ $isSrgbv ? 'Cases' : 'Incidents' }}</span>
            </div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ $isSrgbv ? 'SRGBV Cases' : 'Other Incidents' }}</h2>
            <p class="text-sm text-gray-500">{{ $isSrgbv ? 'School-Related Gender-Based Violence reports.' : 'General school incident reports.' }}</p>
        </div>
        <div class="flex gap-2">
            {{-- Export Button --}}
            <a href="{{ route($exportRoute, array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </a>
            <a href="{{ route($dashboardRoute) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">Dashboard</a>
            @if($canManage)
            <a href="{{ route($createRoute) }}" class="px-4 py-2 bg-{{ $themeColor }}-700 text-white text-sm font-medium hover:bg-{{ $themeColor }}-800 rounded-md">
                {{ $isSrgbv ? 'Report Case' : 'Report Incident' }}
            </a>
            @endif
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white border border-gray-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $incidents->total() }}</p>
            <p class="text-xs text-gray-500">Total</p>
        </div>
        <div class="bg-{{ $themeColor }}-50 border border-{{ $themeColor }}-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-{{ $themeColor }}-700">{{ $openCount ?? 0 }}</p>
            <p class="text-xs text-{{ $themeColor }}-600">Open</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-orange-700">{{ $criticalCount ?? 0 }}</p>
            <p class="text-xs text-orange-600">Critical</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-green-700">{{ $closedCount ?? 0 }}</p>
            <p class="text-xs text-green-600">Resolved</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-md p-4 text-center">
            <p class="text-2xl font-bold text-purple-700">{{ $publicCount ?? 0 }}</p>
            <p class="text-xs text-purple-600">Public Reports</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route($indexRoute) }}" class="bg-white border border-gray-200 rounded-md p-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Number, title, name, school..."
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm w-52 focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
            </div>
            @if(!$isSrgbv)
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Type</label>
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
                    <option value="">All Types</option>
                    @foreach(\App\Models\Incident::TYPES as $key => $label)
                    @if($key !== 'srgbv')
                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Source</label>
                <select name="source" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
                    <option value="">All Sources</option>
                    @foreach(\App\Models\Incident::SOURCES as $key => $label)
                    <option value="{{ $key }}" {{ request('source') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Incident::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">Priority</label>
                <select name="priority" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
                    <option value="">All Priorities</option>
                    @foreach(\App\Models\Incident::PRIORITIES as $key => $label)
                    <option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
            </div>
            <div>
                <label class="block text-[11px] text-gray-500 uppercase tracking-wide mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700 rounded-md">Filter</button>
            @if(request()->hasAny(['search', 'type', 'source', 'status', 'priority', 'date_from', 'date_to']))
            <a href="{{ route($indexRoute) }}" class="px-4 py-2 text-gray-500 text-sm hover:text-gray-700">Clear</a>
            @endif
        </div>
    </form>

    {{-- Cases Table --}}
    <div class="bg-white border border-gray-200 rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Case #</th>
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 160px;">Title / Category</th>
                        @if(!$isSrgbv)
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        @endif
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="min-width: 120px;">School / Location</th>
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Priority</th>
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Source</th>
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Reporter</th>
                        <th scope="col" class="px-4 py-3 text-right text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($incidents as $incident)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-xs font-mono font-semibold text-gray-900">{{ $incident->incident_number }}</span>
                                @if($incident->immediate_action_required)
                                <span class="text-[9px] px-1.5 py-0.5 mt-1 font-semibold bg-red-500 text-white rounded w-fit">URGENT</span>
                                @endif
                                @if($incident->is_confidential)
                                <span class="text-[9px] text-purple-600 font-medium mt-0.5">Confidential</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route($showRoute, $incident) }}" class="text-sm font-medium text-gray-900 hover:text-{{ $themeColor }}-700">{{ $incident->title }}</a>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $incident->category_label }}</p>
                        </td>
                        @if(!$isSrgbv)
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-[10px] px-2 py-1 font-medium bg-{{ $incident->type_color }}-100 text-{{ $incident->type_color }}-700 rounded">{{ $incident->type_label }}</span>
                        </td>
                        @endif
                        <td class="px-4 py-3">
                            <div class="text-xs text-gray-900">{{ $incident->school_name ?? '—' }}</div>
                            @if($incident->school_county)
                            <div class="text-[10px] text-gray-500">{{ $incident->school_county }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-gray-900">{{ $incident->incident_date?->format('M d, Y') ?? '—' }}</div>
                            <div class="text-[10px] text-gray-400">{{ $incident->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $priorityColors = [
                                    'low' => 'bg-green-100 text-green-700',
                                    'medium' => 'bg-blue-100 text-blue-700',
                                    'high' => 'bg-orange-100 text-orange-700',
                                    'critical' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="text-[10px] px-2 py-1 font-medium {{ $priorityColors[$incident->priority] ?? 'bg-gray-100 text-gray-700' }} rounded">{{ $incident->priority_label }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $sourceColors = [
                                    'internal' => 'bg-sky-100 text-sky-700',
                                    'public' => 'bg-amber-100 text-amber-700',
                                    'hotline' => 'bg-indigo-100 text-indigo-700',
                                    'referral' => 'bg-teal-100 text-teal-700',
                                ];
                            @endphp
                            <span class="text-[10px] px-2 py-1 font-medium {{ $sourceColors[$incident->source] ?? 'bg-gray-100 text-gray-700' }} rounded">{{ $incident->source_label }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'reported' => 'bg-blue-100 text-blue-700',
                                    'under_review' => 'bg-amber-100 text-amber-700',
                                    'under_investigation' => 'bg-orange-100 text-orange-700',
                                    'action_taken' => 'bg-purple-100 text-purple-700',
                                    'referred' => 'bg-indigo-100 text-indigo-700',
                                    'resolved' => 'bg-green-100 text-green-700',
                                    'closed' => 'bg-gray-100 text-gray-600',
                                ];
                            @endphp
                            <span class="text-[10px] px-2 py-1 font-medium {{ $statusColors[$incident->status] ?? 'bg-gray-100 text-gray-700' }} rounded">{{ $incident->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-gray-700">
                                @if($incident->reporter)
                                    {{ Str::limit($incident->reporter->name, 15) }}
                                @elseif($incident->public_reporter_name)
                                    {{ Str::limit($incident->public_reporter_name, 15) }}
                                @else
                                    <span class="text-gray-400 italic">Anonymous</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route($showRoute, $incident) }}" class="p-1.5 text-gray-400 hover:text-{{ $themeColor }}-600 hover:bg-gray-100 rounded transition" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route($exportSingleRoute, $incident) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-gray-100 rounded transition" title="Export PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                                @if($canManage)
                                <a href="{{ route($editRoute, $incident) }}" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-gray-100 rounded transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route($destroyRoute, $incident) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this case? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isSrgbv ? '9' : '10' }}" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-gray-400 text-sm">No {{ $isSrgbv ? 'SRGBV cases' : 'incidents' }} found.</p>
                                @if($canManage)
                                <a href="{{ route($createRoute) }}" class="mt-2 text-sm text-{{ $themeColor }}-700 hover:underline">Report the first {{ $isSrgbv ? 'case' : 'incident' }} →</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($incidents->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $incidents->links() }}
        </div>
        @else
        <div class="px-4 py-3 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-500">Showing {{ $incidents->count() }} of {{ $incidents->total() }} {{ $isSrgbv ? 'cases' : 'incidents' }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
