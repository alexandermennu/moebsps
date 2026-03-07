@extends('layouts.app')
@section('title', 'Edit ' . $incident->incident_number)
@section('page-title', 'Edit Incident')
@section('content')
@php
    $isSrgbv = $incident->type === 'srgbv';
    $module = $module ?? ($isSrgbv ? 'srgbv' : 'other');
    $showRoute = $isSrgbv ? 'sir.srgbv.cases.show' : 'sir.other.incidents.show';
    $updateRoute = $isSrgbv ? 'sir.srgbv.cases.update' : 'sir.other.incidents.update';
    $themeColor = $isSrgbv ? 'red' : 'blue';
@endphp
<div class="max-w-5xl mx-auto">
    {{-- Page Header with Progress --}}
    <div class="bg-white border border-gray-200 rounded-lg mb-6 overflow-hidden shadow-sm">
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-white">Edit {{ $isSrgbv ? 'SRGBV Case' : 'Incident' }}</h1>
                        <p class="text-amber-100 text-sm">{{ $incident->incident_number }} • {{ $incident->title }}</p>
                    </div>
                </div>
                <a href="{{ route($showRoute, $incident) }}" class="text-white/80 hover:text-white text-sm flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Details
                </a>
            </div>
        </div>
        
        {{-- Section Progress Indicator --}}
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 overflow-x-auto">
            <div class="flex items-center justify-between text-xs min-w-max">
                <div class="flex items-center gap-1 text-gray-600" data-section="1">
                    <span class="w-5 h-5 rounded-full bg-amber-600 text-white flex items-center justify-center font-medium text-xs">1</span>
                    <span class="hidden sm:inline font-medium">Classification</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="2">
                    <span class="w-5 h-5 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">2</span>
                    <span class="hidden sm:inline">Status</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="3">
                    <span class="w-5 h-5 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">3</span>
                    <span class="hidden sm:inline">School</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="4">
                    <span class="w-5 h-5 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">4</span>
                    <span class="hidden sm:inline">Victim</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="5">
                    <span class="w-5 h-5 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">5</span>
                    <span class="hidden sm:inline">Perpetrator</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="6">
                    <span class="w-5 h-5 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">6</span>
                    <span class="hidden sm:inline">Details</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="7">
                    <span class="w-5 h-5 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">7</span>
                    <span class="hidden sm:inline">Risk</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="8">
                    <span class="w-5 h-5 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">8</span>
                    <span class="hidden sm:inline">Referral</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="9">
                    <span class="w-5 h-5 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">9</span>
                    <span class="hidden sm:inline">Assign</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route($updateRoute, $incident) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        {{-- Section 1: Classification --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-1">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">1. Classification</h3>
                        <p class="text-xs text-gray-500">Incident type, category, and priority level</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Incident Type <span class="text-red-500">*</span></label>
                        <select name="type" id="incident_type" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            @foreach(\App\Models\Incident::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $incident->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                        <select name="category" id="incident_category" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            <option value="">Select category...</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $incident->title) }}" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition resize-none">{{ old('description', $incident->description) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Incident Date <span class="text-red-500">*</span></label>
                        <input type="date" name="incident_date" value="{{ old('incident_date', $incident->incident_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Priority <span class="text-red-500">*</span></label>
                        <select name="priority" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            @foreach(\App\Models\Incident::PRIORITIES as $key => $label)
                            <option value="{{ $key }}" {{ old('priority', $incident->priority) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Status & Resolution --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-2">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">2. Status & Resolution</h3>
                        <p class="text-xs text-gray-500">Update case status and resolution details</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            @foreach(\App\Models\Incident::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $incident->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Resolution Date</label>
                        <input type="date" name="resolution_date" value="{{ old('resolution_date', $incident->resolution_date?->format('Y-m-d')) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Resolution Notes</label>
                    <textarea name="resolution" rows="3" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition resize-none" placeholder="Describe the resolution...">{{ old('resolution', $incident->resolution) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 3: School Information --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-3">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">3. School Information</h3>
                        <p class="text-xs text-gray-500">Where did this incident occur?</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">School Name</label>
                        <input type="text" name="school_name" value="{{ old('school_name', $incident->school_name) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">School Level</label>
                        <select name="school_level" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            <option value="">Select level...</option>
                            @foreach(\App\Models\Incident::SCHOOL_LEVELS as $key => $label)
                            <option value="{{ $key }}" {{ old('school_level', $incident->school_level) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">County</label>
                        <select name="school_county" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            <option value="">Select county...</option>
                            @foreach(\App\Models\User::COUNTIES as $county)
                            <option value="{{ $county }}" {{ old('school_county', $incident->school_county) === $county ? 'selected' : '' }}>{{ $county }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">District</label>
                        <input type="text" name="school_district" value="{{ old('school_district', $incident->school_district) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Incident Location</label>
                        <input type="text" name="incident_location" value="{{ old('incident_location', $incident->incident_location) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition" placeholder="e.g., Classroom B3">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Affected Person --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-4">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">4. Affected Person</h3>
                        <p class="text-xs text-gray-500">Information about the person affected by this incident</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <input type="text" name="victim_name" value="{{ old('victim_name', $incident->victim_name) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Age</label>
                        <select name="victim_age" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            <option value="">Select Age Range</option>
                            @foreach(\App\Models\Incident::VICTIM_AGE_RANGES as $key => $label)
                            <option value="{{ $key }}" {{ old('victim_age', $incident->victim_age) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                        <select name="victim_gender" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('victim_gender', $incident->victim_gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('victim_gender', $incident->victim_gender) === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Grade</label>
                        <input type="text" name="victim_grade" value="{{ old('victim_grade', $incident->victim_grade) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact</label>
                        <input type="text" name="victim_contact" value="{{ old('victim_contact', $incident->victim_contact) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                </div>
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-3 font-medium">Parent/Guardian Information</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Parent/Guardian Name</label>
                            <input type="text" name="victim_parent_guardian" value="{{ old('victim_parent_guardian', $incident->victim_parent_guardian) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Parent/Guardian Contact</label>
                            <input type="text" name="victim_parent_contact" value="{{ old('victim_parent_contact', $incident->victim_parent_contact) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 5: Perpetrator --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-5">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">5. Perpetrator</h3>
                        <p class="text-xs text-gray-500">If a perpetrator is known or suspected</p>
                    </div>
                    <span class="ml-auto text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Optional</span>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Perpetrator Name</label>
                        <input type="text" name="perpetrator_name" value="{{ old('perpetrator_name', $incident->perpetrator_name) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Perpetrator Type</label>
                        <select name="perpetrator_type" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            <option value="">Select Type</option>
                            @foreach(\App\Models\Incident::PERPETRATOR_TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('perpetrator_type', $incident->perpetrator_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <textarea name="perpetrator_description" rows="2" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition resize-none">{{ old('perpetrator_description', $incident->perpetrator_description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 6: Additional Details --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-6">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">6. Additional Details</h3>
                        <p class="text-xs text-gray-500">Detailed account, witnesses, and other context</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Detailed Account</label>
                    <textarea name="incident_description" rows="4" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition resize-none">{{ old('incident_description', $incident->incident_description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Witnesses</label>
                    <textarea name="witnesses" rows="2" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition resize-none">{{ old('witnesses', $incident->witnesses) }}</textarea>
                </div>
                <div class="flex gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="hidden" name="is_confidential" value="0">
                        <input type="checkbox" name="is_confidential" value="1" {{ old('is_confidential', $incident->is_confidential) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 focus:ring-offset-0">
                        <span class="text-sm text-gray-700 group-hover:text-gray-900 transition">Confidential</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="hidden" name="is_recurring" value="0">
                        <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring', $incident->is_recurring) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 focus:ring-offset-0">
                        <span class="text-sm text-gray-700 group-hover:text-gray-900 transition">Recurring Incident</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Section 7: Risk & Follow-Up --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-7">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">7. Risk & Follow-Up</h3>
                        <p class="text-xs text-gray-500">Assess risk level and schedule follow-up actions</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Risk Level</label>
                        <select name="risk_level" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                            <option value="">Not assessed</option>
                            @foreach(\App\Models\Incident::RISK_LEVELS as $key => $label)
                            <option value="{{ $key }}" {{ old('risk_level', $incident->risk_level) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer group pb-2">
                            <input type="hidden" name="immediate_action_required" value="0">
                            <input type="checkbox" name="immediate_action_required" value="1" {{ old('immediate_action_required', $incident->immediate_action_required) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-offset-0">
                            <span class="text-sm text-gray-700 group-hover:text-gray-900 transition font-medium">Immediate Action Required</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Safety Plan</label>
                    <textarea name="safety_plan" rows="2" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition resize-none">{{ old('safety_plan', $incident->safety_plan) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-gray-100">
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="hidden" name="follow_up_required" value="0">
                            <input type="checkbox" name="follow_up_required" value="1" {{ old('follow_up_required', $incident->follow_up_required) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 focus:ring-offset-0">
                            <span class="text-sm text-gray-700 group-hover:text-gray-900 transition">Follow-Up Required</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Follow-Up Date</label>
                        <input type="date" name="follow_up_date" value="{{ old('follow_up_date', $incident->follow_up_date?->format('Y-m-d')) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 8: Referral --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-8">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">8. Referral</h3>
                        <p class="text-xs text-gray-500">External agency referral information</p>
                    </div>
                    <span class="ml-auto text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Optional</span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Referral Agency</label>
                        <input type="text" name="referral_agency" value="{{ old('referral_agency', $incident->referral_agency) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition" placeholder="e.g., SGBV Crimes Unit, MOGCSP, hospital">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Referral Details</label>
                        <input type="text" name="referral_details" value="{{ old('referral_details', $incident->referral_details) }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition" placeholder="Contact person, reference number, etc.">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 9: Assignment --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-9">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">9. Assignment</h3>
                        <p class="text-xs text-gray-500">Assign this incident to a counselor for follow-up</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Assigned Counselor</label>
                <select name="assigned_to" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white transition">
                    <option value="">Unassigned</option>
                    @foreach($counselors as $counselor)
                    <option value="{{ $counselor->id }}" {{ old('assigned_to', $incident->assigned_to) == $counselor->id ? 'selected' : '' }}>{{ $counselor->name }} ({{ $counselor->county ?? 'No county' }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-2">Counselors are filtered by their assigned county</p>
            </div>
        </div>

        {{-- Submit Actions --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Changes will be saved immediately</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route($showRoute, $incident) }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Incident
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Category switching based on incident type
const categoriesByType = @json(\App\Models\Incident::CATEGORIES_BY_TYPE);
const typeSelect = document.getElementById('incident_type');
const categorySelect = document.getElementById('incident_category');
const currentCategory = @json(old('category', $incident->category));

function updateCategories() {
    const type = typeSelect.value;
    const categories = categoriesByType[type] || {};
    categorySelect.innerHTML = '<option value="">Select category...</option>';
    Object.entries(categories).forEach(([key, label]) => {
        const opt = document.createElement('option');
        opt.value = key;
        opt.textContent = label;
        if (key === currentCategory) opt.selected = true;
        categorySelect.appendChild(opt);
    });
}
typeSelect.addEventListener('change', updateCategories);
updateCategories();

// Section visibility tracking (highlights current section in progress bar)
const sections = document.querySelectorAll('[id^="section-"]');
const progressItems = document.querySelectorAll('[data-section]');

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const sectionNum = entry.target.id.replace('section-', '');
            progressItems.forEach(item => {
                const num = item.dataset.section;
                const circle = item.querySelector('span');
                if (num <= sectionNum) {
                    circle.classList.remove('bg-gray-300');
                    circle.classList.add('bg-amber-600');
                    item.classList.remove('text-gray-400');
                    item.classList.add('text-gray-600');
                } else {
                    circle.classList.add('bg-gray-300');
                    circle.classList.remove('bg-amber-600');
                    item.classList.add('text-gray-400');
                    item.classList.remove('text-gray-600');
                }
            });
        }
    });
}, { threshold: 0.3 });

sections.forEach(section => observer.observe(section));
</script>
@endsection
