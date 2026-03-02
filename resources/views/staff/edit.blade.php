@extends('layouts.app')

@section('title', 'Edit Staff')
@section('page-title', 'Edit Staff Member')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('staff.index') }}" class="text-xs text-blue-700 hover:underline">Back to Staff</a>
    </div>

    <div class="bg-white border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-6">Edit: {{ $staff->name }}</h2>
        <p class="text-sm text-gray-500 mb-6">Update staff member details and access level.</p>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200">
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('staff.update', $staff) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $staff->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $staff->email) }}" required
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

            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Access Level / Role *</label>
                <select name="role" id="role" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}" {{ old('role', $staff->role) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">
                    <strong>Supervisor / Coordinator</strong> — Can view division activities (read-only).<br>
                    <strong>Counselor</strong> — School counselor (automatically assigned to CGPC).
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $staff->position) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $staff->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>

            {{-- Profile Photo --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                <div class="flex items-center gap-4">
                    <x-user-avatar :user="$staff" size="lg" />
                    <div class="flex-1">
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/webp"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border file:border-gray-300 file:text-sm file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                        <p class="mt-1 text-xs text-gray-400">JPG, PNG or WebP. Max 2MB.</p>
                        @if($staff->hasProfilePhoto())
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
                        <input type="text" name="counselor_school" id="counselor_school" value="{{ old('counselor_school', $staff->counselor_school) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label for="counselor_county" class="block text-sm font-medium text-gray-700 mb-1">County *</label>
                        <select name="counselor_county" id="counselor_county"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select County...</option>
                            @foreach(\App\Models\User::COUNTIES as $county)
                                <option value="{{ $county }}" {{ old('counselor_county', $staff->counselor_county) === $county ? 'selected' : '' }}>{{ $county }}</option>
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
                                <option value="{{ $key }}" {{ old('counselor_status', $staff->counselor_status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="counselor_school_phone" class="block text-sm font-medium text-gray-700 mb-1">School Phone</label>
                        <input type="text" name="counselor_school_phone" id="counselor_school_phone" value="{{ old('counselor_school_phone', $staff->counselor_school_phone) }}"
                               placeholder="+231-xxx-xxx-xxxx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>

                {{-- Personal Information --}}
                <h3 class="text-sm font-semibold text-blue-800 mb-3 mt-4 pt-3 border-t border-blue-200">Personal Information</h3>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $staff->date_of_birth?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::GENDERS as $key => $label)
                                <option value="{{ $key }}" {{ old('gender', $staff->gender) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $staff->nationality) }}" placeholder="e.g. Liberian"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Residential Address</label>
                        <input type="text" name="address" value="{{ old('address', $staff->address) }}" placeholder="Street address"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City / Town</label>
                        <input type="text" name="city" value="{{ old('city', $staff->city) }}" placeholder="e.g. Monrovia"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $staff->emergency_contact_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $staff->emergency_contact_phone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Relationship</label>
                        <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $staff->emergency_contact_relationship) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>

                {{-- School & Assignment Details --}}
                <h3 class="text-sm font-semibold text-blue-800 mb-3 mt-4 pt-3 border-t border-blue-200">School & Assignment Details</h3>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assignment / Start Date</label>
                        <input type="date" name="counselor_assignment_date" value="{{ old('counselor_assignment_date', $staff->counselor_assignment_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School District</label>
                        <input type="text" name="counselor_school_district" value="{{ old('counselor_school_district', $staff->counselor_school_district) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Address</label>
                        <input type="text" name="counselor_school_address" value="{{ old('counselor_school_address', $staff->counselor_school_address) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Principal / Head Teacher</label>
                        <input type="text" name="counselor_school_principal" value="{{ old('counselor_school_principal', $staff->counselor_school_principal) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Level</label>
                        <select name="counselor_school_level" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::SCHOOL_LEVELS as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_school_level', $staff->counselor_school_level) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Type</label>
                        <select name="counselor_school_type" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select...</option>
                            @foreach(\App\Models\User::SCHOOL_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('counselor_school_type', $staff->counselor_school_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student Population</label>
                        <input type="number" name="counselor_school_population" value="{{ old('counselor_school_population', $staff->counselor_school_population) }}" min="0" max="50000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. of Boys</label>
                        <input type="number" name="counselor_num_boys" value="{{ old('counselor_num_boys', $staff->counselor_num_boys) }}" min="0" max="50000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. of Girls</label>
                        <input type="number" name="counselor_num_girls" value="{{ old('counselor_num_girls', $staff->counselor_num_girls) }}" min="0" max="50000"
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
                                <option value="{{ $key }}" {{ old('counselor_specialization', $staff->counselor_specialization) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="counselor_years_experience" class="block text-sm font-medium text-gray-700 mb-1">Years Experience</label>
                        <input type="number" name="counselor_years_experience" id="counselor_years_experience" value="{{ old('counselor_years_experience', $staff->counselor_years_experience) }}"
                               min="0" max="50" placeholder="e.g. 5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                </div>
                @if($staff->counselor_qualification)
                    <p class="text-xs text-gray-500 mb-1">Highest Qualification: <strong>{{ \App\Models\User::COUNSELOR_QUALIFICATIONS[$staff->counselor_qualification] ?? ucfirst($staff->counselor_qualification) }}</strong> <span class="text-gray-400">(auto-synced from profile)</span></p>
                @endif
                <p class="text-xs text-blue-600 italic">Qualifications & education records are managed from the counselor's own profile page.</p>
            </div>

            @if($staff->isPending())
                <div class="mb-6 p-3 bg-amber-50 border border-amber-200 text-sm text-amber-700">
                    <strong>Pending Approval</strong> — This account is awaiting administrator approval. Active status cannot be changed until approved.
                </div>
            @elseif($staff->isRejected())
                <div class="mb-6 p-3 bg-red-50 border border-red-200 text-sm text-red-700">
                    <strong>Rejected</strong> — {{ $staff->rejection_reason ?? 'This account was rejected by an administrator.' }}
                </div>
            @endif

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">Update Staff</button>
                <a href="{{ route('staff.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const counselorFields = document.getElementById('counselor-fields');

        function toggleCounselorFields() {
            counselorFields.style.display = roleSelect.value === 'counselor' ? 'block' : 'none';
        }

        roleSelect.addEventListener('change', toggleCounselorFields);
        toggleCounselorFields();
    });
</script>
@endsection
