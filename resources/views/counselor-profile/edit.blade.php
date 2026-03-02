@extends('layouts.app')

@section('title', 'Edit Counselor Profile')
@section('page-title', 'Edit Counselor Profile')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6 flex items-center gap-2 text-xs">
        <a href="{{ route('counselor-profile.show', $counselor) }}" class="text-blue-700 hover:underline">Counselor Profile</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-500">Edit</span>
    </div>

    <div class="bg-white border border-gray-200 p-6">
        <div class="flex items-center gap-4 border-b border-gray-200 pb-4 mb-6">
            <x-user-avatar :user="$counselor" size="lg" />
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $counselor->name }}</h2>
                <p class="text-sm text-gray-500">Edit your counselor qualifications, education, certificates, and training information.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('counselor-profile.update') }}">
            @csrf
            @method('PUT')

            {{-- ── Section 1: Highest Education & Details ──────────── --}}
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    Highest Level of Education
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="counselor_qualification" class="block text-sm font-medium text-gray-700 mb-1">Qualification Level *</label>
                        <select name="counselor_qualification" id="counselor_qualification"
                                class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::COUNSELOR_QUALIFICATIONS as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_qualification', $counselor->counselor_qualification) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('counselor_qualification')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="counselor_specialization" class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                        <select name="counselor_specialization" id="counselor_specialization"
                                class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::COUNSELOR_SPECIALIZATIONS as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_specialization', $counselor->counselor_specialization) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('counselor_specialization')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="counselor_years_experience" class="block text-sm font-medium text-gray-700 mb-1">Years of Experience</label>
                        <input type="number" name="counselor_years_experience" id="counselor_years_experience"
                               value="{{ old('counselor_years_experience', $counselor->counselor_years_experience) }}"
                               min="0" max="50" placeholder="e.g. 5"
                               class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('counselor_years_experience')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Education Details Panel — shown when qualification is selected --}}
                @php
                    $eduRecord = $counselor->counselorEducation->first();
                @endphp
                <div id="education-details" class="p-4 bg-blue-50 border border-blue-200" style="display: none;">
                    <h4 class="text-xs font-semibold text-blue-800 uppercase tracking-wide mb-3">
                        <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Education Details — <span id="edu-level-label">Where did you obtain this qualification?</span>
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edu_institution" class="block text-xs font-medium text-gray-700 mb-1">School / University *</label>
                            <input type="text" name="edu_institution" id="edu_institution"
                                   value="{{ old('edu_institution', $eduRecord->institution ?? '') }}"
                                   placeholder="e.g. University of Liberia"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('edu_institution')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="edu_program" class="block text-xs font-medium text-gray-700 mb-1">Program / Degree *</label>
                            <input type="text" name="edu_program" id="edu_program"
                                   value="{{ old('edu_program', $eduRecord->program ?? '') }}"
                                   placeholder="e.g. Bachelor of Education in Counseling"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('edu_program')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="edu_year_started" class="block text-xs font-medium text-gray-700 mb-1">Year Started</label>
                            <input type="number" name="edu_year_started" id="edu_year_started"
                                   value="{{ old('edu_year_started', $eduRecord->year_started ?? '') }}"
                                   min="1950" max="{{ date('Y') + 5 }}" placeholder="e.g. 2018"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="edu_year_graduated" class="block text-xs font-medium text-gray-700 mb-1">Year Graduated</label>
                            <input type="number" name="edu_year_graduated" id="edu_year_graduated"
                                   value="{{ old('edu_year_graduated', $eduRecord->year_graduated ?? '') }}"
                                   min="1950" max="{{ date('Y') + 5 }}" placeholder="e.g. 2022"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="edu_country" class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" name="edu_country" id="edu_country"
                                   value="{{ old('edu_country', $eduRecord->country ?? 'Liberia') }}"
                                   placeholder="e.g. Liberia"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="edu_notes" class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                            <input type="text" name="edu_notes" id="edu_notes"
                                   value="{{ old('edu_notes', $eduRecord->notes ?? '') }}"
                                   placeholder="e.g. Graduated with Honours"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 2: School Contact ──────────── --}}
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    School Contact
                </h3>
                <div class="max-w-sm">
                    <label for="counselor_school_phone" class="block text-sm font-medium text-gray-700 mb-1">School Phone Number</label>
                    <input type="text" name="counselor_school_phone" id="counselor_school_phone"
                           value="{{ old('counselor_school_phone', $counselor->counselor_school_phone) }}"
                           placeholder="e.g. +231-xxx-xxx-xxxx"
                           class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('counselor_school_phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ── Section 3: Training & Development ──────────── --}}
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Training & Development
                </h3>
                <div>
                    <label for="counselor_training" class="block text-sm font-medium text-gray-700 mb-1">Training Programs & Workshops Attended</label>
                    <textarea name="counselor_training" id="counselor_training" rows="4"
                              placeholder="List any training programs, workshops, or certifications you have completed. Include dates and organizations where applicable."
                              class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('counselor_training', $counselor->counselor_training) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Max 2000 characters.</p>
                    @error('counselor_training')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Read-Only Assignment Info --}}
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Assignment Details (Contact Admin to Change)</p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">School:</span>
                        <span class="font-medium text-gray-900">{{ $counselor->counselor_school ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">County:</span>
                        <span class="font-medium text-gray-900">{{ $counselor->counselor_county ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium text-gray-900">{{ $counselor->counselor_status_label }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Appointment Date:</span>
                        <span class="font-medium text-gray-900">{{ $counselor->counselor_appointed_at?->format('M j, Y') ?? '—' }}</span>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">School, county, status, and appointment date are managed by your administrator.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-medium hover:bg-blue-600">Save Changes</button>
                <a href="{{ route('counselor-profile.show', $counselor) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>

    {{-- ── Section 4: Additional Certificates & Achievements ──────────── --}}
    <div class="bg-white border border-gray-200 p-6 mt-6">
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-5">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                Additional Certificates & Achievements
            </h3>
            <span class="text-xs text-gray-400">{{ $counselor->counselorCertificates->count() }} certificate(s)</span>
        </div>

        {{-- Existing Certificates List --}}
        @if($counselor->counselorCertificates->count() > 0)
            <div class="divide-y divide-gray-100 mb-5">
                @foreach($counselor->counselorCertificates as $cert)
                    <div class="py-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 bg-amber-50 border border-amber-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $cert->certificate_name }}</p>
                                    <p class="text-xs text-gray-600 mt-0.5">{{ $cert->institution }}</p>
                                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                        @if($cert->program)
                                            <span>{{ $cert->program }}</span>
                                        @endif
                                        @if($cert->year_obtained)
                                            <span>{{ $cert->year_obtained }}</span>
                                        @endif
                                        @if($cert->certificate_number)
                                            <span>No: {{ $cert->certificate_number }}</span>
                                        @endif
                                        @if($cert->expiry_date)
                                            <span class="{{ $cert->is_expired ? 'text-red-500 font-medium' : 'text-green-600' }}">
                                                {{ $cert->status_label }} ({{ $cert->expiry_date->format('M Y') }})
                                            </span>
                                        @endif
                                    </div>
                                    @if($cert->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $cert->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('counselor-profile.certificates.delete', $cert) }}" onsubmit="return confirm('Remove this certificate?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 border border-red-200">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 italic mb-5">No additional certificates added yet.</p>
        @endif

        {{-- Add New Certificate Form (toggleable) --}}
        <div>
            <button type="button" id="toggle-cert-form" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-700 text-white text-xs font-medium hover:bg-blue-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Another Certificate
            </button>

            <div id="cert-form" class="mt-4 p-4 bg-gray-50 border border-gray-200" style="display: none;">
                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">New Certificate / Achievement</h4>
                <form method="POST" action="{{ route('counselor-profile.certificates.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Certificate / Achievement Name *</label>
                            <input type="text" name="certificate_name" required placeholder="e.g. Certified School Counselor"
                                   value="{{ old('certificate_name') }}"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('certificate_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Issuing Institution / School *</label>
                            <input type="text" name="institution" required placeholder="e.g. University of Liberia"
                                   value="{{ old('institution') }}"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('institution')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Program / Course</label>
                            <input type="text" name="program" placeholder="e.g. Counseling Certificate Program"
                                   value="{{ old('program') }}"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Year Obtained</label>
                            <input type="number" name="year_obtained" min="1950" max="{{ date('Y') + 5 }}" placeholder="e.g. 2023"
                                   value="{{ old('year_obtained') }}"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Certificate / Licence Number</label>
                            <input type="text" name="certificate_number" placeholder="Optional"
                                   value="{{ old('certificate_number') }}"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Description / Notes</label>
                        <textarea name="description" rows="2" placeholder="Any additional details about this certificate or achievement"
                                  class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-3 py-1.5 bg-blue-700 text-white text-xs font-medium hover:bg-blue-600">Save Certificate</button>
                        <button type="button" onclick="document.getElementById('cert-form').style.display='none'" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Qualification → Education Details toggle ───────────
    const qualSelect  = document.getElementById('counselor_qualification');
    const eduPanel    = document.getElementById('education-details');
    const eduLabel    = document.getElementById('edu-level-label');

    const qualLabels = @json(\App\Models\User::COUNSELOR_QUALIFICATIONS);

    function toggleEducationPanel() {
        if (qualSelect.value) {
            eduPanel.style.display = 'block';
            eduLabel.textContent = 'Where did you obtain your ' + (qualLabels[qualSelect.value] || 'qualification') + '?';
        } else {
            eduPanel.style.display = 'none';
        }
    }

    qualSelect.addEventListener('change', toggleEducationPanel);
    toggleEducationPanel(); // run on load

    // ── Add Certificate toggle ─────────────────────────────
    const toggleBtn = document.getElementById('toggle-cert-form');
    const certForm  = document.getElementById('cert-form');

    toggleBtn.addEventListener('click', function () {
        certForm.style.display = certForm.style.display === 'none' ? 'block' : 'none';
    });

    // Auto-show if there were validation errors
    @if($errors->hasAny(['certificate_name', 'institution']))
        certForm.style.display = 'block';
    @endif
});
</script>
@endsection
