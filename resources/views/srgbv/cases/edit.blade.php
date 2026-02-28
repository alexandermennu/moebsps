@extends('layouts.app')

@section('title', 'Edit ' . $case->case_number)
@section('page-title', 'Edit Case ' . $case->case_number)

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('srgbv.cases.show', $case) }}" class="text-xs text-blue-700 hover:underline">Back to Case</a>
    </div>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200">
            <ul class="text-sm text-red-600 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('srgbv.cases.update', $case) }}" class="space-y-6">
        @csrf @method('PUT')

        {{-- Status & Priority --}}
        <div class="bg-white border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-1">Case Status</h3>
            <p class="text-sm text-gray-500 mb-5">Update case status, priority, and assignment.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach(\App\Models\SrgbvCase::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $case->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                    <select name="priority" id="priority" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach(\App\Models\SrgbvCase::PRIORITIES as $key => $label)
                            <option value="{{ $key }}" {{ old('priority', $case->priority) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assigned Counselor</label>
                    <select name="assigned_to" id="assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Unassigned</option>
                        @foreach($counselors as $counselor)
                            <option value="{{ $counselor->id }}" {{ old('assigned_to', $case->assigned_to) == $counselor->id ? 'selected' : '' }}>{{ $counselor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Case Information --}}
        <div class="bg-white border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-5">Case Information</h3>

            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $case->title) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea name="description" id="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('description', $case->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category" id="category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            @foreach(\App\Models\SrgbvCase::CATEGORIES as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $case->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="incident_date" class="block text-sm font-medium text-gray-700 mb-1">Incident Date *</label>
                        <input type="date" name="incident_date" id="incident_date" value="{{ old('incident_date', $case->incident_date->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="incident_location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="incident_location" id="incident_location" value="{{ old('incident_location', $case->incident_location) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label for="incident_description" class="block text-sm font-medium text-gray-700 mb-1">Incident Details</label>
                    <textarea name="incident_description" id="incident_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('incident_description', $case->incident_description) }}</textarea>
                </div>

                <div>
                    <label for="witnesses" class="block text-sm font-medium text-gray-700 mb-1">Witnesses</label>
                    <textarea name="witnesses" id="witnesses" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('witnesses', $case->witnesses) }}</textarea>
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring', $case->is_recurring) ? 'checked' : '' }}
                               class="h-4 w-4 text-red-600 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">Recurring incident</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_confidential" value="1" {{ old('is_confidential', $case->is_confidential) ? 'checked' : '' }}
                               class="h-4 w-4 text-red-600 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">Confidential</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Victim Information --}}
        <div class="bg-white border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-5">Victim Information</h3>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="victim_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="victim_name" id="victim_name" value="{{ old('victim_name', $case->victim_name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="victim_gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="victim_gender" id="victim_gender"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select</option>
                            <option value="Male" {{ old('victim_gender', $case->victim_gender) === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('victim_gender', $case->victim_gender) === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('victim_gender', $case->victim_gender) === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="victim_age" class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                        <input type="number" name="victim_age" id="victim_age" value="{{ old('victim_age', $case->victim_age) }}" min="1" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="victim_grade" class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
                        <input type="text" name="victim_grade" id="victim_grade" value="{{ old('victim_grade', $case->victim_grade) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="victim_school" class="block text-sm font-medium text-gray-700 mb-1">School</label>
                        <input type="text" name="victim_school" id="victim_school" value="{{ old('victim_school', $case->victim_school) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="victim_contact" class="block text-sm font-medium text-gray-700 mb-1">Victim Contact</label>
                        <input type="text" name="victim_contact" id="victim_contact" value="{{ old('victim_contact', $case->victim_contact) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="victim_parent_guardian" class="block text-sm font-medium text-gray-700 mb-1">Parent / Guardian</label>
                        <input type="text" name="victim_parent_guardian" id="victim_parent_guardian" value="{{ old('victim_parent_guardian', $case->victim_parent_guardian) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label for="victim_parent_contact" class="block text-sm font-medium text-gray-700 mb-1">Parent Contact</label>
                    <input type="text" name="victim_parent_contact" id="victim_parent_contact" value="{{ old('victim_parent_contact', $case->victim_parent_contact) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500 max-w-md">
                </div>
            </div>
        </div>

        {{-- Perpetrator --}}
        <div class="bg-white border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-5">Perpetrator Information</h3>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="perpetrator_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="perpetrator_name" id="perpetrator_name" value="{{ old('perpetrator_name', $case->perpetrator_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="perpetrator_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="perpetrator_type" id="perpetrator_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Select</option>
                            @foreach(\App\Models\SrgbvCase::PERPETRATOR_TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('perpetrator_type', $case->perpetrator_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="perpetrator_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="perpetrator_description" id="perpetrator_description" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('perpetrator_description', $case->perpetrator_description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Risk & Safety --}}
        <div class="bg-white border border-red-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-5">Risk Assessment</h3>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-1">Risk Level</label>
                        <select name="risk_level" id="risk_level"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">None</option>
                            @foreach(\App\Models\SrgbvCase::RISK_LEVELS as $key => $label)
                                <option value="{{ $key }}" {{ old('risk_level', $case->risk_level) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="immediate_action_required" value="1" {{ old('immediate_action_required', $case->immediate_action_required) ? 'checked' : '' }}
                                   class="h-4 w-4 text-red-600 border-gray-300 rounded">
                            <span class="text-sm text-red-700 font-medium">Immediate action required</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="safety_plan" class="block text-sm font-medium text-gray-700 mb-1">Safety Plan</label>
                    <textarea name="safety_plan" id="safety_plan" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('safety_plan', $case->safety_plan) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Resolution & Referral --}}
        <div class="bg-white border border-green-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-5">Resolution & Referral</h3>

            <div class="space-y-4">
                <div>
                    <label for="resolution" class="block text-sm font-medium text-gray-700 mb-1">Resolution</label>
                    <textarea name="resolution" id="resolution" rows="3"
                              placeholder="Describe how the case was resolved..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('resolution', $case->resolution) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="resolution_date" class="block text-sm font-medium text-gray-700 mb-1">Resolution Date</label>
                        <input type="date" name="resolution_date" id="resolution_date" value="{{ old('resolution_date', $case->resolution_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="referral_agency" class="block text-sm font-medium text-gray-700 mb-1">Referral Agency</label>
                        <input type="text" name="referral_agency" id="referral_agency" value="{{ old('referral_agency', $case->referral_agency) }}"
                               placeholder="e.g., Police, Hospital, NGO..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label for="referral_details" class="block text-sm font-medium text-gray-700 mb-1">Referral Details</label>
                    <textarea name="referral_details" id="referral_details" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('referral_details', $case->referral_details) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="follow_up_required" value="1" {{ old('follow_up_required', $case->follow_up_required) ? 'checked' : '' }}
                               class="h-4 w-4 text-red-600 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">Follow-up required</span>
                    </label>
                    <div>
                        <label for="follow_up_date" class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date</label>
                        <input type="date" name="follow_up_date" id="follow_up_date" value="{{ old('follow_up_date', $case->follow_up_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-red-700 text-white text-sm font-medium hover:bg-red-800">Save Changes</button>
            <a href="{{ route('srgbv.cases.show', $case) }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
