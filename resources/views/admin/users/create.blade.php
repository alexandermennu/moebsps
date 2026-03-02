@extends('layouts.app')

@section('title', 'Add User')
@section('page-title', 'Add New User')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-700 hover:underline">Back to Users</a>
    </div>

    <div class="bg-white border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-6">Create New User</h2>

        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select name="role" id="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ old('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="division_id" class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                    <select name="division_id" id="division_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        <option value="">No Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text" name="position" id="position" value="{{ old('position') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>

            {{-- Profile Photo --}}
            <div class="mb-4">
                <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border file:border-gray-300 file:text-sm file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                <p class="mt-1 text-xs text-gray-400">JPG, PNG or WebP. Max 2MB.</p>
            </div>

            {{-- Counselor-specific Fields --}}
            <div id="counselor-fields" class="mb-4 p-4 bg-blue-50 border border-blue-200" style="display: none;">
                <h3 class="text-sm font-semibold text-blue-800 mb-3">Counselor Assignment</h3>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label for="counselor_school" class="block text-sm font-medium text-gray-700 mb-1">School of Assignment *</label>
                        <input type="text" name="counselor_school" id="counselor_school" value="{{ old('counselor_school') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label for="counselor_county" class="block text-sm font-medium text-gray-700 mb-1">County *</label>
                        <select name="counselor_county" id="counselor_county"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select County...</option>
                            @foreach(\App\Models\User::COUNTIES as $county)
                                <option value="{{ $county }}" {{ old('counselor_county') === $county ? 'selected' : '' }}>{{ $county }}</option>
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
                                <option value="{{ $key }}" {{ old('counselor_status', 'active') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="counselor_appointed_at" class="block text-sm font-medium text-gray-700 mb-1">Appointment Date</label>
                        <input type="date" name="counselor_appointed_at" id="counselor_appointed_at" value="{{ old('counselor_appointed_at') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>

                {{-- Personal Information --}}
                <h3 class="text-sm font-semibold text-blue-800 mb-3 mt-4 pt-3 border-t border-blue-200">Personal Information</h3>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::GENDERS as $key => $label)
                                <option value="{{ $key }}" {{ old('gender') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                        <input type="text" name="nationality" value="{{ old('nationality') }}" placeholder="e.g. Liberian"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Residential Address</label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="Street address"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City / Town</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Monrovia"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Relationship</label>
                        <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>

                {{-- Assignment Details --}}
                <h3 class="text-sm font-semibold text-blue-800 mb-3 mt-4 pt-3 border-t border-blue-200">School & Assignment Details</h3>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assignment / Start Date</label>
                        <input type="date" name="counselor_assignment_date" value="{{ old('counselor_assignment_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School District</label>
                        <input type="text" name="counselor_school_district" value="{{ old('counselor_school_district') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Address</label>
                        <input type="text" name="counselor_school_address" value="{{ old('counselor_school_address') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Principal / Head Teacher</label>
                        <input type="text" name="counselor_school_principal" value="{{ old('counselor_school_principal') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Level</label>
                        <select name="counselor_school_level" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::SCHOOL_LEVELS as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_school_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Type</label>
                        <select name="counselor_school_type" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::SCHOOL_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_school_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student Population</label>
                        <input type="number" name="counselor_school_population" value="{{ old('counselor_school_population') }}" min="0" max="50000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. of Boys</label>
                        <input type="number" name="counselor_num_boys" value="{{ old('counselor_num_boys') }}" min="0" max="50000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. of Girls</label>
                        <input type="number" name="counselor_num_girls" value="{{ old('counselor_num_girls') }}" min="0" max="50000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-blue-800 mb-3 mt-4 pt-3 border-t border-blue-200">Experience & Specialization</h3>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label for="counselor_specialization" class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                        <select name="counselor_specialization" id="counselor_specialization"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::COUNSELOR_SPECIALIZATIONS as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_specialization') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="counselor_years_experience" class="block text-sm font-medium text-gray-700 mb-1">Years Experience</label>
                        <input type="number" name="counselor_years_experience" id="counselor_years_experience" value="{{ old('counselor_years_experience') }}"
                               min="0" max="50" placeholder="e.g. 5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <p class="text-xs text-blue-600 italic mb-3">Qualifications & education records are managed from the counselor's own profile page.</p>
                <div class="mb-3">
                    <label for="counselor_school_phone" class="block text-sm font-medium text-gray-700 mb-1">School Phone</label>
                    <input type="text" name="counselor_school_phone" id="counselor_school_phone" value="{{ old('counselor_school_phone') }}"
                           placeholder="+231-xxx-xxx-xxxx"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="counselor_training" class="block text-sm font-medium text-gray-700 mb-1">Training & Certifications</label>
                    <textarea name="counselor_training" id="counselor_training" rows="3"
                              placeholder="List relevant training programs and certifications..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">{{ old('counselor_training') }}</textarea>
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-slate-600 border-gray-300 rounded focus:ring-slate-500">
                    <span class="text-sm text-gray-700">Active account</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">Create User</button>
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

        // Ensure disabled select still submits — add hidden input
        divisionSelect.closest('form').addEventListener('submit', function () {
            if (divisionSelect.disabled) {
                divisionSelect.disabled = false;
            }
        });
    });
</script>
@endsection
