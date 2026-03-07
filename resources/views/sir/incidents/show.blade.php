@extends('layouts.app')
@section('title', $incident->incident_number)
@section('page-title', 'Incident ' . $incident->incident_number)
@section('content')
@php
    $isSrgbv = $incident->type === 'srgbv';
    $module = $module ?? ($isSrgbv ? 'srgbv' : 'other');
    $indexRoute = $isSrgbv ? 'sir.srgbv.cases.index' : 'sir.other.incidents.index';
    $editRoute = $isSrgbv ? 'sir.srgbv.cases.edit' : 'sir.other.incidents.edit';
    $updateRoute = $isSrgbv ? 'sir.srgbv.cases.update' : 'sir.other.incidents.update';
    $destroyRoute = $isSrgbv ? 'sir.srgbv.cases.destroy' : 'sir.other.incidents.destroy';
    $statusRoute = $isSrgbv ? 'sir.srgbv.cases.status' : 'sir.other.incidents.status';
    $notesRoute = $isSrgbv ? 'sir.srgbv.cases.notes' : 'sir.other.incidents.notes';
    $filesRoute = $isSrgbv ? 'sir.srgbv.cases.files' : 'sir.other.incidents.files';
    $filesDeleteRoute = $isSrgbv ? 'sir.srgbv.cases.files.delete' : 'sir.other.incidents.files.delete';
    $exportRoute = $isSrgbv ? 'sir.srgbv.cases.export-single' : 'sir.other.incidents.export-single';
    $dashboardRoute = $isSrgbv ? 'sir.srgbv.dashboard' : 'sir.other.dashboard';
    $themeColor = $isSrgbv ? 'red' : 'blue';
