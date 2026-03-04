@extends('layouts.app')
@section('title', 'Report Incident')
@section('page-title', 'Report Incident')
@section('content')
@php
    $isSrgbv = ($module ?? 'other') === 'srgbv';
    $indexRoute = $isSrgbv ? 'sir.srgbv.cases.index' : 'sir.other.incidents.index';
    $storeRoute = $isSrgbv ? 'sir.srgbv.cases.store' : 'sir.other.incidents.store';
    $dashboardRoute = $isSrgbv ? 'sir.srgbv.dashboard' : 'sir.other.dashboard';
    $themeColor = $isSrgbv ? 'red' : 'blue';
@endphp
<div class="max-w-5xl mx-auto">
    {{-- Page Header with Progress --}}
    <div class="bg-white border border-gray-200 rounded-lg mb-6 overflow-hidden shadow-sm">
        <div class="bg-gradient-to-r from-{{ $themeColor }}-700 to-{{ $themeColor }}-800 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-white">Report New {{ $isSrgbv ? 'SRGBV Case' : 'Incident' }}</h1>
                        <p class="text-{{ $themeColor }}-100 text-sm">Complete all required sections below</p>
                    </div>
                </div>
                <a href="{{ route($dashboardRoute) }}" class="text-white/80 hover:text-white text-sm flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Sticky Section Progress Indicator --}}
    <div id="progress-bar" class="sticky top-0 z-40 bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
        <div class="px-6 py-3">
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-1 text-gray-600" data-section="1">
                    <span class="w-6 h-6 rounded-full bg-{{ $themeColor }}-600 text-white flex items-center justify-center font-medium text-xs">1</span>
                    <span class="hidden sm:inline font-medium">Classification</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1.5"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="2">
                    <span class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">2</span>
                    <span class="hidden sm:inline">School</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1.5"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="3">
                    <span class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">3</span>
                    <span class="hidden sm:inline">Victim</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1.5"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="4">
                    <span class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">4</span>
                    <span class="hidden sm:inline">Perpetrator</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1.5"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="5">
                    <span class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">5</span>
                    <span class="hidden sm:inline">Details</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1.5"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="6">
                    <span class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">6</span>
                    <span class="hidden sm:inline">Risk</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1.5"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="7">
                    <span class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">7</span>
                    <span class="hidden sm:inline">Assign</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-1.5"></div>
                <div class="flex items-center gap-1 text-gray-400" data-section="8">
                    <span class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center font-medium text-xs">8</span>
                    <span class="hidden sm:inline">Files</span>
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

    <form method="POST" action="{{ route($storeRoute) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Section 1: Incident Classification --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-1">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">1. Incident Classification</h3>
                        <p class="text-xs text-gray-500">Identify the incident type, category, and priority level</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Incident Type <span class="text-red-500">*</span></label>
                        <select name="type" id="incident-type" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition">
                            <option value="">Select Type</option>
                            @foreach(\App\Models\Incident::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $selectedType) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                        <select name="category" id="incident-category" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition">
                            <option value="">Select Type First</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Priority <span class="text-red-500">*</span></label>
                        <select name="priority" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition">
                            @foreach(\App\Models\Incident::PRIORITIES as $key => $label)
                            <option value="{{ $key }}" {{ old('priority', 'medium') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Incident Date <span class="text-red-500">*</span></label>
                        <input type="date" name="incident_date" value="{{ old('incident_date', now()->format('Y-m-d')) }}" required max="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Brief summary of the incident" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Detailed description of the incident..." class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 2: School Information --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-2">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">2. School Information</h3>
                        <p class="text-xs text-gray-500">Where did this incident occur?</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">School Name</label>
                        <input type="text" name="school_name" value="{{ old('school_name') }}" placeholder="Enter school name" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">School Level</label>
                        <select name="school_level" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition">
                            <option value="">Select Level</option>
                            @foreach(\App\Models\Incident::SCHOOL_LEVELS as $key => $label)
                            <option value="{{ $key }}" {{ old('school_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">County</label>
                        <select name="school_county" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition">
                            <option value="">Select County</option>
                            @foreach(\App\Models\User::COUNTIES as $county)
                            <option value="{{ $county }}" {{ old('school_county') === $county ? 'selected' : '' }}>{{ $county }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">District</label>
                        <input type="text" name="school_district" value="{{ old('school_district') }}" placeholder="District name" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Incident Location</label>
                        <input type="text" name="incident_location" value="{{ old('incident_location') }}" placeholder="e.g., Classroom, playground" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Affected Person / Victim --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-3">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">3. Affected Person</h3>
                        <p class="text-xs text-gray-500">Information about the person affected by this incident <span id="victim-required-note" class="text-red-600 font-medium hidden">• Required for SRGBV cases</span></p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name <span class="victim-required-star text-red-600 hidden">*</span></label>
                        <input type="text" name="victim_name" value="{{ old('victim_name') }}" placeholder="Victim's name" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Age</label>
                        <input type="number" name="victim_age" value="{{ old('victim_age') }}" min="1" max="100" placeholder="Age" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                        <select name="victim_gender" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('victim_gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('victim_gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('victim_gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Grade</label>
                        <input type="text" name="victim_grade" value="{{ old('victim_grade') }}" placeholder="e.g., 6th Grade" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact</label>
                        <input type="text" name="victim_contact" value="{{ old('victim_contact') }}" placeholder="Phone or email" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                </div>
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-3 font-medium">Parent/Guardian Information</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Parent/Guardian Name</label>
                            <input type="text" name="victim_parent_guardian" value="{{ old('victim_parent_guardian') }}" placeholder="Full name" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Parent/Guardian Contact</label>
                            <input type="text" name="victim_parent_contact" value="{{ old('victim_parent_contact') }}" placeholder="Phone number" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Perpetrator --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-4">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">4. Perpetrator Information</h3>
                        <p class="text-xs text-gray-500">If a perpetrator is known or suspected</p>
                    </div>
                    <span class="ml-auto text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Optional</span>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Perpetrator Name</label>
                        <input type="text" name="perpetrator_name" value="{{ old('perpetrator_name') }}" placeholder="If known" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Perpetrator Type</label>
                        <select name="perpetrator_type" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition">
                            <option value="">Select Type</option>
                            @foreach(\App\Models\Incident::PERPETRATOR_TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('perpetrator_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <textarea name="perpetrator_description" rows="2" placeholder="Any additional details about the perpetrator..." class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none">{{ old('perpetrator_description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 5: Additional Details --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-5">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">5. Additional Details</h3>
                        <p class="text-xs text-gray-500">Witnesses, detailed description, and other context</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Detailed Incident Description</label>
                    <textarea name="incident_description" rows="4" placeholder="Step-by-step account of what happened..." class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none">{{ old('incident_description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Witnesses</label>
                    <textarea name="witnesses" rows="2" placeholder="Names and contact information of witnesses..." class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none">{{ old('witnesses') }}</textarea>
                </div>
                <div class="pt-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-offset-0">
                        <span class="text-sm text-gray-700 group-hover:text-gray-900 transition">This is a recurring incident</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Section 6: Risk Assessment --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-6">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">6. Risk Assessment</h3>
                        <p class="text-xs text-gray-500">Assess the risk level and required urgency</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Risk Level</label>
                        <select name="risk_level" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition">
                            <option value="">Assess Risk</option>
                            @foreach(\App\Models\Incident::RISK_LEVELS as $key => $label)
                            <option value="{{ $key }}" {{ old('risk_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="immediate_action_required" value="1" {{ old('immediate_action_required') ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-offset-0">
                            <span class="text-sm text-gray-700 group-hover:text-gray-900 transition">Immediate action</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="is_confidential" value="1" {{ old('is_confidential', '1') ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-offset-0">
                            <span class="text-sm text-gray-700 group-hover:text-gray-900 transition">Confidential</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Safety Plan</label>
                    <textarea name="safety_plan" rows="2" placeholder="Any immediate safety measures taken or recommended..." class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none">{{ old('safety_plan') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 7: Assignment --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-7">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">7. Assignment</h3>
                        <p class="text-xs text-gray-500">Assign this incident to a counselor for follow-up</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Assign To</label>
                <select name="assigned_to" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white transition">
                    <option value="">Leave Unassigned</option>
                    @foreach($counselors as $counselor)
                    <option value="{{ $counselor->id }}" {{ old('assigned_to') == $counselor->id ? 'selected' : '' }}>{{ $counselor->name }} ({{ $counselor->counselor_county ?? 'N/A' }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-2">Counselors are filtered by their assigned county</p>
            </div>
        </div>

        {{-- Section 8: File Uploads --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm" id="section-8">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">8. Supporting Documents</h3>
                        <p class="text-xs text-gray-500">Upload any evidence, photos, or reports</p>
                    </div>
                    <span class="ml-auto text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Optional</span>
                </div>
            </div>
            <div class="p-6">
                <div class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center hover:border-gray-300 transition cursor-pointer" onclick="document.getElementById('file-input').click()">
                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-sm text-gray-600 mb-1">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 10MB each</p>
                    <input type="file" name="files[]" id="file-input" multiple class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </div>
                <div id="file-list" class="mt-3 space-y-2 hidden"></div>
            </div>
        </div>

        {{-- Submit Actions --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>All required fields must be completed</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route($dashboardRoute) }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-red-700 text-white text-sm font-medium hover:bg-red-800 rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Submit Report
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Dynamic Category & UI Enhancement Script --}}
<script>
// Category switching based on incident type
const categoriesByType = @json(\App\Models\Incident::CATEGORIES_BY_TYPE);
const oldCategory = @json(old('category', ''));
const typeSelect = document.getElementById('incident-type');
const categorySelect = document.getElementById('incident-category');

function updateCategories() {
    const type = typeSelect.value;
    categorySelect.innerHTML = '<option value="">Select Category</option>';
    if (type && categoriesByType[type]) {
        Object.entries(categoriesByType[type]).forEach(([key, label]) => {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = label;
            if (key === oldCategory) opt.selected = true;
            categorySelect.appendChild(opt);
        });
    }
    // Toggle victim required indicator for SRGBV
    document.getElementById('victim-required-note')?.classList.toggle('hidden', type !== 'srgbv');
    document.querySelectorAll('.victim-required-star').forEach(el => el.classList.toggle('hidden', type !== 'srgbv'));
}

typeSelect.addEventListener('change', updateCategories);
if (typeSelect.value) updateCategories();

// File upload preview
const fileInput = document.getElementById('file-input');
const fileList = document.getElementById('file-list');

fileInput.addEventListener('change', function() {
    fileList.innerHTML = '';
    if (this.files.length > 0) {
        fileList.classList.remove('hidden');
        Array.from(this.files).forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2';
            div.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-sm text-gray-700">${file.name}</span>
                    <span class="text-xs text-gray-500">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                </div>
            `;
            fileList.appendChild(div);
        });
    } else {
        fileList.classList.add('hidden');
    }
});

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
                    circle.classList.add('bg-red-600');
                    item.classList.remove('text-gray-400');
                    item.classList.add('text-gray-600');
                } else {
                    circle.classList.add('bg-gray-300');
                    circle.classList.remove('bg-red-600');
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
