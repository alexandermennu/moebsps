@extends('layouts.app')

@section('title', $counselor->name . ' — Counselor Profile')
@section('page-title', 'Counselor Profile')

@section('content')
<div class="max-w-6xl">
    {{-- Breadcrumb --}}
    <div class="mb-6 flex items-center gap-2 text-xs">
        @if(auth()->user()->hasFullAccess())
            <a href="{{ route('admin.users.counselors') }}" class="text-blue-700 hover:underline">Counselors</a>
            <span class="text-gray-400">/</span>
        @endif
        <span class="text-gray-500">{{ $counselor->name }}</span>
    </div>

    {{-- Profile Header Card --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-8">
            <div class="flex items-center gap-5">
                <x-user-avatar :user="$counselor" size="xl" />
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $counselor->name }}</h1>
                    <p class="text-blue-100 text-sm mt-1">School Counselor</p>
                    @if($counselor->division)
                        <p class="text-blue-200 text-xs mt-1">{{ $counselor->division->name }}</p>
                    @endif
                    <div class="mt-3 flex items-center gap-3">
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'abandoned_resigned' => 'bg-red-100 text-red-800',
                                'transferred' => 'bg-amber-100 text-amber-800',
                                'on_study_leave' => 'bg-purple-100 text-purple-800',
                                'on_sick_leave' => 'bg-orange-100 text-orange-800',
                                'returned_from_study' => 'bg-blue-100 text-blue-800',
                            ];
                            $statusClass = $statusColors[$counselor->counselor_status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $counselor->counselor_status_label }}</span>
                        @if(!$counselor->is_active)
                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800">Account Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions Bar --}}
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex items-center gap-3">
            @if($counselor->id === auth()->id())
                <a href="{{ route('counselor-profile.edit') }}" class="px-3 py-1.5 bg-blue-700 text-white text-xs font-medium hover:bg-blue-600">
                    Edit Counselor Profile
                </a>
                <a href="{{ route('profile.edit') }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50">
                    Edit Account
                </a>
            @elseif(auth()->user()->hasFullAccess())
                <a href="{{ route('admin.users.edit', $counselor) }}" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium hover:bg-slate-700">
                    Edit User Account
                </a>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 1 & 2: Personal Info + Assignment Details (side-by-side)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- SECTION 1: Personal Information --}}
        <div class="bg-white border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Section 1 — Personal Information
                </h2>
            </div>
            <div class="p-5">
                <dl class="space-y-3 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Full Name</dt>
                            <dd class="text-gray-900 font-medium">{{ $counselor->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Email Address</dt>
                            <dd class="text-gray-900">{{ $counselor->email }}</dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Date of Birth</dt>
                            <dd class="text-gray-900">{{ $counselor->date_of_birth?->format('F j, Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Gender</dt>
                            <dd class="text-gray-900">{{ \App\Models\User::GENDERS[$counselor->gender] ?? '—' }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Nationality</dt>
                        <dd class="text-gray-900">{{ $counselor->nationality ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Residential Address</dt>
                        <dd class="text-gray-900">{{ $counselor->address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">City / Town</dt>
                        <dd class="text-gray-900">{{ $counselor->city ?? '—' }}</dd>
                    </div>
                </dl>

                {{-- Contact Details --}}
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Contact Details</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-gray-500 text-xs font-medium">Phone</dt>
                                <dd class="text-gray-900">{{ $counselor->phone ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 text-xs font-medium">School Phone</dt>
                                <dd class="text-gray-900">{{ $counselor->counselor_school_phone ?? '—' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Position</dt>
                            <dd class="text-gray-900">{{ $counselor->position ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Member Since</dt>
                            <dd class="text-gray-900">{{ $counselor->created_at->format('F j, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Emergency Contact --}}
                @if($counselor->emergency_contact_name || $counselor->emergency_contact_phone)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Emergency Contact</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-gray-500 text-xs font-medium">Contact Name</dt>
                                <dd class="text-gray-900">{{ $counselor->emergency_contact_name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 text-xs font-medium">Contact Phone</dt>
                                <dd class="text-gray-900">{{ $counselor->emergency_contact_phone ?? '—' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Relationship</dt>
                            <dd class="text-gray-900">{{ $counselor->emergency_contact_relationship ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
                @endif
            </div>
        </div>

        {{-- SECTION 2: Assignment Details --}}
        <div class="bg-white border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Section 2 — Assignment Details
                </h2>
            </div>
            <div class="p-5">
                <dl class="space-y-3 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">School of Assignment</dt>
                            <dd class="text-gray-900 font-medium">{{ $counselor->counselor_school ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">County of Assignment</dt>
                            <dd class="text-gray-900 font-medium">{{ $counselor->counselor_county ?? '—' }}</dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Date of Appointment</dt>
                            <dd class="text-gray-900">{{ $counselor->counselor_appointed_at?->format('F j, Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Assignment / Start Date</dt>
                            <dd class="text-gray-900">{{ $counselor->counselor_assignment_date?->format('F j, Y') ?? '—' }}</dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">School District</dt>
                            <dd class="text-gray-900">{{ $counselor->counselor_school_district ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">School Address</dt>
                            <dd class="text-gray-900">{{ $counselor->counselor_school_address ?? '—' }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Principal / Head Teacher</dt>
                        <dd class="text-gray-900">{{ $counselor->counselor_school_principal ?? '—' }}</dd>
                    </div>
                </dl>

                {{-- School Brief Details --}}
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">School Brief Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                            <p class="text-xs text-gray-500 font-medium uppercase">School Level</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ \App\Models\User::SCHOOL_LEVELS[$counselor->counselor_school_level] ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                            <p class="text-xs text-gray-500 font-medium uppercase">School Type</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ \App\Models\User::SCHOOL_TYPES[$counselor->counselor_school_type] ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                            <p class="text-xs text-gray-500 font-medium uppercase">Student Population</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_school_population ? number_format($counselor->counselor_school_population) : '—' }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                            <p class="text-xs text-gray-500 font-medium uppercase">Student : Counselor Ratio</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_student_counselor_ratio ? $counselor->counselor_student_counselor_ratio . ' : 1' : '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Training --}}
                @if($counselor->counselor_training)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Training & Development</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($counselor->counselor_training)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 3: Education, Experience & Qualifications (full-width)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                Section 3 — Education, Experience & Qualifications
            </h2>
        </div>
        <div class="p-5">
            {{-- Qualification Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                    <p class="text-xs text-gray-500 font-medium uppercase">Highest Education</p>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_qualification_label }}</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                    <p class="text-xs text-gray-500 font-medium uppercase">Specialization</p>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_specialization_label }}</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                    <p class="text-xs text-gray-500 font-medium uppercase">Years of Experience</p>
                    <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_years_experience !== null ? $counselor->counselor_years_experience . ' years' : '—' }}</p>
                </div>
            </div>

            {{-- Primary Education Details --}}
            @php $eduRecord = $counselor->counselorEducation->first(); @endphp
            @if($eduRecord)
                <div class="bg-blue-50 border border-blue-200 p-4 mb-5">
                    <h3 class="text-xs font-semibold text-blue-800 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                        Education — {{ $eduRecord->degree_level_label }}
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div>
                            <span class="text-blue-600 text-xs font-medium">Institution</span>
                            <p class="text-gray-900 font-medium">{{ $eduRecord->institution }}</p>
                        </div>
                        <div>
                            <span class="text-blue-600 text-xs font-medium">Program</span>
                            <p class="text-gray-900">{{ $eduRecord->program ?: '—' }}</p>
                        </div>
                        <div>
                            <span class="text-blue-600 text-xs font-medium">Period</span>
                            <p class="text-gray-900">{{ $eduRecord->year_range }}</p>
                        </div>
                        @if($eduRecord->country)
                            <div>
                                <span class="text-blue-600 text-xs font-medium">Country</span>
                                <p class="text-gray-900">{{ $eduRecord->country }}</p>
                            </div>
                        @endif
                        @if($eduRecord->notes)
                            <div class="col-span-2 sm:col-span-4">
                                <span class="text-blue-600 text-xs font-medium">Notes</span>
                                <p class="text-gray-700 text-xs">{{ $eduRecord->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Additional Certificates & Achievements --}}
            @if($counselor->counselorCertificates->count() > 0)
                <div class="border-t border-gray-200 pt-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            Additional Certificates & Achievements
                        </h3>
                        <span class="text-xs text-gray-400">{{ $counselor->counselorCertificates->count() }} certificate(s)</span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($counselor->counselorCertificates as $cert)
                            <div class="py-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 bg-amber-50 border border-amber-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">{{ $cert->certificate_name }}</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $cert->institution }}</p>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                                            @if($cert->program)
                                                <span><strong>Program:</strong> {{ $cert->program }}</span>
                                            @endif
                                            @if($cert->year_obtained)
                                                <span><strong>Year:</strong> {{ $cert->year_obtained }}</span>
                                            @endif
                                            @if($cert->certificate_number)
                                                <span><strong>No:</strong> {{ $cert->certificate_number }}</span>
                                            @endif
                                            @if($cert->expiry_date)
                                                <span class="px-1.5 py-0.5 text-xs font-medium {{ $cert->is_expired ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                                                    {{ $cert->status_label }} — {{ $cert->expiry_date->format('M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($cert->description)
                                            <p class="text-xs text-gray-500 mt-1.5 italic">{{ $cert->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         DOCUMENTS & CERTIFICATES
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Documents & Certificates
            </h2>
            <span class="text-xs text-gray-400">{{ $counselor->counselorDocuments->count() }} document(s)</span>
        </div>
        <div class="p-5">
            @if($counselor->counselorDocuments->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($counselor->counselorDocuments as $doc)
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if($doc->isPdf())
                                    <div class="w-9 h-9 bg-red-50 border border-red-200 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                @elseif($doc->isImage())
                                    <div class="w-9 h-9 bg-blue-50 border border-blue-200 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @else
                                    <div class="w-9 h-9 bg-gray-50 border border-gray-200 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $doc->title }}</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 font-medium">{{ $doc->document_type_label }}</span>
                                        <span>{{ $doc->file_size_formatted }}</span>
                                        <span>{{ $doc->created_at->format('M j, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ $doc->getFileUrl() }}" target="_blank" class="px-2 py-1 text-xs text-blue-700 hover:bg-blue-50 border border-blue-200">View</a>
                                @if($counselor->id === auth()->id() || auth()->user()->hasFullAccess())
                                    <form method="POST" action="{{ route('counselor-profile.documents.delete', $doc) }}" onsubmit="return confirm('Delete this document?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 border border-red-200">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 italic py-2">No documents uploaded yet.</p>
            @endif

            {{-- Upload Document Form --}}
            @if($counselor->id === auth()->id() || auth()->user()->hasFullAccess())
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase mb-3">Upload New Document</h3>
                    <form method="POST"
                          action="{{ auth()->user()->hasFullAccess() && $counselor->id !== auth()->id()
                              ? route('admin.counselor-profile.documents.upload', $counselor)
                              : route('counselor-profile.documents.upload') }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label for="document_type" class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                                <select name="document_type" id="document_type" required
                                        class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select...</option>
                                    @foreach(\App\Models\CounselorDocument::DOCUMENT_TYPES as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="doc_title" class="block text-xs font-medium text-gray-600 mb-1">Title *</label>
                                <input type="text" name="title" id="doc_title" required placeholder="e.g. BSc Education Certificate"
                                       class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="file" name="document" required accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                                   class="flex-1 text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:border file:border-gray-300 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                            <button type="submit" class="px-3 py-1.5 bg-blue-700 text-white text-xs font-medium hover:bg-blue-600">Upload</button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">PDF, JPG, PNG, WebP, DOC, DOCX. Max 5MB.</p>
                        @error('document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
