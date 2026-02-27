@extends('layouts.app')

@section('title', 'Report SRGBV Case')
@section('page-title', 'Report SRGBV Case')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('srgbv.cases.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Cases</a>
    </div>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
            <ul class="text-sm text-red-600 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('srgbv.cases.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Section 1: Case Information --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Case Information</h3>
            <p class="text-sm text-gray-500 mb-5">Provide details about the incident being reported.</p>

            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Case Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           placeholder="Brief description of the case"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Detailed Description *</label>
                    <textarea name="description" id="description" rows="4" required
                              placeholder="Provide a detailed account of the incident..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category" id="category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select category</option>
                            @foreach(\App\Models\SrgbvCase::CATEGORIES as $key => $label)
                                <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority Level *</label>
                        <select name="priority" id="priority" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            @foreach(\App\Models\SrgbvCase::PRIORITIES as $key => $label)
                                <option value="{{ $key }}" {{ old('priority', 'medium') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="incident_date" class="block text-sm font-medium text-gray-700 mb-1">Incident Date *</label>
                        <input type="date" name="incident_date" id="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="incident_location" class="block text-sm font-medium text-gray-700 mb-1">Incident Location</label>
                        <input type="text" name="incident_location" id="incident_location" value="{{ old('incident_location') }}"
                               placeholder="Where did the incident occur?"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div class="flex items-end gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}
                                   class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-sm text-gray-700">Recurring incident</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_confidential" value="1" {{ old('is_confidential', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-sm text-gray-700">Confidential</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="incident_description" class="block text-sm font-medium text-gray-700 mb-1">Additional Incident Details</label>
                    <textarea name="incident_description" id="incident_description" rows="3"
                              placeholder="Any additional context about the incident..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('incident_description') }}</textarea>
                </div>

                <div>
                    <label for="witnesses" class="block text-sm font-medium text-gray-700 mb-1">Witnesses</label>
                    <textarea name="witnesses" id="witnesses" rows="2"
                              placeholder="Names and contact info of any witnesses..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('witnesses') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 2: Victim Information --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Victim Information</h3>
            <p class="text-sm text-gray-500 mb-5">Details about the affected individual.</p>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="victim_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="victim_name" id="victim_name" value="{{ old('victim_name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="victim_gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="victim_gender" id="victim_gender"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select</option>
                            <option value="Male" {{ old('victim_gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('victim_gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('victim_gender') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="victim_age" class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                        <input type="number" name="victim_age" id="victim_age" value="{{ old('victim_age') }}" min="1" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="victim_grade" class="block text-sm font-medium text-gray-700 mb-1">Grade / Class</label>
                        <input type="text" name="victim_grade" id="victim_grade" value="{{ old('victim_grade') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="victim_school" class="block text-sm font-medium text-gray-700 mb-1">School</label>
                        <input type="text" name="victim_school" id="victim_school" value="{{ old('victim_school') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="victim_contact" class="block text-sm font-medium text-gray-700 mb-1">Victim Contact</label>
                        <input type="text" name="victim_contact" id="victim_contact" value="{{ old('victim_contact') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="victim_parent_guardian" class="block text-sm font-medium text-gray-700 mb-1">Parent / Guardian Name</label>
                        <input type="text" name="victim_parent_guardian" id="victim_parent_guardian" value="{{ old('victim_parent_guardian') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label for="victim_parent_contact" class="block text-sm font-medium text-gray-700 mb-1">Parent / Guardian Contact</label>
                    <input type="text" name="victim_parent_contact" id="victim_parent_contact" value="{{ old('victim_parent_contact') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500 max-w-md">
                </div>
            </div>
        </div>

        {{-- Section 3: Perpetrator Information --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Perpetrator Information</h3>
            <p class="text-sm text-gray-500 mb-5">Details about the alleged perpetrator (if known).</p>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="perpetrator_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="perpetrator_name" id="perpetrator_name" value="{{ old('perpetrator_name') }}"
                               placeholder="Name or alias if known"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="perpetrator_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="perpetrator_type" id="perpetrator_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select type</option>
                            @foreach(\App\Models\SrgbvCase::PERPETRATOR_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('perpetrator_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="perpetrator_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="perpetrator_description" id="perpetrator_description" rows="2"
                              placeholder="Physical description, relationship to victim, etc."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('perpetrator_description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 4: Risk Assessment --}}
        <div class="bg-white rounded-lg border border-red-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Risk Assessment</h3>
            <p class="text-sm text-gray-500 mb-5">Assess the risk level and immediate needs.</p>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-1">Risk Level</label>
                        <select name="risk_level" id="risk_level"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Assess risk level</option>
                            @foreach(\App\Models\SrgbvCase::RISK_LEVELS as $key => $label)
                                <option value="{{ $key }}" {{ old('risk_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="immediate_action_required" value="1" {{ old('immediate_action_required') ? 'checked' : '' }}
                                   class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-sm text-red-700 font-medium">⚠ Immediate action required</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="safety_plan" class="block text-sm font-medium text-gray-700 mb-1">Safety Plan</label>
                    <textarea name="safety_plan" id="safety_plan" rows="3"
                              placeholder="Describe any immediate safety measures needed..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('safety_plan') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 5: Assignment --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Case Assignment</h3>
            <p class="text-sm text-gray-500 mb-5">Assign this case to a counselor for follow-up.</p>

            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assign to Counselor</label>
                <select name="assigned_to" id="assigned_to"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500 max-w-md">
                    <option value="">Unassigned</option>
                    @foreach($counselors as $counselor)
                        <option value="{{ $counselor->id }}" {{ old('assigned_to') == $counselor->id ? 'selected' : '' }}>
                            {{ $counselor->name }} ({{ $counselor->division?->name ?? 'No Division' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Section 6: File Uploads --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Supporting Evidence</h3>
            <p class="text-sm text-gray-500 mb-5">Upload photos, documents, or other evidence files (max 10MB each).</p>

            <div id="file-upload-container">
                <div class="file-upload-row flex gap-3 items-start mb-3">
                    <div class="flex-1">
                        <input type="file" name="files[]" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                    </div>
                    <div>
                        <select name="file_categories[]" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            @foreach(\App\Models\SrgbvCase::FILE_CATEGORIES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <input type="text" name="file_descriptions[]" placeholder="File description..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                </div>
            </div>

            <button type="button" onclick="addFileRow()" class="mt-2 text-sm text-red-600 hover:text-red-800">
                + Add another file
            </button>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-red-700 text-white text-sm font-medium rounded-md hover:bg-red-800">
                Submit Report
            </button>
            <a href="{{ route('srgbv.cases.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function addFileRow() {
    const container = document.getElementById('file-upload-container');
    const row = document.createElement('div');
    row.className = 'file-upload-row flex gap-3 items-start mb-3';
    row.innerHTML = `
        <div class="flex-1">
            <input type="file" name="files[]" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
        </div>
        <div>
            <select name="file_categories[]" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                @foreach(\App\Models\SrgbvCase::FILE_CATEGORIES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <input type="text" name="file_descriptions[]" placeholder="File description..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 mt-2">✕</button>
    `;
    container.appendChild(row);
}
</script>
@endsection