@endphp
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Breadcrumb & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('sir.dashboard') }}" class="hover:text-gray-600">SIR</a>
                <span>›</span>
                <a href="{{ route($dashboardRoute) }}" class="hover:text-gray-600">{{ $isSrgbv ? 'SRGBV' : 'Other Incidents' }}</a>
                <span>›</span>
                <span class="text-gray-600">{{ $incident->incident_number }}</span>
            </div>
            <h2 class="text-xl font-bold text-gray-900">{{ $incident->title }}</h2>
            <p class="text-sm text-gray-500">{{ $incident->category_label }} • {{ $incident->incident_date?->format('M d, Y') ?? 'Date unknown' }} • Reported {{ $incident->created_at?->diffForHumans() ?? 'recently' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route($exportRoute, $incident) }}" target="_blank" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition" title="Export as PDF">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </a>
            <a href="{{ route($dashboardRoute) }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            @if($canManage)
            <a href="{{ route($editRoute, $incident) }}" class="inline-flex items-center gap-2 bg-{{ $themeColor }}-600 hover:bg-{{ $themeColor }}-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            @endif
        </div>
    </div>

    {{-- Incident Header Card --}}
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        {{-- Header Bar --}}
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-{{ $themeColor }}-100 rounded-lg flex items-center justify-center">
                        @if($isSrgbv)
                        <svg class="w-6 h-6 text-{{ $themeColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        @else
                        <svg class="w-6 h-6 text-{{ $themeColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-mono bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $incident->incident_number }}</span>
                            @if($incident->immediate_action_required)
                            <span class="text-xs bg-red-600 text-white px-2 py-0.5 rounded font-semibold">URGENT</span>
                            @endif
                            @if($incident->is_confidential)
                            <span class="text-xs bg-purple-600 text-white px-2 py-0.5 rounded">Confidential</span>
                            @endif
                        </div>
                        <h1 class="text-lg font-semibold text-gray-900">{{ $incident->title }}</h1>
                    </div>
                </div>
                
                {{-- Quick Status Change --}}
                @if($canManage && $incident->isOpen())
                <form method="POST" action="{{ route($statusRoute, $incident) }}" class="flex items-center gap-2">
                    @csrf @method('PATCH')
                    <span class="text-xs text-gray-500">Status:</span>
                    <select name="status" class="text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500 bg-white">
                        @foreach(\App\Models\Incident::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ $incident->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="px-3 py-1.5 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700 rounded-lg transition">Update</button>
                </form>
                @endif
            </div>
        </div>

        {{-- Status badges bar --}}
        <div class="px-6 py-3 bg-gray-50 flex flex-wrap items-center gap-2">
            <span class="text-xs px-2.5 py-1 font-medium rounded-lg bg-{{ $incident->type_color }}-100 text-{{ $incident->type_color }}-700">{{ $incident->type_label }}</span>
            <span class="text-xs px-2.5 py-1 font-medium rounded-lg bg-{{ $incident->priority_color }}-100 text-{{ $incident->priority_color }}-700">{{ $incident->priority_label }}</span>
            <span class="text-xs px-2.5 py-1 font-medium rounded-lg bg-{{ $incident->status_color }}-100 text-{{ $incident->status_color }}-700">{{ $incident->status_label }}</span>
            <span class="text-xs px-2.5 py-1 font-medium rounded-lg bg-{{ $incident->source_color }}-100 text-{{ $incident->source_color }}-700">{{ $incident->source_label }}</span>
            @if($incident->is_recurring)
            <span class="text-xs px-2.5 py-1 font-medium rounded-lg bg-amber-100 text-amber-700">Recurring</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Description --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">Incident Description</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $incident->description }}</p>
                    @if($incident->incident_description)
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Detailed Account</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $incident->incident_description }}</p>
                    </div>
                    @endif
                    @if($incident->witnesses)
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Witnesses</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $incident->witnesses }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- School Information --}}
            @if($incident->school_name || $incident->school_county || $incident->incident_location)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">School Information</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($incident->school_name)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">School</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->school_name }}</span>
                        </div>
                        @endif
                        @if($incident->school_level)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Level</span>
                            <span class="text-sm font-semibold text-gray-900">{{ \App\Models\Incident::SCHOOL_LEVELS[$incident->school_level] ?? $incident->school_level }}</span>
                        </div>
                        @endif
                        @if($incident->school_county)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">County</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->school_county }}</span>
                        </div>
                        @endif
                        @if($incident->school_district)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">District</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->school_district }}</span>
                        </div>
                        @endif
                        @if($incident->incident_location)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Location</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->incident_location }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Affected Person --}}
            @if($incident->victim_name)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">Affected Person</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Name</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->victim_name }}</span>
                        </div>
                        @if($incident->victim_age)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Age Range</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->victim_age_label }}</span>
                        </div>
                        @endif
                        @if($incident->victim_gender)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Gender</span>
                            <span class="text-sm font-semibold text-gray-900">{{ ucfirst($incident->victim_gender) }}</span>
                        </div>
                        @endif
                        @if($incident->victim_grade)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Grade</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->victim_grade }}</span>
                        </div>
                        @endif
                        @if($incident->victim_contact)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Contact</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->victim_contact }}</span>
                        </div>
                        @endif
                        @if($incident->victim_parent_guardian)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Parent/Guardian</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->victim_parent_guardian }}</span>
                        </div>
                        @endif
                        @if($incident->victim_parent_contact)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Parent Contact</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->victim_parent_contact }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Perpetrator --}}
            @if($incident->perpetrator_name || $incident->perpetrator_type)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">Perpetrator Information</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        @if($incident->perpetrator_name)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Name</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $incident->perpetrator_name }}</span>
                        </div>
                        @endif
                        @if($incident->perpetrator_type)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wide block mb-1">Type</span>
                            <span class="text-sm font-semibold text-gray-900">{{ \App\Models\Incident::PERPETRATOR_TYPES[$incident->perpetrator_type] ?? $incident->perpetrator_type }}</span>
                        </div>
                        @endif
                    </div>
                    @if($incident->perpetrator_description)
                    <div class="bg-orange-50 border border-orange-100 rounded-lg p-4">
                        <h4 class="text-xs font-semibold text-orange-700 uppercase tracking-wide mb-2">Description</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $incident->perpetrator_description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Public Reporter Info --}}
            @if($incident->isPublicReport() && ($incident->public_reporter_name || $incident->public_reporter_phone || $incident->public_reporter_email))
            <div class="bg-white border border-green-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-green-100 bg-green-50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-green-900">Public Reporter</h3>
                            @if($incident->tracking_code)
                            <p class="text-xs text-green-600">Tracking Code: <span class="font-mono font-bold">{{ $incident->tracking_code }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @if($incident->public_reporter_name)
                        <div class="bg-green-50 rounded-lg p-3">
                            <span class="text-[11px] text-green-600 font-medium uppercase tracking-wide block mb-1">Name</span>
                            <span class="text-sm font-semibold text-green-900">{{ $incident->public_reporter_name }}</span>
                        </div>
                        @endif
                        @if($incident->public_reporter_phone)
                        <div class="bg-green-50 rounded-lg p-3">
                            <span class="text-[11px] text-green-600 font-medium uppercase tracking-wide block mb-1">Phone</span>
                            <span class="text-sm font-semibold text-green-900">{{ $incident->public_reporter_phone }}</span>
                        </div>
                        @endif
                        @if($incident->public_reporter_email)
                        <div class="bg-green-50 rounded-lg p-3">
                            <span class="text-[11px] text-green-600 font-medium uppercase tracking-wide block mb-1">Email</span>
                            <span class="text-sm font-semibold text-green-900">{{ $incident->public_reporter_email }}</span>
                        </div>
                        @endif
                        @if($incident->public_reporter_relationship)
                        <div class="bg-green-50 rounded-lg p-3">
                            <span class="text-[11px] text-green-600 font-medium uppercase tracking-wide block mb-1">Relationship</span>
                            <span class="text-sm font-semibold text-green-900">{{ \App\Models\Incident::REPORTER_RELATIONSHIPS[$incident->public_reporter_relationship] ?? $incident->public_reporter_relationship }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Files & Evidence --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">Files & Evidence</h3>
                        </div>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg font-medium">{{ $incident->files->count() }} file{{ $incident->files->count() !== 1 ? 's' : '' }}</span>
                    </div>
                </div>
                <div class="p-6">
                    {{-- Upload form --}}
                    <form method="POST" action="{{ route($filesRoute, $incident) }}" enctype="multipart/form-data" class="mb-5">
                        @csrf
                        <div class="border-2 border-dashed border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors bg-gray-50/50">
                            <div class="flex flex-col sm:flex-row items-center gap-3">
                                <div class="flex-1 w-full">
                                    <input type="file" name="files[]" multiple class="block w-full text-sm text-gray-500 
                                        file:mr-3 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium
                                        file:bg-indigo-50 file:text-indigo-700 file:rounded-lg
                                        hover:file:bg-indigo-100 file:cursor-pointer file:transition">
                                </div>
                                <select name="file_category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                    @foreach(\App\Models\Incident::FILE_CATEGORIES as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700 rounded-lg transition flex items-center gap-2 whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    Upload
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($incident->files->count())
                    @php
                        $imageFiles = $incident->files->filter(fn($f) => $f->isImage() && $f->getFileUrl());
                        $documentFiles = $incident->files->filter(fn($f) => !$f->isImage() || !$f->getFileUrl());
                    @endphp

                    {{-- Image Gallery Grid --}}
                    @if($imageFiles->count())
                    <div class="mb-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Images ({{ $imageFiles->count() }})</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3" id="image-gallery">
                            @foreach($imageFiles as $index => $file)
                            @php $fileUrl = $file->getFileUrl(); @endphp
                            <div class="relative group aspect-square">
                                <img 
                                    src="{{ $fileUrl }}" 
                                    alt="{{ $file->file_name }}" 
                                    class="w-full h-full object-cover rounded-lg cursor-pointer hover:opacity-90 transition shadow-sm gallery-image"
                                    data-index="{{ $index }}"
                                    data-url="{{ $fileUrl }}"
                                    data-name="{{ $file->file_name }}"
                                    data-category="{{ $file->category_label }}"
                                    data-size="{{ $file->file_size_formatted }}"
                                    data-uploader="{{ $file->uploader?->name ?? 'Public' }}"
                                    onclick="openLightbox({{ $index }})"
                                >
                                {{-- Overlay with info --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent rounded-lg opacity-0 group-hover:opacity-100 transition pointer-events-none">
                                    <div class="absolute bottom-0 left-0 right-0 p-2">
                                        <p class="text-xs text-white truncate">{{ $file->file_name }}</p>
                                        <p class="text-[10px] text-gray-300">{{ $file->category_label }}</p>
                                    </div>
                                </div>
                                {{-- Delete button --}}
                                @if($canManage || $file->uploaded_by === auth()->id())
                                <form method="POST" action="{{ route($filesDeleteRoute, [$incident, $file]) }}" onsubmit="return confirm('Delete this file?')" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    @csrf @method('DELETE')
                                    <button class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Documents List --}}
                    @if($documentFiles->count())
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Documents ({{ $documentFiles->count() }})</p>
                        <div class="space-y-2">
                            @foreach($documentFiles as $file)
                            @php $fileUrl = $file->getFileUrl(); @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center shrink-0">
                                        @php
                                            $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                            $iconColor = match($ext) {
                                                'pdf' => 'text-red-500',
                                                'doc', 'docx' => 'text-blue-500',
                                                'xls', 'xlsx' => 'text-green-500',
                                                'ppt', 'pptx' => 'text-orange-500',
                                                default => 'text-gray-400'
                                            };
                                        @endphp
                                        <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $file->file_name }}</p>
                                        <p class="text-xs text-gray-400 flex items-center gap-1.5">
                                            <span class="bg-gray-200 px-1.5 py-0.5 rounded text-gray-600">{{ $file->category_label }}</span>
                                            <span>{{ $file->file_size_formatted }}</span>
                                            <span>•</span>
                                            <span>{{ $file->uploader?->name ?? 'Public' }}</span>
                                            @if(!$fileUrl)
                                            <span class="text-red-500">• File missing</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition">
                                    @if($fileUrl)
                                    <a href="{{ $fileUrl }}" target="_blank" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">Download</a>
                                    @endif
                                    @if($canManage || $file->uploaded_by === auth()->id())
                                    <form method="POST" action="{{ route($filesDeleteRoute, [$incident, $file]) }}" onsubmit="return confirm('Delete this file?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-medium text-red-600 hover:text-red-800 transition">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-sm text-gray-500">No files uploaded yet</p>
                        <p class="text-xs text-gray-400 mt-1">Upload evidence files using the form above</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Lightbox Modal --}}
            <div id="lightbox-modal" class="fixed inset-0 z-50 hidden">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/90" onclick="closeLightbox()"></div>
                
                {{-- Content --}}
                <div class="relative h-full flex flex-col">
                    {{-- Header --}}
                    <div class="flex items-center justify-between p-4 text-white shrink-0">
                        <div>
                            <p id="lightbox-filename" class="font-medium"></p>
                            <p id="lightbox-meta" class="text-sm text-gray-400"></p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span id="lightbox-counter" class="text-sm text-gray-400"></span>
                            <a id="lightbox-download" href="#" target="_blank" class="text-sm text-indigo-400 hover:text-indigo-300">Download</a>
                            <button onclick="closeLightbox()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Image Container --}}
                    <div class="flex-1 flex items-center justify-center px-16 py-4 min-h-0">
                        <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
                    </div>
                    
                    {{-- Navigation --}}
                    <div class="absolute left-4 top-1/2 -translate-y-1/2">
                        <button id="lightbox-prev" onclick="navigateLightbox(-1)" class="w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition text-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                    </div>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2">
                        <button id="lightbox-next" onclick="navigateLightbox(1)" class="w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition text-white disabled:opacity-30 disabled:cursor-not-allowed">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Case Notes Timeline --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">Notes & Activity</h3>
                        </div>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg font-medium">{{ $notes->count() }} note{{ $notes->count() !== 1 ? 's' : '' }}</span>
                    </div>
                </div>
                <div class="p-6">
                    {{-- Add Note --}}
                    <form method="POST" action="{{ route($notesRoute, $incident) }}" class="mb-6">
                        @csrf
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <textarea name="note" rows="3" required placeholder="Add a note about this incident..." class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none bg-white"></textarea>
                            <div class="flex flex-wrap items-center gap-3 mt-3">
                                <select name="note_type" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white">
                                    @foreach(\App\Models\IncidentNote::NOTE_TYPES as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @if($canManage)
                                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                    <input type="checkbox" name="is_private" value="1" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                    <span>Private (managers only)</span>
                                </label>
                                @endif
                                <button class="ml-auto px-4 py-2 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700 rounded-lg transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add Note
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Notes list --}}
                    <div class="space-y-4">
                        @forelse($notes as $note)
                        <div class="relative pl-6 pb-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            {{-- Timeline dot --}}
                            <div class="absolute left-0 top-1 w-3 h-3 rounded-full border-2 {{ $note->is_private ? 'bg-amber-400 border-amber-400' : 'bg-teal-400 border-teal-400' }}"></div>
                            {{-- Vertical line --}}
                            @if(!$loop->last)
                            <div class="absolute left-1 top-4 w-0.5 h-full bg-gray-200"></div>
                            @endif
                            
                            <div class="flex items-center flex-wrap gap-2 mb-2">
                                <span class="text-sm font-semibold text-gray-800">{{ $note->user?->name ?? 'Unknown User' }}</span>
                                <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-lg">{{ $note->note_type_label }}</span>
                                @if($note->is_private)
                                <span class="text-xs px-2 py-0.5 bg-amber-100 text-amber-700 rounded-lg flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Private
                                </span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $note->created_at?->diffForHumans() ?? '' }}</span>
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $note->note }}</p>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="text-sm text-gray-500">No notes yet</p>
                            <p class="text-xs text-gray-400 mt-1">Add a note to document case progress</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar (1/3) --}}
        <div class="space-y-6">
            {{-- Details Card --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900">Details</h3>
                    </div>
                </div>
                <div class="p-5">
                    <dl class="space-y-4 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Status</dt>
                            <dd><span class="inline-block px-2.5 py-1 text-xs font-medium bg-{{ $incident->status_color }}-100 text-{{ $incident->status_color }}-700 rounded-lg">{{ $incident->status_label }}</span></dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Type</dt>
                            <dd class="font-medium text-gray-800">{{ $incident->type_label }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Category</dt>
                            <dd class="font-medium text-gray-800 text-right">{{ $incident->category_label }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Priority</dt>
                            <dd><span class="inline-block px-2.5 py-1 text-xs font-medium bg-{{ $incident->priority_color }}-100 text-{{ $incident->priority_color }}-700 rounded-lg">{{ $incident->priority_label }}</span></dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Source</dt>
                            <dd class="font-medium text-gray-800">{{ $incident->source_label }}</dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <dt class="text-gray-500 mb-1">Incident Date</dt>
                            <dd class="font-medium text-gray-800">{{ $incident->incident_date?->format('M d, Y') ?? 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 mb-1">Reported</dt>
                            <dd class="font-medium text-gray-800">{{ $incident->created_at?->format('M d, Y g:i A') ?? 'Unknown' }}</dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <dt class="text-gray-500 mb-1">Reporter</dt>
                            <dd class="font-medium text-gray-800">{{ $incident->reporter?->name ?? ($incident->public_reporter_name ?? 'Anonymous') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 mb-1">Assigned To</dt>
                            <dd class="font-medium {{ $incident->assigned_to ? 'text-gray-800' : 'text-gray-400' }}">{{ $incident->assignee?->name ?? 'Unassigned' }}</dd>
                        </div>
                        @if($incident->is_recurring)
                        <div class="border-t border-gray-100 pt-4">
                            <dd class="text-red-700 font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Recurring Incident
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Risk Assessment --}}
            @if($incident->risk_level || $incident->immediate_action_required || $incident->safety_plan)
            @php
                $riskColor = $incident->risk_level === 'immediate_danger' ? 'red' : ($incident->risk_level === 'high' ? 'orange' : 'amber');
            @endphp
            <div class="bg-white border border-{{ $riskColor }}-200 rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-{{ $riskColor }}-100 bg-{{ $riskColor }}-50">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-{{ $riskColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900">Risk Assessment</h3>
                    </div>
                </div>
                <div class="p-5">
                    <dl class="space-y-4 text-sm">
                        @if($incident->risk_level)
                        <div>
                            <dt class="text-gray-500 mb-1">Risk Level</dt>
                            <dd><span class="px-2.5 py-1 text-xs font-medium bg-{{ $incident->risk_level_color }}-100 text-{{ $incident->risk_level_color }}-700 rounded-lg">{{ $incident->risk_level_label }}</span></dd>
                        </div>
                        @endif
                        @if($incident->safety_plan)
                        <div>
                            <dt class="text-gray-500 mb-1">Safety Plan</dt>
                            <dd class="text-gray-700 leading-relaxed">{{ $incident->safety_plan }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            {{-- Follow-Up --}}
            @if($incident->follow_up_required || $incident->resolution)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900">Follow-Up & Resolution</h3>
                    </div>
                </div>
                <div class="p-5">
                    <dl class="space-y-4 text-sm">
                        @if($incident->follow_up_required)
                        <div>
                            <dt class="text-gray-500 mb-1">Follow-Up Date</dt>
                            <dd class="font-medium text-gray-800">{{ $incident->follow_up_date?->format('M d, Y') ?? 'Not set' }}</dd>
                        </div>
                        @endif
                        @if($incident->resolution)
                        <div>
                            <dt class="text-gray-500 mb-1">Resolution</dt>
                            <dd class="text-gray-700 leading-relaxed">{{ $incident->resolution }}</dd>
                        </div>
                        @endif
                        @if($incident->resolution_date)
                        <div>
                            <dt class="text-gray-500 mb-1">Resolution Date</dt>
                            <dd class="font-medium text-gray-800">{{ $incident->resolution_date?->format('M d, Y') }}</dd>
                        </div>
                        @endif
                        @if($incident->referral_agency)
                        <div class="border-t border-gray-100 pt-4">
                            <dt class="text-gray-500 mb-1">Referral Agency</dt>
                            <dd class="font-medium text-gray-800">{{ $incident->referral_agency }}</dd>
                        </div>
                        @endif
                        @if($incident->referral_details)
                        <div>
                            <dt class="text-gray-500 mb-1">Referral Details</dt>
                            <dd class="text-gray-700 leading-relaxed">{{ $incident->referral_details }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            {{-- Quick Assign (for managers) --}}
            @if($canManage)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900">Quick Assign</h3>
                    </div>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route($updateRoute, $incident) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="type" value="{{ $incident->type }}">
                        <input type="hidden" name="category" value="{{ $incident->category }}">
                        <input type="hidden" name="title" value="{{ $incident->title }}">
                        <input type="hidden" name="description" value="{{ $incident->description }}">
                        <input type="hidden" name="priority" value="{{ $incident->priority }}">
                        <input type="hidden" name="status" value="{{ $incident->status }}">
                        <input type="hidden" name="incident_date" value="{{ $incident->incident_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}">
                        <select name="assigned_to" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-{{ $themeColor }}-500 bg-white">
                            <option value="">— Unassigned —</option>
                            @foreach($counselors as $counselor)
                            <option value="{{ $counselor->id }}" {{ $incident->assigned_to == $counselor->id ? 'selected' : '' }}>{{ $counselor->name }}</option>
                            @endforeach
                        </select>
                        <button class="w-full px-4 py-2.5 bg-gray-800 text-white text-sm font-medium hover:bg-gray-700 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Update Assignment
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Status Progress --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900">Case Progress</h3>
                    </div>
                </div>
                <div class="p-5">
                    @php
                        $statusOrder = ['reported', 'under_review', 'under_investigation', 'action_taken', 'referred', 'resolved', 'closed'];
                        $currentIndex = array_search($incident->status, $statusOrder);
                        $progressColor = $incident->type === 'srgbv' ? 'red' : 'blue';
                    @endphp
                    <div class="space-y-3">
                        @foreach($statusOrder as $index => $status)
                        <div class="flex items-center gap-3">
                            <div class="relative flex items-center justify-center">
                                @php
                                    if ($index < $currentIndex) {
                                        $dotClass = 'bg-green-500 border-green-500';
                                    } elseif ($index === $currentIndex) {
                                        $dotClass = "bg-{$progressColor}-500 border-{$progressColor}-500 ring-4 ring-{$progressColor}-100";
                                    } else {
                                        $dotClass = 'border-gray-300 bg-white';
                                    }
                                @endphp
                                <div class="w-5 h-5 rounded-full border-2 transition-all {{ $dotClass }} flex items-center justify-center">
                                    @if($index < $currentIndex)
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @elseif($index === $currentIndex)
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                    @endif
                                </div>
                                @if($index < count($statusOrder) - 1)
                                <div class="absolute top-5 left-1/2 w-0.5 h-4 -translate-x-1/2 {{ $index < $currentIndex ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                                @endif
                            </div>
                            <span class="text-sm {{ $index < $currentIndex ? 'text-green-700 font-medium' : ($index === $currentIndex ? 'text-gray-900 font-semibold' : 'text-gray-400') }}">
                                {{ \App\Models\Incident::STATUSES[$status] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Delete Action --}}
            @if($canManage)
            <div class="bg-white border border-red-200 rounded-lg overflow-hidden">
                <div class="p-5">
                    <h3 class="text-sm font-semibold text-red-800 mb-2">Danger Zone</h3>
                    <p class="text-xs text-gray-500 mb-3">Once deleted, this incident cannot be recovered.</p>
                    <form method="POST" action="{{ route($destroyRoute, $incident) }}" onsubmit="return confirm('Permanently delete this incident? This action cannot be undone.')">
                        @csrf @method('DELETE')
                        <button class="w-full px-4 py-2.5 bg-white border border-red-300 text-red-600 text-sm font-medium hover:bg-red-50 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete Incident
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Lightbox JavaScript --}}
<script>
    // Gallery images data
    const galleryImages = [];
    document.querySelectorAll('.gallery-image').forEach((img, index) => {
        galleryImages.push({
            url: img.dataset.url,
            name: img.dataset.name,
            category: img.dataset.category,
            size: img.dataset.size,
            uploader: img.dataset.uploader
        });
    });
    
    let currentImageIndex = 0;
    
    function openLightbox(index) {
        if (galleryImages.length === 0) return;
        currentImageIndex = index;
        updateLightbox();
        document.getElementById('lightbox-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        document.getElementById('lightbox-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    function navigateLightbox(direction) {
        currentImageIndex += direction;
        if (currentImageIndex < 0) currentImageIndex = galleryImages.length - 1;
        if (currentImageIndex >= galleryImages.length) currentImageIndex = 0;
        updateLightbox();
    }
    
    function updateLightbox() {
        const img = galleryImages[currentImageIndex];
        if (!img) return;
        
        document.getElementById('lightbox-image').src = img.url;
        document.getElementById('lightbox-filename').textContent = img.name;
        document.getElementById('lightbox-meta').textContent = `${img.category} • ${img.size} • Uploaded by ${img.uploader}`;
        document.getElementById('lightbox-counter').textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
        document.getElementById('lightbox-download').href = img.url;
        
        // Update navigation buttons
        document.getElementById('lightbox-prev').disabled = galleryImages.length <= 1;
        document.getElementById('lightbox-next').disabled = galleryImages.length <= 1;
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('lightbox-modal');
        if (modal.classList.contains('hidden')) return;
        
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });
</script>
@endsection
