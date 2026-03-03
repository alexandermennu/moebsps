@extends('layouts.app')
@section('title', 'Edit ' . $incident->incident_number)
@section('page-title', 'Edit Incident')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('sir.incidents.show', $incident) }}" class="text-xs text-blue-700 hover:underline">← Back to {{ $incident->incident_number }}</a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 p-4 rounded-md">
        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('sir.incidents.update', $incident) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        {{-- 1. Classification --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">1. Classification</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Incident Type <span class="text-red-500">*</span></label>
                    <select name="type" id="incident_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach(\App\Models\Incident::TYPES as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $incident->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category" id="incident_category" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Select category...</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $incident->title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('description', $incident->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Incident Date <span class="text-red-500">*</span></label>
                    <input type="date" name="incident_date" value="{{ old('incident_date', $incident->incident_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach(\App\Models\Incident::PRIORITIES as $key => $label)
                        <option value="{{ $key }}" {{ old('priority', $incident->priority) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- 2. Status & Resolution --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">2. Status & Resolution</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach(\App\Models\Incident::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $incident->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Resolution Date</label>
                    <input type="date" name="resolution_date" value="{{ old('resolution_date', $incident->resolution_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Resolution Notes</label>
                    <textarea name="resolution" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Describe the resolution...">{{ old('resolution', $incident->resolution) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 3. School Information --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">3. School Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">School Name</label>
                    <input type="text" name="school_name" value="{{ old('school_name', $incident->school_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">School Level</label>
                    <select name="school_level" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Select level...</option>
                        @foreach(\App\Models\Incident::SCHOOL_LEVELS as $key => $label)
                        <option value="{{ $key }}" {{ old('school_level', $incident->school_level) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">County</label>
                    <select name="school_county" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Select county...</option>
                        @foreach(\App\Models\User::COUNTIES as $county)
                        <option value="{{ $county }}" {{ old('school_county', $incident->school_county) === $county ? 'selected' : '' }}>{{ $county }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">District</label>
                    <input type="text" name="school_district" value="{{ old('school_district', $incident->school_district) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Specific Location in School</label>
                    <input type="text" name="incident_location" value="{{ old('incident_location', $incident->incident_location) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="e.g., Classroom B3, playground, etc.">
                </div>
            </div>
        </div>

        {{-- 4. Affected Person --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">4. Affected Person</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                    <input type="text" name="victim_name" value="{{ old('victim_name', $incident->victim_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Age</label>
                    <input type="number" name="victim_age" value="{{ old('victim_age', $incident->victim_age) }}" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Gender</label>
                    <select name="victim_gender" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Select...</option>
                        <option value="male" {{ old('victim_gender', $incident->victim_gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('victim_gender', $incident->victim_gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Grade</label>
                    <input type="text" name="victim_grade" value="{{ old('victim_grade', $incident->victim_grade) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Contact</label>
                    <input type="text" name="victim_contact" value="{{ old('victim_contact', $incident->victim_contact) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Parent/Guardian</label>
                    <input type="text" name="victim_parent_guardian" value="{{ old('victim_parent_guardian', $incident->victim_parent_guardian) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Parent Contact</label>
                    <input type="text" name="victim_parent_contact" value="{{ old('victim_parent_contact', $incident->victim_parent_contact) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>
        </div>

        {{-- 5. Perpetrator --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">5. Perpetrator</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Perpetrator Name</label>
                    <input type="text" name="perpetrator_name" value="{{ old('perpetrator_name', $incident->perpetrator_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Perpetrator Type</label>
                    <select name="perpetrator_type" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Select...</option>
                        @foreach(\App\Models\Incident::PERPETRATOR_TYPES as $key => $label)
                        <option value="{{ $key }}" {{ old('perpetrator_type', $incident->perpetrator_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea name="perpetrator_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('perpetrator_description', $incident->perpetrator_description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 6. Additional Details --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">6. Additional Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Detailed Account</label>
                    <textarea name="incident_description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('incident_description', $incident->incident_description) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Witnesses</label>
                    <textarea name="witnesses" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('witnesses', $incident->witnesses) }}</textarea>
                </div>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_confidential" value="0">
                        <input type="checkbox" name="is_confidential" value="1" {{ old('is_confidential', $incident->is_confidential) ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-700">Confidential</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_recurring" value="0">
                        <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring', $incident->is_recurring) ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-700">Recurring Incident</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- 7. Risk & Follow-Up --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">7. Risk & Follow-Up</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Risk Level</label>
                    <select name="risk_level" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Not assessed</option>
                        @foreach(\App\Models\Incident::RISK_LEVELS as $key => $label)
                        <option value="{{ $key }}" {{ old('risk_level', $incident->risk_level) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="flex items-center gap-2 mt-6">
                        <input type="hidden" name="immediate_action_required" value="0">
                        <input type="checkbox" name="immediate_action_required" value="1" {{ old('immediate_action_required', $incident->immediate_action_required) ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-700 font-medium">Immediate Action Required</span>
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Safety Plan</label>
                    <textarea name="safety_plan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('safety_plan', $incident->safety_plan) }}</textarea>
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="follow_up_required" value="0">
                        <input type="checkbox" name="follow_up_required" value="1" {{ old('follow_up_required', $incident->follow_up_required) ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-700">Follow-Up Required</span>
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Follow-Up Date</label>
                    <input type="date" name="follow_up_date" value="{{ old('follow_up_date', $incident->follow_up_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>
        </div>

        {{-- 8. Referral --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">8. Referral</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Referral Agency</label>
                    <input type="text" name="referral_agency" value="{{ old('referral_agency', $incident->referral_agency) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="e.g., SGBV Crimes Unit, MOGCSP, hospital">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Referral Details</label>
                    <input type="text" name="referral_details" value="{{ old('referral_details', $incident->referral_details) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Contact person, reference number, etc.">
                </div>
            </div>
        </div>

        {{-- 9. Assignment --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">9. Assignment</h3>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Assigned Counselor</label>
                <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Unassigned</option>
                    @foreach($counselors as $counselor)
                    <option value="{{ $counselor->id }}" {{ old('assigned_to', $incident->assigned_to) == $counselor->id ? 'selected' : '' }}>{{ $counselor->name }} ({{ $counselor->county ?? 'No county' }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('sir.incidents.show', $incident) }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-red-700 text-white text-sm font-medium hover:bg-red-800 rounded-md">Update Incident</button>
        </div>
    </form>
</div>

<script>
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
</script>
@endsection
