@extends('layouts.app')
@section('title', 'Report Incident')
@section('page-title', 'Report Incident')
@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('sir.incidents.index') }}" class="text-xs text-blue-700 hover:underline">← Back to Incidents</a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
        <p class="text-sm font-medium text-red-800">Please correct the following errors:</p>
        <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('sir.incidents.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Section 1: Incident Type & Classification --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Incident Classification</h3>
            <p class="text-sm text-gray-500 mb-5">Select the type of incident being reported.</p>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Incident Type *</label>
                        <select name="type" id="incident-type" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select Type</option>
                            @foreach(\App\Models\Incident::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $selectedType) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category" id="incident-category" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select Type First</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                        <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            @foreach(\App\Models\Incident::PRIORITIES as $key => $label)
                            <option value="{{ $key }}" {{ old('priority', 'medium') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Incident Date *</label>
                        <input type="date" name="incident_date" value="{{ old('incident_date', now()->format('Y-m-d')) }}" required max="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Brief summary of the incident" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea name="description" rows="4" required placeholder="Detailed description of the incident..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 2: School Information --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">School Information</h3>
            <p class="text-sm text-gray-500 mb-5">Where did the incident occur?</p>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Name</label>
                        <input type="text" name="school_name" value="{{ old('school_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Level</label>
                        <select name="school_level" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select Level</option>
                            @foreach(\App\Models\Incident::SCHOOL_LEVELS as $key => $label)
                            <option value="{{ $key }}" {{ old('school_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">County</label>
                        <select name="school_county" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select County</option>
                            @foreach(\App\Models\User::COUNTIES as $county)
                            <option value="{{ $county }}" {{ old('school_county') === $county ? 'selected' : '' }}>{{ $county }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                        <input type="text" name="school_district" value="{{ old('school_district') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Incident Location</label>
                        <input type="text" name="incident_location" value="{{ old('incident_location') }}" placeholder="e.g., Classroom, playground" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Affected Person / Victim (shown for all types but required only for SRGBV) --}}
        <div class="bg-white border border-gray-200 rounded-md p-6" id="victim-section">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Affected Person</h3>
            <p class="text-sm text-gray-500 mb-5">Information about the person affected by this incident. <span id="victim-required-note" class="text-red-600 hidden">* Required for SRGBV cases.</span></p>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="victim-required-star text-red-600 hidden">*</span></label>
                        <input type="text" name="victim_name" value="{{ old('victim_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                        <input type="number" name="victim_age" value="{{ old('victim_age') }}" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="victim_gender" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select</option>
                            <option value="male" {{ old('victim_gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('victim_gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('victim_gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
                        <input type="text" name="victim_grade" value="{{ old('victim_grade') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact</label>
                        <input type="text" name="victim_contact" value="{{ old('victim_contact') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent/Guardian Name</label>
                        <input type="text" name="victim_parent_guardian" value="{{ old('victim_parent_guardian') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent/Guardian Contact</label>
                        <input type="text" name="victim_parent_contact" value="{{ old('victim_parent_contact') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Perpetrator --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Perpetrator Information</h3>
            <p class="text-sm text-gray-500 mb-5">If a perpetrator is known or suspected (optional).</p>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Perpetrator Name</label>
                        <input type="text" name="perpetrator_name" value="{{ old('perpetrator_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Perpetrator Type</label>
                        <select name="perpetrator_type" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select</option>
                            @foreach(\App\Models\Incident::PERPETRATOR_TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('perpetrator_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="perpetrator_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('perpetrator_description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 5: Additional Details --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Additional Details</h3>
            <p class="text-sm text-gray-500 mb-5">Witnesses, incident description, and other context.</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detailed Incident Description</label>
                    <textarea name="incident_description" rows="3" placeholder="Step-by-step account of what happened..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('incident_description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Witnesses</label>
                    <textarea name="witnesses" rows="2" placeholder="Names and contact information of witnesses..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('witnesses') }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <label class="text-sm text-gray-700">This is a recurring incident</label>
                </div>
            </div>
        </div>

        {{-- Section 6: Risk Assessment --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Risk Assessment</h3>
            <p class="text-sm text-gray-500 mb-5">Assess the risk level and urgency.</p>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Risk Level</label>
                        <select name="risk_level" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Assess Risk</option>
                            @foreach(\App\Models\Incident::RISK_LEVELS as $key => $label)
                            <option value="{{ $key }}" {{ old('risk_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="immediate_action_required" value="1" {{ old('immediate_action_required') ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-sm text-gray-700">Immediate action required</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_confidential" value="1" {{ old('is_confidential', '1') ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-sm text-gray-700">Confidential</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Safety Plan</label>
                    <textarea name="safety_plan" rows="2" placeholder="Any immediate safety measures taken or recommended..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('safety_plan') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 7: Assignment --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Assignment</h3>
            <p class="text-sm text-gray-500 mb-5">Assign this incident to a counselor for follow-up.</p>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Unassigned</option>
                    @foreach($counselors as $counselor)
                    <option value="{{ $counselor->id }}" {{ old('assigned_to') == $counselor->id ? 'selected' : '' }}>{{ $counselor->name }} ({{ $counselor->counselor_county ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Section 8: File Uploads --}}
        <div class="bg-white border border-gray-200 rounded-md p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Supporting Documents</h3>
            <p class="text-sm text-gray-500 mb-5">Upload any evidence, photos, or reports (max 10MB each).</p>
            <input type="file" name="files[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('sir.incidents.index') }}" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-700 text-white text-sm font-medium hover:bg-red-800 rounded-md">Submit Incident Report</button>
        </div>
    </form>
</div>

{{-- Dynamic Category Script --}}
<script>
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
</script>
@endsection
