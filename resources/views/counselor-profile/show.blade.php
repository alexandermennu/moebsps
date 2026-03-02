@extends('layouts.app')

@section('title', $counselor->name . ' — Counselor Profile')
@section('page-title', 'Counselor Profile')

@section('content')
<div class="max-w-5xl">
    {{-- Breadcrumb --}}
    <div class="mb-6 flex items-center gap-2 text-xs">
        @if(auth()->user()->hasFullAccess())
            <a href="{{ route('admin.users.counselors') }}" class="text-blue-700 hover:underline">Counselors</a>
            <span class="text-gray-400">/</span>
        @endif
        <span class="text-gray-500">{{ $counselor->name }}</span>
    </div>

    {{-- Profile Header Card --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-8">
            <div class="flex items-center gap-5">
                <x-user-avatar :user="$counselor" size="xl" />
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $counselor->name }}</h1>
                    <p class="text-blue-100 text-sm mt-1">School Counselor</p>
                    @if($counselor->division)
                        <p class="text-blue-200 text-xs mt-1">{{ $counselor->division->name }}</p>
                    @endif
                    <div class="mt-3 flex items-center gap-3">
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'abandoned_resigned' => 'bg-red-100 text-red-800',
                                'transferred' => 'bg-amber-100 text-amber-800',
                                'on_study_leave' => 'bg-purple-100 text-purple-800',
                                'on_sick_leave' => 'bg-orange-100 text-orange-800',
                                'returned_from_study' => 'bg-blue-100 text-blue-800',
                            ];
                            $statusClass = $statusColors[$counselor->counselor_status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $counselor->counselor_status_label }}</span>
                        @if(!$counselor->is_active)
                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-800">Account Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions Bar --}}
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex items-center gap-3">
            @if($counselor->id === auth()->id())
                <a href="{{ route('counselor-profile.edit') }}" class="px-3 py-1.5 bg-blue-700 text-white text-xs font-medium hover:bg-blue-600">
                    Edit Counselor Profile
                </a>
                <a href="{{ route('profile.edit') }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50">
                    Edit Account
                </a>
            @elseif(auth()->user()->hasFullAccess())
                <a href="{{ route('admin.users.edit', $counselor) }}" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium hover:bg-slate-700">
                    Edit User Account
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Assignment & Contact --}}
        <div class="space-y-6">
            {{-- Assignment Details --}}
            <div class="bg-white border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Assignment
                </h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">School of Assignment</dt>
                        <dd class="text-gray-900 font-medium">{{ $counselor->counselor_school ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">County</dt>
                        <dd class="text-gray-900">{{ $counselor->counselor_county ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">School Phone</dt>
                        <dd class="text-gray-900">{{ $counselor->counselor_school_phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Date of Appointment</dt>
                        <dd class="text-gray-900">{{ $counselor->counselor_appointed_at?->format('F j, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Contact Information --}}
            <div class="bg-white border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Contact
                </h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Email</dt>
                        <dd class="text-gray-900">{{ $counselor->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Phone</dt>
                        <dd class="text-gray-900">{{ $counselor->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Position</dt>
                        <dd class="text-gray-900">{{ $counselor->position ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Member Since</dt>
                        <dd class="text-gray-900">{{ $counselor->created_at->format('F j, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Right Column: Qualifications, Training, Documents --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Qualifications & Experience --}}
            <div class="bg-white border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Qualifications & Experience
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                        <p class="text-xs text-gray-500 font-medium uppercase">Qualification</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_qualification_label }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                        <p class="text-xs text-gray-500 font-medium uppercase">Specialization</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_specialization_label }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                        <p class="text-xs text-gray-500 font-medium uppercase">Years of Experience</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_years_experience !== null ? $counselor->counselor_years_experience . ' years' : '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Training & Development --}}
            <div class="bg-white border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Training & Development
                </h2>

                @if($counselor->counselor_training)
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($counselor->counselor_training)) !!}
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">No training information recorded yet.</p>
                @endif
            </div>

            {{-- Documents & Certificates --}}
            <div class="bg-white border border-gray-200 p-5">
                <div class="flex items-center justify-between border-b border-gray-200 pb-2 mb-4">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                        <svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Documents & Certificates
                    </h2>
                    <span class="text-xs text-gray-400">{{ $counselor->counselorDocuments->count() }} document(s)</span>
                </div>

                @if($counselor->counselorDocuments->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($counselor->counselorDocuments as $doc)
                            <div class="py-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    {{-- File type icon --}}
                                    @if($doc->isPdf())
                                        <div class="w-9 h-9 bg-red-50 border border-red-200 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                    @elseif($doc->isImage())
                                        <div class="w-9 h-9 bg-blue-50 border border-blue-200 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @else
                                        <div class="w-9 h-9 bg-gray-50 border border-gray-200 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $doc->title }}</p>
                                        <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                                            <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 font-medium">{{ $doc->document_type_label }}</span>
                                            <span>{{ $doc->file_size_formatted }}</span>
                                            <span>{{ $doc->created_at->format('M j, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ $doc->getFileUrl() }}" target="_blank" class="px-2 py-1 text-xs text-blue-700 hover:bg-blue-50 border border-blue-200">
                                        View
                                    </a>
                                    @if($counselor->id === auth()->id() || auth()->user()->hasFullAccess())
                                        <form method="POST" action="{{ route('counselor-profile.documents.delete', $doc) }}" onsubmit="return confirm('Delete this document?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 border border-red-200">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic py-2">No documents uploaded yet.</p>
                @endif

                {{-- Upload Document Form --}}
                @if($counselor->id === auth()->id() || auth()->user()->hasFullAccess())
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-3">Upload New Document</h3>
                        <form method="POST"
                              action="{{ auth()->user()->hasFullAccess() && $counselor->id !== auth()->id()
                                  ? route('admin.counselor-profile.documents.upload', $counselor)
                                  : route('counselor-profile.documents.upload') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label for="document_type" class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                                    <select name="document_type" id="document_type" required
                                            class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select...</option>
                                        @foreach(\App\Models\CounselorDocument::DOCUMENT_TYPES as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="doc_title" class="block text-xs font-medium text-gray-600 mb-1">Title *</label>
                                    <input type="text" name="title" id="doc_title" required placeholder="e.g. BSc Education Certificate"
                                           class="w-full px-2.5 py-1.5 border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="file" name="document" required accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                                       class="flex-1 text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:border file:border-gray-300 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                                <button type="submit" class="px-3 py-1.5 bg-blue-700 text-white text-xs font-medium hover:bg-blue-600">
                                    Upload
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-400">PDF, JPG, PNG, WebP, DOC, DOCX. Max 5MB.</p>
                            @error('document')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @error('title')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
