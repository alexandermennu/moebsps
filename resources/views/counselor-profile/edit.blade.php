@extends('layouts.app')

@section('title', 'Edit Counselor Profile')
@section('page-title', 'Edit Counselor Profile')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6 flex items-center gap-2 text-xs">
        <a href="{{ route('counselor-profile.show', $counselor) }}" class="text-blue-700 hover:underline">Counselor Profile</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-500">Edit</span>
    </div>

    {{-- Profile Header --}}
    <div class="bg-white border border-gray-200 p-5 mb-6">
        <div class="flex items-center gap-4">
            <x-user-avatar :user="$counselor" size="lg" />
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $counselor->name }}</h2>
                <p class="text-sm text-gray-500">Edit your personal details, assignment information, education, and professional credentials.</p>
            </div>
        </div>
    </div>

    {{-- Profile Status Banner --}}
    @if($counselor->counselor_profile_status === 'pending_review')
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <h3 class="text-sm font-semibold text-amber-800">Profile Pending Review</h3>
                <p class="text-xs text-amber-700 mt-0.5">Your profile is currently awaiting administrator review. You can still make changes — any updates will keep the profile in review status until approved.</p>
            </div>
        </div>
    @elseif($counselor->counselor_profile_status === 'changes_requested')
        <div class="mb-6 p-4 bg-red-50 border border-red-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <div>
                <h3 class="text-sm font-semibold text-red-800">Changes Requested</h3>
                <p class="text-xs text-red-700 mt-0.5">An administrator has reviewed your profile and requested changes. Please update the relevant information and resubmit.</p>
                @if($counselor->counselor_profile_review_notes)
                    <div class="mt-2 p-2 bg-white border border-red-200 text-xs text-red-800">
                        <strong>Reviewer Notes:</strong> {{ $counselor->counselor_profile_review_notes }}
                    </div>
                @endif
            </div>
        </div>
    @elseif($counselor->counselor_profile_status === 'approved')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <h3 class="text-sm font-semibold text-green-800">Profile Approved</h3>
                <p class="text-xs text-green-700 mt-0.5">Your profile has been verified by an administrator. Any changes you make will require re-approval.</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200">
            <h3 class="text-sm font-semibold text-red-800 mb-2">Please correct the following errors:</h3>
            <ul class="text-sm text-red-600 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('counselor-profile.update') }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- ═══════════════════════════════════════════════════════════════════
                 SECTION 1: PERSONAL INFORMATION
                 ═══════════════════════════════════════════════════════════════════ --}}
            <div class="bg-white border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Personal Information
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Basic personal and contact details.</p>
                </div>
                <div class="p-5 space-y-4">
                    {{-- Name & Email (read-only) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Full Name</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 border border-gray-200 px-3 py-2">{{ $counselor->name }}</p>
                            <p class="text-xs text-gray-400 mt-1">Change via <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">Account Settings</a></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Email Address</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 border border-gray-200 px-3 py-2">{{ $counselor->email }}</p>
                            <p class="text-xs text-gray-400 mt-1">Change via <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">Account Settings</a></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date_of_birth" class="block text-xs font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth"
                                   value="{{ old('date_of_birth', $counselor->date_of_birth?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('date_of_birth') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="gender" class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
                            <select name="gender" id="gender"
                                    class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select...</option>
                                @foreach(\App\Models\User::GENDERS as $key => $label)
                                    <option value="{{ $key }}" {{ old('gender', $counselor->gender) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="nationality" class="block text-xs font-medium text-gray-700 mb-1">Nationality</label>
                        <input type="text" name="nationality" id="nationality"
                               value="{{ old('nationality', $counselor->nationality ?? 'Liberian') }}"
                               placeholder="e.g. Liberian"
                               class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('nationality') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-medium text-gray-700 mb-1">Residential Address</label>
                        <input type="text" name="address" id="address"
                               value="{{ old('address', $counselor->address) }}"
                               placeholder="e.g. Paynesville, Red Light Area"
                               class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-xs font-medium text-gray-700 mb-1">City / Town</label>
                        <input type="text" name="city" id="city"
                               value="{{ old('city', $counselor->city) }}"
                               placeholder="e.g. Monrovia"
                               class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Contact Details --}}
                    <div class="pt-3 border-t border-gray-200">
                        <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Contact Details</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Phone Number</label>
                                <p class="text-sm font-medium text-gray-900 bg-gray-50 border border-gray-200 px-3 py-2">{{ $counselor->phone ?? '—' }}</p>
                                <p class="text-xs text-gray-400 mt-1">Change via <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">Account Settings</a></p>
                            </div>
                            <div>
                                <label for="counselor_school_phone" class="block text-xs font-medium text-gray-700 mb-1">School Phone</label>
                                <input type="text" name="counselor_school_phone" id="counselor_school_phone"
                                       value="{{ old('counselor_school_phone', $counselor->counselor_school_phone) }}"
                                       placeholder="+231-xxx-xxx-xxxx"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('counselor_school_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Emergency Contact --}}
                    <div class="pt-3 border-t border-gray-200">
                        <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Emergency Contact</h4>
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label for="emergency_contact_name" class="block text-xs font-medium text-gray-700 mb-1">Contact Name</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                                       value="{{ old('emergency_contact_name', $counselor->emergency_contact_name) }}"
                                       placeholder="e.g. John Doe"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="emergency_contact_phone" class="block text-xs font-medium text-gray-700 mb-1">Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone"
                                       value="{{ old('emergency_contact_phone', $counselor->emergency_contact_phone) }}"
                                       placeholder="+231-xxx-xxx-xxxx"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label for="emergency_contact_relationship" class="block text-xs font-medium text-gray-700 mb-1">Relationship</label>
                            <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship"
                                   value="{{ old('emergency_contact_relationship', $counselor->emergency_contact_relationship) }}"
                                   placeholder="e.g. Spouse, Parent, Sibling"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
                 SECTION 2: ASSIGNMENT DETAILS
                 ═══════════════════════════════════════════════════════════════════ --}}
            <div class="bg-white border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Assignment Details
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Your current school placement and assignment information.</p>
                </div>
                <div class="p-5 space-y-4">
                    {{-- Admin-managed fields (read-only) --}}
                    <div class="p-4 bg-amber-50 border border-amber-200">
                        <p class="text-xs font-semibold text-amber-800 uppercase tracking-wide mb-3">
                            <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Admin-Managed (Contact Admin to Change)
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-amber-700 text-xs font-medium">School of Assignment</span>
                                <p class="text-gray-900 font-medium">{{ $counselor->counselor_school ?? '—' }}</p>
                            </div>
                            <div>
                                <span class="text-amber-700 text-xs font-medium">County of Assignment</span>
                                <p class="text-gray-900 font-medium">{{ $counselor->counselor_county ?? '—' }}</p>
                            </div>
                            <div>
                                <span class="text-amber-700 text-xs font-medium">Current Status</span>
                                <p class="text-gray-900 font-medium">{{ $counselor->counselor_status_label }}</p>
                            </div>
                            <div>
                                <span class="text-amber-700 text-xs font-medium">Date of Appointment</span>
                                <p class="text-gray-900 font-medium">{{ $counselor->counselor_appointed_at?->format('M j, Y') ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Self-service assignment detail fields --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="counselor_assignment_date" class="block text-xs font-medium text-gray-700 mb-1">Assignment / Start Date</label>
                            <input type="date" name="counselor_assignment_date" id="counselor_assignment_date"
                                   value="{{ old('counselor_assignment_date', $counselor->counselor_assignment_date?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('counselor_assignment_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="counselor_school_district" class="block text-xs font-medium text-gray-700 mb-1">School District</label>
                            <input type="text" name="counselor_school_district" id="counselor_school_district"
                                   value="{{ old('counselor_school_district', $counselor->counselor_school_district) }}"
                                   placeholder="e.g. District #1"
                                   class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('counselor_school_district') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="counselor_school_address" class="block text-xs font-medium text-gray-700 mb-1">School Address / Location</label>
                        <input type="text" name="counselor_school_address" id="counselor_school_address"
                               value="{{ old('counselor_school_address', $counselor->counselor_school_address) }}"
                               placeholder="e.g. Congo Town, Tubman Blvd"
                               class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('counselor_school_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="counselor_school_principal" class="block text-xs font-medium text-gray-700 mb-1">School Principal / Head Teacher</label>
                        <input type="text" name="counselor_school_principal" id="counselor_school_principal"
                               value="{{ old('counselor_school_principal', $counselor->counselor_school_principal) }}"
                               placeholder="e.g. Mrs. Jane Smith"
                               class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('counselor_school_principal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- School Brief Details --}}
                    <div class="pt-3 border-t border-gray-200">
                        <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">School Brief Details</h4>
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label for="counselor_school_level" class="block text-xs font-medium text-gray-700 mb-1">School Level</label>
                                <select name="counselor_school_level" id="counselor_school_level"
                                        class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select...</option>
                                    @foreach(\App\Models\User::SCHOOL_LEVELS as $key => $label)
                                        <option value="{{ $key }}" {{ old('counselor_school_level', $counselor->counselor_school_level) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('counselor_school_level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="counselor_school_type" class="block text-xs font-medium text-gray-700 mb-1">School Type</label>
                                <select name="counselor_school_type" id="counselor_school_type"
                                        class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select...</option>
                                    @foreach(\App\Models\User::SCHOOL_TYPES as $key => $label)
                                        <option value="{{ $key }}" {{ old('counselor_school_type', $counselor->counselor_school_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('counselor_school_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="counselor_school_population" class="block text-xs font-medium text-gray-700 mb-1">Student Population</label>
                                <input type="number" name="counselor_school_population" id="counselor_school_population"
                                       value="{{ old('counselor_school_population', $counselor->counselor_school_population) }}"
                                       min="0" max="50000" placeholder="e.g. 1200"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('counselor_school_population') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="counselor_num_boys" class="block text-xs font-medium text-gray-700 mb-1">No. of Boys</label>
                                <input type="number" name="counselor_num_boys" id="counselor_num_boys"
                                       value="{{ old('counselor_num_boys', $counselor->counselor_num_boys) }}"
                                       min="0" max="50000" placeholder="e.g. 600"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('counselor_num_boys') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="counselor_num_girls" class="block text-xs font-medium text-gray-700 mb-1">No. of Girls</label>
                                <input type="number" name="counselor_num_girls" id="counselor_num_girls"
                                       value="{{ old('counselor_num_girls', $counselor->counselor_num_girls) }}"
                                       min="0" max="50000" placeholder="e.g. 600"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('counselor_num_girls') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Training & Development --}}
                    <div class="pt-3 border-t border-gray-200">
                        <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Training & Development Notes</h4>
                        <div>
                            <label for="counselor_training" class="block text-xs font-medium text-gray-700 mb-1">Training Programs & Workshops Attended</label>
                            <textarea name="counselor_training" id="counselor_training" rows="4"
                                      placeholder="List any training programs, workshops, or certifications completed. Include dates and organizations."
                                      class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('counselor_training', $counselor->counselor_training) }}</textarea>
                            <p class="mt-1 text-xs text-gray-400">Max 2000 characters.</p>
                            @error('counselor_training') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION 3: EXPERIENCE & SPECIALIZATION  (full-width)
             ═══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 mb-6">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    Experience & Specialization
                </h3>
                <p class="text-xs text-gray-500 mt-1">Your area of specialization. Add your qualifications in the section below.</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="counselor_specialization" class="block text-xs font-medium text-gray-700 mb-1">Area of Specialization</label>
                        <select name="counselor_specialization" id="counselor_specialization"
                                class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::COUNSELOR_SPECIALIZATIONS as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_specialization', $counselor->counselor_specialization) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('counselor_specialization') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="counselor_years_experience" class="block text-xs font-medium text-gray-700 mb-1">Total Years of Counseling Experience</label>
                        <input type="number" name="counselor_years_experience" id="counselor_years_experience"
                               value="{{ old('counselor_years_experience', $counselor->counselor_years_experience) }}"
                               min="0" max="50" placeholder="e.g. 5"
                               class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('counselor_years_experience') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex gap-3 mb-6">
            <button type="submit" class="px-5 py-2.5 bg-blue-700 text-white text-sm font-medium hover:bg-blue-600">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Profile Changes
            </button>
            <a href="{{ route('counselor-profile.show', $counselor) }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</a>
        </div>
    </form>

    {{-- ═══════════════════════════════════════════════════════════════════
         QUALIFICATIONS & EDUCATION (separate card, outside main form)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                Qualifications & Education
            </h3>
            <span class="text-xs text-gray-400">{{ $counselor->counselorEducation->count() }} qualification(s)</span>
        </div>
        <div class="p-5">
            <p class="text-xs text-gray-500 mb-4">Add all your academic qualifications below. Each entry requires the qualification level, institution, year obtained, and optionally a supporting document.</p>

            @if($counselor->counselorEducation->count() > 0)
                <div class="divide-y divide-gray-100 mb-5">
                    @foreach($counselor->counselorEducation as $edu)
                        <div class="py-3">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 bg-blue-50 border border-blue-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $edu->degree_level_label }}</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $edu->institution }}</p>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                            @if($edu->program) <span>{{ $edu->program }}</span> @endif
                                            @if($edu->year_obtained) <span class="font-medium text-gray-600">Year: {{ $edu->year_obtained }}</span> @endif
                                            @if($edu->country) <span>{{ $edu->country }}</span> @endif
                                        </div>
                                        @if($edu->year_started || $edu->year_graduated)
                                            <p class="text-xs text-gray-400 mt-0.5">Period: {{ $edu->year_range }}</p>
                                        @endif
                                        @if($edu->notes)
                                            <p class="text-xs text-gray-500 mt-1">{{ $edu->notes }}</p>
                                        @endif
                                        {{-- Document attachment indicator --}}
                                        @if($edu->hasDocument())
                                            <div class="mt-1.5 flex items-center gap-1.5 text-xs">
                                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <a href="{{ $edu->getDocumentUrl() }}" target="_blank" class="text-blue-700 hover:underline font-medium">{{ $edu->document_name }}</a>
                                                <span class="text-gray-400">({{ $edu->document_size_formatted }})</span>
                                            </div>
                                        @else
                                            <div class="mt-1.5 flex items-center gap-1 text-xs text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span>No document attached</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('counselor-profile.qualifications.delete', $edu) }}" onsubmit="return confirm('Remove this qualification?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 border border-red-200">Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 italic mb-5">No qualifications added yet. Click below to add your first qualification.</p>
            @endif

            <div>
                <button type="button" id="toggle-qual-form" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-700 text-white text-sm font-medium hover:bg-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Qualification
                </button>

                <div id="qual-form" class="mt-4 p-5 bg-gray-50 border border-gray-200" style="display: none;">
                    <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-4">New Qualification</h4>
                    <form method="POST" action="{{ route('counselor-profile.qualifications.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Qualification Level *</label>
                                <select name="degree_level" required
                                        class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select...</option>
                                    @foreach(\App\Models\User::COUNSELOR_QUALIFICATIONS as $key => $label)
                                        <option value="{{ $key }}" {{ old('degree_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('degree_level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">School / University *</label>
                                <input type="text" name="institution" required placeholder="e.g. University of Liberia"
                                       value="{{ old('institution') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('institution') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Program / Degree</label>
                                <input type="text" name="program" placeholder="e.g. Bachelor of Education in Counseling"
                                       value="{{ old('program') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Year Obtained *</label>
                                <input type="number" name="year_obtained" required min="1950" max="{{ date('Y') + 5 }}" placeholder="e.g. 2022"
                                       value="{{ old('year_obtained') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('year_obtained') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Country</label>
                                <input type="text" name="country" placeholder="e.g. Liberia"
                                       value="{{ old('country', 'Liberia') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                                <input type="text" name="notes" placeholder="e.g. Graduated with Honours"
                                       value="{{ old('notes') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mb-4 p-3 bg-white border border-gray-200">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Attach Supporting Document (optional)
                                </span>
                            </label>
                            <input type="file" name="qualification_document" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                                   class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:border file:border-gray-300 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                            <p class="mt-1 text-xs text-gray-400">Upload a copy of your degree, diploma, or certificate for verification. PDF, JPG, PNG, DOC. Max 5MB.</p>
                            @error('qualification_document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-medium hover:bg-blue-600">Save Qualification</button>
                            <button type="button" onclick="document.getElementById('qual-form').style.display='none'" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         ADDITIONAL CERTIFICATES & ACHIEVEMENTS (separate card, outside form)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                Additional Certificates & Achievements
            </h3>
            <span class="text-xs text-gray-400">{{ $counselor->counselorCertificates->count() }} certificate(s)</span>
        </div>
        <div class="p-5">
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
                                            @if($cert->program) <span>{{ $cert->program }}</span> @endif
                                            @if($cert->year_obtained) <span>{{ $cert->year_obtained }}</span> @endif
                                            @if($cert->certificate_number) <span>No: {{ $cert->certificate_number }}</span> @endif
                                            @if($cert->expiry_date)
                                                <span class="{{ $cert->is_expired ? 'text-red-500 font-medium' : 'text-green-600' }}">
                                                    {{ $cert->status_label }} ({{ $cert->expiry_date->format('M Y') }})
                                                </span>
                                            @endif
                                        </div>
                                        @if($cert->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $cert->description }}</p>
                                        @endif
                                        {{-- Document attachment indicator --}}
                                        @if($cert->hasDocument())
                                            <div class="mt-1.5 flex items-center gap-1.5 text-xs">
                                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <a href="{{ $cert->getDocumentUrl() }}" target="_blank" class="text-blue-700 hover:underline font-medium">{{ $cert->document_name }}</a>
                                                <span class="text-gray-400">({{ $cert->document_size_formatted }})</span>
                                            </div>
                                        @else
                                            <div class="mt-1.5 flex items-center gap-1 text-xs text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span>No document attached</span>
                                            </div>
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

            <div>
                <button type="button" id="toggle-cert-form" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-700 text-white text-sm font-medium hover:bg-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Another Certificate
                </button>

                <div id="cert-form" class="mt-4 p-5 bg-gray-50 border border-gray-200" style="display: none;">
                    <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-4">New Certificate / Achievement</h4>
                    <form method="POST" action="{{ route('counselor-profile.certificates.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Certificate / Achievement Name *</label>
                                <input type="text" name="certificate_name" required placeholder="e.g. Certified School Counselor"
                                       value="{{ old('certificate_name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('certificate_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Issuing Institution / School *</label>
                                <input type="text" name="institution" required placeholder="e.g. University of Liberia"
                                       value="{{ old('institution') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('institution') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Program / Course</label>
                                <input type="text" name="program" placeholder="e.g. Counseling Certificate Program"
                                       value="{{ old('program') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Year Obtained</label>
                                <input type="number" name="year_obtained" min="1950" max="{{ date('Y') + 5 }}" placeholder="e.g. 2023"
                                       value="{{ old('year_obtained') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Certificate / Licence Number</label>
                                <input type="text" name="certificate_number" placeholder="Optional"
                                       value="{{ old('certificate_number') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date</label>
                                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Description / Notes</label>
                            <textarea name="description" rows="2" placeholder="Any additional details about this certificate or achievement"
                                      class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-4 p-3 bg-white border border-gray-200">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Attach Supporting Document (optional)
                                </span>
                            </label>
                            <input type="file" name="certificate_document" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                                   class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:border file:border-gray-300 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                            <p class="mt-1 text-xs text-gray-400">Upload a copy of this certificate for verification. PDF, JPG, PNG, DOC. Max 5MB.</p>
                            @error('certificate_document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-medium hover:bg-blue-600">Save Certificate</button>
                            <button type="button" onclick="document.getElementById('cert-form').style.display='none'" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Qualification form toggle
    const toggleQualBtn = document.getElementById('toggle-qual-form');
    const qualForm  = document.getElementById('qual-form');

    toggleQualBtn.addEventListener('click', function () {
        qualForm.style.display = qualForm.style.display === 'none' ? 'block' : 'none';
    });

    @if($errors->hasAny(['degree_level', 'institution', 'year_obtained', 'qualification_document']))
        qualForm.style.display = 'block';
    @endif

    // Certificate form toggle
    const toggleBtn = document.getElementById('toggle-cert-form');
    const certForm  = document.getElementById('cert-form');

    toggleBtn.addEventListener('click', function () {
        certForm.style.display = certForm.style.display === 'none' ? 'block' : 'none';
    });

    @if($errors->hasAny(['certificate_name']))
        certForm.style.display = 'block';
    @endif
});
</script>
@endsection
