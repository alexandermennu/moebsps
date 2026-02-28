@extends('layouts.app')

@section('title', $case->case_number)
@section('page-title', 'Case ' . $case->case_number)

@section('content')
<div class="space-y-6">
    {{-- Back & Actions Bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('srgbv.cases.index') }}" class="text-xs text-blue-700 hover:underline">Back to Cases</a>
        <div class="flex gap-2">
            @if($canManage)
                <a href="{{ route('srgbv.cases.edit', $case) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">
                    Edit Case
                </a>
            @endif
        </div>
    </div>

    {{-- Case Header --}}
    <div class="bg-white border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-sm font-mono text-gray-400">{{ $case->case_number }}</span>
                    {{-- Priority --}}
                    <span class="text-[10px] px-1.5 py-0.5 font-medium
                        @switch($case->priority)
                            @case('critical') bg-red-100 text-red-700 @break
                            @case('high') bg-amber-100 text-amber-700 @break
                            @case('medium') bg-blue-100 text-blue-700 @break
                            @case('low') bg-gray-100 text-gray-600 @break
                        @endswitch
                    ">{{ $case->priority_label }} Priority</span>
                    {{-- Status --}}
                    <span class="text-[10px] px-1.5 py-0.5 font-medium
                        @switch($case->status)
                            @case('reported') bg-red-100 text-red-700 @break
                            @case('under_investigation') bg-amber-100 text-amber-700 @break
                            @case('action_taken') bg-blue-100 text-blue-700 @break
                            @case('referred') bg-purple-100 text-purple-700 @break
                            @case('resolved') bg-green-100 text-green-700 @break
                            @case('closed') bg-gray-100 text-gray-600 @break
                        @endswitch
                    ">{{ $case->status_label }}</span>
                    @if($case->is_confidential)
                        <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-600">Confidential</span>
                    @endif
                    @if($case->immediate_action_required)
                        <span class="text-[10px] px-1.5 py-0.5 bg-red-600 text-white animate-pulse">URGENT</span>
                    @endif
                </div>
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ $case->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $case->category_label }} · Incident: {{ $case->incident_date->format('M d, Y') }} · Reported {{ $case->created_at->diffForHumans() }}
                </p>
            </div>

            {{-- Quick Status Change --}}
            @if($canManage && $case->isOpen())
                <form method="POST" action="{{ route('srgbv.cases.status', $case) }}" class="flex items-center gap-2">
                    @csrf @method('PATCH')
                    <select name="status" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm">
                        @foreach(\App\Models\SrgbvCase::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ $case->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-sm hover:bg-slate-700">Update</button>
                </form>
            @endif
        </div>

        <p class="text-sm text-gray-700 leading-relaxed">{{ $case->description }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content (Left 2 cols) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Victim Information --}}
            <div class="bg-white border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    Victim Information
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Name</span>
                        <p class="font-medium text-gray-800">{{ $case->victim_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Age</span>
                        <p class="font-medium text-gray-800">{{ $case->victim_age ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Gender</span>
                        <p class="font-medium text-gray-800">{{ $case->victim_gender ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Grade</span>
                        <p class="font-medium text-gray-800">{{ $case->victim_grade ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">School</span>
                        <p class="font-medium text-gray-800">{{ $case->victim_school ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Contact</span>
                        <p class="font-medium text-gray-800">{{ $case->victim_contact ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Parent / Guardian</span>
                        <p class="font-medium text-gray-800">{{ $case->victim_parent_guardian ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Parent Contact</span>
                        <p class="font-medium text-gray-800">{{ $case->victim_parent_contact ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Perpetrator Information --}}
            @if($case->perpetrator_name || $case->perpetrator_type || $case->perpetrator_description)
            <div class="bg-white border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    Perpetrator Information
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Name</span>
                        <p class="font-medium text-gray-800">{{ $case->perpetrator_name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Type</span>
                        <p class="font-medium text-gray-800">{{ \App\Models\SrgbvCase::PERPETRATOR_TYPES[$case->perpetrator_type] ?? $case->perpetrator_type ?? '—' }}</p>
                    </div>
                    @if($case->perpetrator_description)
                    <div class="col-span-2">
                        <span class="text-gray-500">Description</span>
                        <p class="font-medium text-gray-800">{{ $case->perpetrator_description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Incident Details --}}
            @if($case->incident_description || $case->incident_location || $case->witnesses)
            <div class="bg-white border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    Incident Details
                </h3>
                <div class="space-y-3 text-sm">
                    @if($case->incident_location)
                    <div>
                        <span class="text-gray-500">Location:</span>
                        <span class="ml-1 font-medium text-gray-800">{{ $case->incident_location }}</span>
                    </div>
                    @endif
                    @if($case->is_recurring)
                    <div>
                        <span class="text-[10px] px-1.5 py-0.5 bg-orange-100 text-orange-700">Recurring Incident</span>
                    </div>
                    @endif
                    @if($case->incident_description)
                    <div>
                        <span class="text-gray-500 block mb-1">Details:</span>
                        <p class="text-gray-800 leading-relaxed">{{ $case->incident_description }}</p>
                    </div>
                    @endif
                    @if($case->witnesses)
                    <div>
                        <span class="text-gray-500 block mb-1">Witnesses:</span>
                        <p class="text-gray-800">{{ $case->witnesses }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Risk Assessment & Safety --}}
            @if($case->risk_level || $case->safety_plan || $case->immediate_action_required)
            <div class="bg-white border {{ $case->immediate_action_required ? 'border-red-300' : 'border-gray-200' }} p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    Risk Assessment & Safety
                </h3>
                <div class="space-y-3 text-sm">
                    @if($case->risk_level)
                    <div>
                        <span class="text-gray-500">Risk Level:</span>
                        <span class="ml-1 text-[10px] px-1.5 py-0.5 font-medium
                            @switch($case->risk_level)
                                @case('immediate_danger') bg-red-100 text-red-700 @break
                                @case('high') bg-amber-100 text-amber-700 @break
                                @case('moderate') bg-blue-100 text-blue-700 @break
                                @case('low') bg-green-100 text-green-700 @break
                            @endswitch
                        ">{{ \App\Models\SrgbvCase::RISK_LEVELS[$case->risk_level] ?? $case->risk_level }}</span>
                    </div>
                    @endif
                    @if($case->safety_plan)
                    <div>
                        <span class="text-gray-500 block mb-1">Safety Plan:</span>
                        <p class="text-gray-800 leading-relaxed">{{ $case->safety_plan }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Resolution --}}
            @if($case->resolution || $case->referral_agency)
            <div class="bg-white border border-green-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">
                    Resolution & Referral
                </h3>
                <div class="space-y-3 text-sm">
                    @if($case->resolution)
                    <div>
                        <span class="text-gray-500 block mb-1">Resolution:</span>
                        <p class="text-gray-800 leading-relaxed">{{ $case->resolution }}</p>
                    </div>
                    @endif
                    @if($case->resolution_date)
                    <div>
                        <span class="text-gray-500">Resolution Date:</span>
                        <span class="ml-1 font-medium text-gray-800">{{ $case->resolution_date->format('M d, Y') }}</span>
                    </div>
                    @endif
                    @if($case->referral_agency)
                    <div>
                        <span class="text-gray-500">Referral Agency:</span>
                        <span class="ml-1 font-medium text-gray-800">{{ $case->referral_agency }}</span>
                    </div>
                    @endif
                    @if($case->referral_details)
                    <div>
                        <span class="text-gray-500 block mb-1">Referral Details:</span>
                        <p class="text-gray-800">{{ $case->referral_details }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Files & Evidence --}}
            <div class="bg-white border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                        Files & Evidence
                        <span class="text-xs text-gray-400">({{ $case->files->count() }})</span>
                    </h3>
                    <button type="button" onclick="document.getElementById('upload-form').classList.toggle('hidden')"
                            class="text-sm text-red-600 hover:text-red-800">+ Upload Files</button>
                </div>

                {{-- Upload Form (hidden by default) --}}
                <form id="upload-form" method="POST" action="{{ route('srgbv.cases.files', $case) }}" enctype="multipart/form-data" class="hidden mb-4 p-4 bg-gray-50 border border-gray-200">
                    @csrf
                    <div class="flex flex-wrap gap-3 items-end">
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Select Files</label>
                            <input type="file" name="files[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" required
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Category</label>
                            <select name="file_category" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                @foreach(\App\Models\SrgbvCase::FILE_CATEGORIES as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Description</label>
                            <input type="text" name="file_description" placeholder="Describe the file..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-red-700 text-white text-sm hover:bg-red-800">Upload</button>
                    </div>
                </form>

                @if($case->files->isEmpty())
                    <p class="text-sm text-gray-500">No files uploaded yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($case->files as $file)
                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100">
                                <div class="flex items-center gap-3">
                                    @if($file->isImage())
                                        <div class="w-10 h-10 bg-purple-100 flex items-center justify-center text-sm"></div>
                                    @else
                                        <div class="w-10 h-10 bg-blue-100 flex items-center justify-center text-sm"></div>
                                    @endif
                                    <div>
                                        <a href="{{ $file->getFileUrl() }}" target="_blank" class="text-sm font-medium text-gray-800 hover:text-red-700">{{ $file->file_name }}</a>
                                        <p class="text-xs text-gray-500">
                                            {{ $file->category_label }} · {{ $file->file_size_formatted }} · {{ $file->uploader?->name }} · {{ $file->created_at->diffForHumans() }}
                                        </p>
                                        @if($file->description)
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $file->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($canManage || $file->uploaded_by === $user->id)
                                    <form method="POST" action="{{ route('srgbv.cases.files.delete', [$case, $file]) }}"
                                          onsubmit="return confirm('Delete this file?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Image Gallery --}}
                    @php $images = $case->files->filter(fn($f) => $f->isImage()); @endphp
                    @if($images->isNotEmpty())
                        <div class="mt-4">
                            <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Photo Evidence</h4>
                            <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                                @foreach($images as $img)
                                    <a href="{{ $img->getFileUrl() }}" target="_blank" class="block">
                                        <img src="{{ $img->getFileUrl() }}" alt="{{ $img->file_name }}" class="w-full h-24 object-cover rounded-md border border-gray-200 hover:border-red-400">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Case Notes / Timeline --}}
            <div class="bg-white border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                    Case Notes & Timeline
                    <span class="text-xs text-gray-400">({{ $notes->count() }})</span>
                </h3>

                {{-- Add Note Form --}}
                <form method="POST" action="{{ route('srgbv.cases.notes', $case) }}" class="mb-6 p-4 bg-gray-50 border border-gray-200">
                    @csrf
                    <div class="flex gap-3 mb-3">
                        <div class="flex-1">
                            <select name="note_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                @foreach(\App\Models\SrgbvCaseNote::NOTE_TYPES as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($canManage)
                        <label class="flex items-center gap-1.5">
                            <input type="checkbox" name="is_private" value="1" class="h-4 w-4 text-red-600 border-gray-300 rounded">
                            <span class="text-xs text-gray-500">Private note</span>
                        </label>
                        @endif
                    </div>
                    <textarea name="note" rows="3" required placeholder="Add a case note, progress update, or follow-up..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    <button type="submit" class="px-4 py-2 bg-red-700 text-white text-sm hover:bg-red-800">Add Note</button>
                </form>

                {{-- Notes Timeline --}}
                @if($notes->isEmpty())
                    <p class="text-sm text-gray-500">No notes yet.</p>
                @else
                    <div class="space-y-4">
                        @foreach($notes as $note)
                            <div class="relative pl-6 border-l-2 {{ $note->is_private ? 'border-amber-300' : 'border-gray-200' }}">
                                <div class="absolute -left-1.5 top-1 w-3 h-3 rounded-full {{ $note->is_private ? 'bg-amber-400' : 'bg-gray-400' }}"></div>
                                <div class="pb-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] px-1.5 py-0.5
                                            @switch($note->note_type)
                                                @case('progress_update') bg-blue-100 text-blue-700 @break
                                                @case('follow_up') bg-teal-100 text-teal-700 @break
                                                @case('referral') bg-purple-100 text-purple-700 @break
                                                @case('action_taken') bg-green-100 text-green-700 @break
                                                @case('assessment') bg-amber-100 text-amber-700 @break
                                                @case('counseling_session') bg-indigo-100 text-indigo-700 @break
                                                @default bg-gray-100 text-gray-600
                                            @endswitch
                                        ">{{ $note->note_type_label }}</span>
                                        @if($note->is_private)
                                            <span class="text-xs text-amber-600">Private</span>
                                        @endif
                                        <span class="text-xs text-gray-400">{{ $note->created_at->format('M d, Y \a\t g:ia') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $note->note }}</p>
                                    <p class="text-xs text-gray-400 mt-1">— {{ $note->user?->name }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar (Right col) --}}
        <div class="space-y-4">
            {{-- Case Details Card --}}
            <div class="bg-white border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Case Details</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500">Case Number</span>
                        <p class="font-mono font-medium text-gray-800">{{ $case->case_number }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Reported By</span>
                        <p class="font-medium text-gray-800">{{ $case->reporter?->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Division</span>
                        <p class="font-medium text-gray-800">{{ $case->division?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Assigned To</span>
                        <p class="font-medium text-gray-800">{{ $case->assignee?->name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Date Reported</span>
                        <p class="font-medium text-gray-800">{{ $case->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Days Open</span>
                        <p class="font-medium text-gray-800">{{ $case->days_since_reported }} days</p>
                    </div>
                </div>
            </div>

            {{-- Follow-up Card --}}
            @if($case->follow_up_required)
            <div class="bg-amber-50 border border-amber-200 p-5">
                <h3 class="text-sm font-semibold text-amber-800 mb-2">Follow-up Required</h3>
                @if($case->follow_up_date)
                    <p class="text-sm text-amber-700">
                        Due: <strong>{{ $case->follow_up_date->format('M d, Y') }}</strong>
                        @if($case->follow_up_date->isPast())
                            <span class="text-red-600 text-xs font-semibold">(OVERDUE)</span>
                        @else
                            <span class="text-xs">({{ $case->follow_up_date->diffForHumans() }})</span>
                        @endif
                    </p>
                @else
                    <p class="text-sm text-amber-700">No follow-up date set.</p>
                @endif
            </div>
            @endif

            {{-- Assign Counselor (Managers) --}}
            @if($canManage)
            <div class="bg-white border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Quick Assign</h3>
                <form method="POST" action="{{ route('srgbv.cases.update', $case) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="title" value="{{ $case->title }}">
                    <input type="hidden" name="description" value="{{ $case->description }}">
                    <input type="hidden" name="category" value="{{ $case->category }}">
                    <input type="hidden" name="priority" value="{{ $case->priority }}">
                    <input type="hidden" name="status" value="{{ $case->status }}">
                    <input type="hidden" name="victim_name" value="{{ $case->victim_name }}">
                    <input type="hidden" name="incident_date" value="{{ $case->incident_date->format('Y-m-d') }}">
                    <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm mb-2">
                        <option value="">Unassigned</option>
                        @foreach($counselors as $counselor)
                            <option value="{{ $counselor->id }}" {{ $case->assigned_to == $counselor->id ? 'selected' : '' }}>{{ $counselor->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full px-3 py-2 bg-slate-800 text-white text-sm hover:bg-slate-700">Assign</button>
                </form>
            </div>
            @endif

            {{-- Status Progress --}}
            <div class="bg-white border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Status Progress</h3>
                <div class="space-y-2">
                    @php
                        $statusOrder = ['reported', 'under_investigation', 'action_taken', 'referred', 'resolved', 'closed'];
                        $currentIdx = array_search($case->status, $statusOrder);
                    @endphp
                    @foreach($statusOrder as $idx => $status)
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center
                                {{ $idx <= $currentIdx ? 'border-green-500 bg-green-500' : 'border-gray-300' }}">
                                @if($idx <= $currentIdx)
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                @endif
                            </div>
                            <span class="text-xs {{ $idx <= $currentIdx ? 'text-gray-800 font-medium' : 'text-gray-400' }}">
                                {{ \App\Models\SrgbvCase::STATUSES[$status] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
