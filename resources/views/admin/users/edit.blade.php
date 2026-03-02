@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-700 hover:underline">Back to Users</a>
    </div>

    <div class="bg-white border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-6">Edit User: {{ $user->name }}</h2>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400">(leave blank to keep)</span></label>
                    <input type="password" name="password" id="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select name="role" id="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ old('role', $user->role) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="division_id" class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                    <select name="division_id" id="division_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        <option value="">No Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>

            {{-- Profile Photo --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                <div class="flex items-center gap-4">
                    <x-user-avatar :user="$user" size="lg" />
                    <div class="flex-1">
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/webp"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border file:border-gray-300 file:text-sm file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                        <p class="mt-1 text-xs text-gray-400">JPG, PNG or WebP. Max 2MB.</p>
                        @if($user->hasProfilePhoto())
                            <label class="flex items-center gap-2 mt-2">
                                <input type="checkbox" name="remove_photo" value="1" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="text-xs text-red-600">Remove current photo</span>
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Counselor-specific Fields --}}
            <div id="counselor-fields" class="mb-4 p-4 bg-blue-50 border border-blue-200" style="display: none;">
                <h3 class="text-sm font-semibold text-blue-800 mb-3">Counselor Assignment</h3>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label for="counselor_school" class="block text-sm font-medium text-gray-700 mb-1">School of Assignment *</label>
                        <input type="text" name="counselor_school" id="counselor_school" value="{{ old('counselor_school', $user->counselor_school) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label for="counselor_county" class="block text-sm font-medium text-gray-700 mb-1">County *</label>
                        <select name="counselor_county" id="counselor_county"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select County...</option>
                            @foreach(\App\Models\User::COUNTIES as $county)
                                <option value="{{ $county }}" {{ old('counselor_county', $user->counselor_county) === $county ? 'selected' : '' }}>{{ $county }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label for="counselor_status" class="block text-sm font-medium text-gray-700 mb-1">Current Status *</label>
                        <select name="counselor_status" id="counselor_status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            @foreach(\App\Models\User::COUNSELOR_STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_status', $user->counselor_status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="counselor_appointed_at" class="block text-sm font-medium text-gray-700 mb-1">Appointment Date</label>
                        <input type="date" name="counselor_appointed_at" id="counselor_appointed_at" value="{{ old('counselor_appointed_at', $user->counselor_appointed_at?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-blue-800 mb-3 mt-4 pt-3 border-t border-blue-200">Counselor Profile</h3>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div>
                        <label for="counselor_qualification" class="block text-sm font-medium text-gray-700 mb-1">Qualification</label>
                        <select name="counselor_qualification" id="counselor_qualification"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::COUNSELOR_QUALIFICATIONS as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_qualification', $user->counselor_qualification) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="counselor_specialization" class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                        <select name="counselor_specialization" id="counselor_specialization"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::COUNSELOR_SPECIALIZATIONS as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_specialization', $user->counselor_specialization) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="counselor_years_experience" class="block text-sm font-medium text-gray-700 mb-1">Years Experience</label>
                        <input type="number" name="counselor_years_experience" id="counselor_years_experience" value="{{ old('counselor_years_experience', $user->counselor_years_experience) }}"
                               min="0" max="50" placeholder="e.g. 5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                {{-- Education Details Panel — appears when qualification is selected --}}
                @php $eduRecord = $user->counselorEducation->first() ?? null; @endphp
                <div id="admin-education-details" class="p-3 bg-white border border-blue-200 mb-3" style="display: none;">
                    <h4 class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-2">
                        Education Details — <span id="admin-edu-level-label">Institution info</span>
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">School / University</label>
                            <input type="text" name="edu_institution" value="{{ old('edu_institution', $eduRecord->institution ?? '') }}" placeholder="e.g. University of Liberia"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Program / Degree</label>
                            <input type="text" name="edu_program" value="{{ old('edu_program', $eduRecord->program ?? '') }}" placeholder="e.g. Bachelor of Education"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Year Started</label>
                            <input type="number" name="edu_year_started" value="{{ old('edu_year_started', $eduRecord->year_started ?? '') }}" min="1950" max="{{ date('Y') + 5 }}" placeholder="e.g. 2018"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Year Graduated</label>
                            <input type="number" name="edu_year_graduated" value="{{ old('edu_year_graduated', $eduRecord->year_graduated ?? '') }}" min="1950" max="{{ date('Y') + 5 }}" placeholder="e.g. 2022"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Country</label>
                            <input type="text" name="edu_country" value="{{ old('edu_country', $eduRecord->country ?? 'Liberia') }}" placeholder="e.g. Liberia"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                            <input type="text" name="edu_notes" value="{{ old('edu_notes', $eduRecord->notes ?? '') }}" placeholder="e.g. Graduated with Honours"
                                   class="w-full px-2.5 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="counselor_school_phone" class="block text-sm font-medium text-gray-700 mb-1">School Phone</label>
                    <input type="text" name="counselor_school_phone" id="counselor_school_phone" value="{{ old('counselor_school_phone', $user->counselor_school_phone) }}"
                           placeholder="+231-xxx-xxx-xxxx"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="counselor_training" class="block text-sm font-medium text-gray-700 mb-1">Training & Certifications</label>
                    <textarea name="counselor_training" id="counselor_training" rows="3"
                              placeholder="List relevant training programs and certifications..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">{{ old('counselor_training', $user->counselor_training) }}</textarea>
                </div>

                @if($user->isCounselor())
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <a href="{{ route('counselor-profile.show', $user) }}" class="text-xs text-blue-700 hover:underline font-medium">View Full Counselor Profile →</a>
                    </div>
                @endif
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-slate-600 border-gray-300 rounded focus:ring-slate-500">
                    <span class="text-sm text-gray-700">Active account</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">Update User</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const counselorFields = document.getElementById('counselor-fields');
        const divisionSelect = document.getElementById('division_id');

        // Find the CGPC division option value
        let cgpcOptionValue = null;
        for (const opt of divisionSelect.options) {
            if (opt.text.includes('Counseling') || opt.text.includes('CGPC')) {
                cgpcOptionValue = opt.value;
                break;
            }
        }

        function toggleCounselorFields() {
            const isCounselor = roleSelect.value === 'counselor';
            counselorFields.style.display = isCounselor ? 'block' : 'none';

            // Lock division to CGPC for counselors
            if (isCounselor && cgpcOptionValue) {
                divisionSelect.value = cgpcOptionValue;
                divisionSelect.disabled = true;
            } else {
                divisionSelect.disabled = false;
            }
        }

        roleSelect.addEventListener('change', toggleCounselorFields);
        toggleCounselorFields();

        // ── Education details panel toggle ─────────────────
        const qualSelect = document.getElementById('counselor_qualification');
        const eduPanel   = document.getElementById('admin-education-details');
        const eduLabel   = document.getElementById('admin-edu-level-label');
        const qualLabels = @json(\App\Models\User::COUNSELOR_QUALIFICATIONS);

        function toggleEduPanel() {
            if (qualSelect && eduPanel) {
                if (qualSelect.value) {
                    eduPanel.style.display = 'block';
                    eduLabel.textContent = qualLabels[qualSelect.value] || 'qualification';
                } else {
                    eduPanel.style.display = 'none';
                }
            }
        }
        if (qualSelect) {
            qualSelect.addEventListener('change', toggleEduPanel);
            toggleEduPanel();
        }

        // Ensure disabled select still submits — add hidden input
        divisionSelect.closest('form').addEventListener('submit', function () {
            if (divisionSelect.disabled) {
                divisionSelect.disabled = false;
            }
        });
    });
</script>
@endsection
