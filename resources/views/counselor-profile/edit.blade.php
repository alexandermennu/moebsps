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
                <p class="text-sm text-gray-500">Edit your counselor qualifications, experience, and training information.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('counselor-profile.update') }}">
            @csrf
            @method('PUT')

            {{-- Qualifications Section --}}
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Qualifications & Experience
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="counselor_qualification" class="block text-sm font-medium text-gray-700 mb-1">Highest Qualification</label>
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
            </div>

            {{-- School Contact --}}
            <div class="mb-6">
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

            {{-- Training & Development --}}
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Training & Development
                </h3>
                <div>
                    <label for="counselor_training" class="block text-sm font-medium text-gray-700 mb-1">Training & Certifications Completed</label>
                    <textarea name="counselor_training" id="counselor_training" rows="5"
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
</div>
@endsection
