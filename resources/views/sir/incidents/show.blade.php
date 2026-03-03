@extends('layouts.app')
@section('title', $incident->incident_number)
@section('page-title', 'Incident ' . $incident->incident_number)
@section('content')
<div class="space-y-6">
    {{-- Back & Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('sir.incidents.index') }}" class="text-xs text-blue-700 hover:underline">← Back to Incidents</a>
        <div class="flex gap-2">
            @if($canManage)
            <a href="{{ route('sir.incidents.edit', $incident) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 rounded-md">Edit</a>
            <form method="POST" action="{{ route('sir.incidents.destroy', $incident) }}" onsubmit="return confirm('Permanently delete this incident? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="px-4 py-2 bg-white border border-red-300 text-red-600 text-sm font-medium hover:bg-red-50 rounded-md">Delete</button>
            </form>
            @endif
        </div>
    </div>

    {{-- Incident Header --}}
    <div class="bg-white border border-gray-200 rounded-md p-6">
        <div class="flex flex-wrap items-center gap-1.5 mb-3">
            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-gray-100 text-gray-600 rounded">{{ $incident->incident_number }}</span>
            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->type_color }}-100 text-{{ $incident->type_color }}-700 rounded">{{ $incident->type_label }}</span>
            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->priority_color }}-100 text-{{ $incident->priority_color }}-700 rounded">{{ $incident->priority_label }}</span>
            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->status_color }}-100 text-{{ $incident->status_color }}-700 rounded">{{ $incident->status_label }}</span>
            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-{{ $incident->source_color }}-100 text-{{ $incident->source_color }}-700 rounded">{{ $incident->source_label }}</span>
            @if($incident->is_confidential)
            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-purple-100 text-purple-700 rounded">Confidential</span>
            @endif
            @if($incident->immediate_action_required)
            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-red-500 text-white rounded">URGENT</span>
            @endif
        </div>
        <h2 class="text-lg font-semibold text-gray-900">{{ $incident->title }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $incident->category_label }} · {{ $incident->incident_date->format('M d, Y') }} · Reported {{ $incident->created_at->diffForHumans() }} ({{ $incident->days_since_reported }} days ago)</p>

        {{-- Quick Status Change --}}
        @if($canManage && $incident->isOpen())
        <div class="mt-4 pt-4 border-t border-gray-100">
            <form method="POST" action="{{ route('sir.incidents.status', $incident) }}" class="flex items-center gap-3">
                @csrf @method('PATCH')
                <label class="text-xs font-medium text-gray-500 uppercase">Quick Status:</label>
                <select name="status" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    @foreach(\App\Models\Incident::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ $incident->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-1.5 bg-slate-800 text-white text-sm hover:bg-slate-700 rounded-md">Update</button>
            </form>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Description --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Description</h3>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $incident->description }}</p>
                @if($incident->incident_description)
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-4 mb-2">Detailed Account</h4>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $incident->incident_description }}</p>
                @endif
                @if($incident->witnesses)
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-4 mb-2">Witnesses</h4>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $incident->witnesses }}</p>
                @endif
            </div>

            {{-- School Information --}}
            @if($incident->school_name || $incident->school_county || $incident->incident_location)
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">School Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    @if($incident->school_name)
                    <div><span class="text-gray-500">School:</span> <span class="text-gray-800 font-medium">{{ $incident->school_name }}</span></div>
                    @endif
                    @if($incident->school_level)
                    <div><span class="text-gray-500">Level:</span> <span class="text-gray-800">{{ \App\Models\Incident::SCHOOL_LEVELS[$incident->school_level] ?? $incident->school_level }}</span></div>
                    @endif
                    @if($incident->school_county)
                    <div><span class="text-gray-500">County:</span> <span class="text-gray-800">{{ $incident->school_county }}</span></div>
                    @endif
                    @if($incident->school_district)
                    <div><span class="text-gray-500">District:</span> <span class="text-gray-800">{{ $incident->school_district }}</span></div>
                    @endif
                    @if($incident->incident_location)
                    <div><span class="text-gray-500">Location:</span> <span class="text-gray-800">{{ $incident->incident_location }}</span></div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Affected Person --}}
            @if($incident->victim_name)
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Affected Person</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Name:</span> <span class="text-gray-800 font-medium">{{ $incident->victim_name }}</span></div>
                    @if($incident->victim_age)<div><span class="text-gray-500">Age:</span> <span class="text-gray-800">{{ $incident->victim_age }}</span></div>@endif
                    @if($incident->victim_gender)<div><span class="text-gray-500">Gender:</span> <span class="text-gray-800">{{ ucfirst($incident->victim_gender) }}</span></div>@endif
                    @if($incident->victim_grade)<div><span class="text-gray-500">Grade:</span> <span class="text-gray-800">{{ $incident->victim_grade }}</span></div>@endif
                    @if($incident->victim_contact)<div><span class="text-gray-500">Contact:</span> <span class="text-gray-800">{{ $incident->victim_contact }}</span></div>@endif
                    @if($incident->victim_parent_guardian)<div><span class="text-gray-500">Parent/Guardian:</span> <span class="text-gray-800">{{ $incident->victim_parent_guardian }}</span></div>@endif
                    @if($incident->victim_parent_contact)<div><span class="text-gray-500">Parent Contact:</span> <span class="text-gray-800">{{ $incident->victim_parent_contact }}</span></div>@endif
                </div>
            </div>
            @endif

            {{-- Perpetrator --}}
            @if($incident->perpetrator_name || $incident->perpetrator_type)
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Perpetrator</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    @if($incident->perpetrator_name)<div><span class="text-gray-500">Name:</span> <span class="text-gray-800 font-medium">{{ $incident->perpetrator_name }}</span></div>@endif
                    @if($incident->perpetrator_type)<div><span class="text-gray-500">Type:</span> <span class="text-gray-800">{{ \App\Models\Incident::PERPETRATOR_TYPES[$incident->perpetrator_type] ?? $incident->perpetrator_type }}</span></div>@endif
                </div>
                @if($incident->perpetrator_description)
                <p class="text-sm text-gray-700 mt-3 whitespace-pre-wrap">{{ $incident->perpetrator_description }}</p>
                @endif
            </div>
            @endif

            {{-- Public Reporter Info --}}
            @if($incident->isPublicReport() && ($incident->public_reporter_name || $incident->public_reporter_phone || $incident->public_reporter_email))
            <div class="bg-green-50 border border-green-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-green-900 uppercase tracking-wide mb-3">Public Reporter</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    @if($incident->public_reporter_name)<div><span class="text-green-600">Name:</span> <span class="text-green-800 font-medium">{{ $incident->public_reporter_name }}</span></div>@endif
                    @if($incident->public_reporter_phone)<div><span class="text-green-600">Phone:</span> <span class="text-green-800">{{ $incident->public_reporter_phone }}</span></div>@endif
                    @if($incident->public_reporter_email)<div><span class="text-green-600">Email:</span> <span class="text-green-800">{{ $incident->public_reporter_email }}</span></div>@endif
                    @if($incident->public_reporter_relationship)<div><span class="text-green-600">Relationship:</span> <span class="text-green-800">{{ \App\Models\Incident::REPORTER_RELATIONSHIPS[$incident->public_reporter_relationship] ?? $incident->public_reporter_relationship }}</span></div>@endif
                </div>
                @if($incident->tracking_code)
                <p class="text-xs text-green-600 mt-3">Tracking Code: <span class="font-mono font-bold">{{ $incident->tracking_code }}</span></p>
                @endif
            </div>
            @endif

            {{-- Files & Evidence --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Files & Evidence ({{ $incident->files->count() }})</h3>
                </div>

                {{-- Upload form --}}
                <form method="POST" action="{{ route('sir.incidents.files', $incident) }}" enctype="multipart/form-data" class="mb-4 p-3 bg-gray-50 rounded-md">
                    @csrf
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <input type="file" name="files[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:border-0 file:text-sm file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        </div>
                        <select name="file_category" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm">
                            @foreach(\App\Models\Incident::FILE_CATEGORIES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="px-3 py-1.5 bg-slate-800 text-white text-sm hover:bg-slate-700 rounded-md">Upload</button>
                    </div>
                </form>

                @if($incident->files->count())
                <div class="space-y-2">
                    @foreach($incident->files as $file)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($file->isImage())
                            <img src="{{ $file->getFileUrl() }}" alt="" class="w-10 h-10 object-cover rounded">
                            @else
                            <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm text-gray-800 truncate">{{ $file->file_name }}</p>
                                <p class="text-xs text-gray-400">{{ $file->category_label }} · {{ $file->file_size_formatted }} · {{ $file->uploader?->name ?? 'Public' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ $file->getFileUrl() }}" target="_blank" class="text-xs text-blue-700 hover:underline">View</a>
                            @if($canManage || $file->uploaded_by === auth()->id())
                            <form method="POST" action="{{ route('sir.incidents.files.delete', [$incident, $file]) }}" onsubmit="return confirm('Delete this file?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline">Delete</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400">No files uploaded yet.</p>
                @endif
            </div>

            {{-- Case Notes Timeline --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Notes & Activity ({{ $notes->count() }})</h3>

                {{-- Add Note --}}
                <form method="POST" action="{{ route('sir.incidents.notes', $incident) }}" class="mb-6 p-4 bg-gray-50 rounded-md">
                    @csrf
                    <div class="space-y-3">
                        <textarea name="note" rows="3" required placeholder="Add a note..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        <div class="flex items-center gap-3">
                            <select name="note_type" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm">
                                @foreach(\App\Models\IncidentNote::NOTE_TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($canManage)
                            <label class="flex items-center gap-1.5">
                                <input type="checkbox" name="is_private" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-xs text-gray-500">Private (managers only)</span>
                            </label>
                            @endif
                            <button class="ml-auto px-4 py-1.5 bg-slate-800 text-white text-sm hover:bg-slate-700 rounded-md">Add Note</button>
                        </div>
                    </div>
                </form>

                {{-- Notes list --}}
                <div class="space-y-4">
                    @forelse($notes as $note)
                    <div class="border-l-2 border-{{ $note->is_private ? 'amber' : 'gray' }}-300 pl-4 py-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-medium text-gray-800">{{ $note->user->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded">{{ $note->note_type_label }}</span>
                            @if($note->is_private)
                            <span class="text-[10px] px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded">Private</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $note->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $note->note }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400">No notes yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar (1/3) --}}
        <div class="space-y-6">
            {{-- Details Card --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Details</h3>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-gray-500">Status</dt><dd class="font-medium text-gray-800 mt-0.5"><span class="inline-block px-2 py-0.5 text-xs bg-{{ $incident->status_color }}-100 text-{{ $incident->status_color }}-700 rounded">{{ $incident->status_label }}</span></dd></div>
                    <div><dt class="text-gray-500">Type</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->type_label }}</dd></div>
                    <div><dt class="text-gray-500">Category</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->category_label }}</dd></div>
                    <div><dt class="text-gray-500">Priority</dt><dd class="font-medium text-gray-800 mt-0.5"><span class="inline-block px-2 py-0.5 text-xs bg-{{ $incident->priority_color }}-100 text-{{ $incident->priority_color }}-700 rounded">{{ $incident->priority_label }}</span></dd></div>
                    <div><dt class="text-gray-500">Source</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->source_label }}</dd></div>
                    <div><dt class="text-gray-500">Incident Date</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->incident_date->format('M d, Y') }}</dd></div>
                    <div><dt class="text-gray-500">Reported</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->created_at->format('M d, Y g:i A') }}</dd></div>
                    <div><dt class="text-gray-500">Reporter</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->reporter?->name ?? ($incident->public_reporter_name ?? 'Anonymous') }}</dd></div>
                    <div><dt class="text-gray-500">Assigned To</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->assignee?->name ?? 'Unassigned' }}</dd></div>
                    @if($incident->is_recurring)
                    <div><dt class="text-gray-500">Recurring</dt><dd class="font-medium text-red-700 mt-0.5">Yes — recurring incident</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Risk Assessment --}}
            @if($incident->risk_level || $incident->immediate_action_required || $incident->safety_plan)
            <div class="bg-{{ $incident->risk_level === 'immediate_danger' ? 'red' : ($incident->risk_level === 'high' ? 'orange' : 'amber') }}-50 border border-{{ $incident->risk_level === 'immediate_danger' ? 'red' : ($incident->risk_level === 'high' ? 'orange' : 'amber') }}-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Risk Assessment</h3>
                <dl class="space-y-2 text-sm">
                    @if($incident->risk_level)
                    <div><dt class="text-gray-500">Risk Level</dt><dd class="font-medium text-gray-800 mt-0.5"><span class="px-2 py-0.5 text-xs bg-{{ $incident->risk_level_color }}-100 text-{{ $incident->risk_level_color }}-700 rounded">{{ $incident->risk_level_label }}</span></dd></div>
                    @endif
                    @if($incident->safety_plan)
                    <div><dt class="text-gray-500">Safety Plan</dt><dd class="text-gray-700 mt-0.5">{{ $incident->safety_plan }}</dd></div>
                    @endif
                </dl>
            </div>
            @endif

            {{-- Follow-Up --}}
            @if($incident->follow_up_required || $incident->resolution)
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Follow-Up & Resolution</h3>
                <dl class="space-y-2 text-sm">
                    @if($incident->follow_up_required)
                    <div><dt class="text-gray-500">Follow-Up Date</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->follow_up_date?->format('M d, Y') ?? 'Not set' }}</dd></div>
                    @endif
                    @if($incident->resolution)
                    <div><dt class="text-gray-500">Resolution</dt><dd class="text-gray-700 mt-0.5">{{ $incident->resolution }}</dd></div>
                    @endif
                    @if($incident->resolution_date)
                    <div><dt class="text-gray-500">Resolution Date</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->resolution_date->format('M d, Y') }}</dd></div>
                    @endif
                    @if($incident->referral_agency)
                    <div><dt class="text-gray-500">Referral Agency</dt><dd class="font-medium text-gray-800 mt-0.5">{{ $incident->referral_agency }}</dd></div>
                    @endif
                    @if($incident->referral_details)
                    <div><dt class="text-gray-500">Referral Details</dt><dd class="text-gray-700 mt-0.5">{{ $incident->referral_details }}</dd></div>
                    @endif
                </dl>
            </div>
            @endif

            {{-- Quick Assign (for managers) --}}
            @if($canManage)
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Quick Assign</h3>
                <form method="POST" action="{{ route('sir.incidents.update', $incident) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="type" value="{{ $incident->type }}">
                    <input type="hidden" name="category" value="{{ $incident->category }}">
                    <input type="hidden" name="title" value="{{ $incident->title }}">
                    <input type="hidden" name="description" value="{{ $incident->description }}">
                    <input type="hidden" name="priority" value="{{ $incident->priority }}">
                    <input type="hidden" name="status" value="{{ $incident->status }}">
                    <input type="hidden" name="incident_date" value="{{ $incident->incident_date->format('Y-m-d') }}">
                    <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Unassigned</option>
                        @foreach($counselors as $counselor)
                        <option value="{{ $counselor->id }}" {{ $incident->assigned_to == $counselor->id ? 'selected' : '' }}>{{ $counselor->name }}</option>
                        @endforeach
                    </select>
                    <button class="w-full px-3 py-1.5 bg-slate-800 text-white text-sm hover:bg-slate-700 rounded-md">Update Assignment</button>
                </form>
            </div>
            @endif

            {{-- Status Progress --}}
            <div class="bg-white border border-gray-200 rounded-md p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Progress</h3>
                @php
                    $statusOrder = ['reported', 'under_review', 'under_investigation', 'action_taken', 'referred', 'resolved', 'closed'];
                    $currentIndex = array_search($incident->status, $statusOrder);
                @endphp
                <div class="space-y-2">
                    @foreach($statusOrder as $index => $status)
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full border-2 {{ $index <= $currentIndex ? 'bg-green-500 border-green-500' : 'border-gray-300' }} flex items-center justify-center">
                            @if($index <= $currentIndex)
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @endif
                        </div>
                        <span class="text-xs {{ $index <= $currentIndex ? 'text-gray-800 font-medium' : 'text-gray-400' }}">{{ \App\Models\Incident::STATUSES[$status] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
