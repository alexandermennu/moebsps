@extends('layouts.app')

@section('title', $counselor->name . ' — Counselor Profile')
@section('page-title', 'Counselor Profile')

@section('content')
<div class="max-w-6xl">
    {{-- Breadcrumb --}}
    <div class="mb-6 flex items-center gap-2 text-xs">
        @if(auth()->user()->hasFullAccess())
            <a href="{{ route('admin.users.counselors') }}" class="text-blue-700 hover:underline">← Back To All Counselors</a>
            <span class="text-gray-400">/</span>
        @elseif(auth()->user()->canCreateStaff() && auth()->user()->division && auth()->user()->division->code === 'CGPC')
            <a href="{{ route('staff.counselors') }}" class="text-blue-700 hover:underline">← Back To All Counselors</a>
            <span class="text-gray-400">/</span>
        @elseif(auth()->user()->role === 'counselor' && auth()->user()->id === $counselor->id)
            <a href="{{ route('counselor-profile.edit') }}" class="text-blue-700 hover:underline">← My Profile</a>
            <span class="text-gray-400">/</span>
        @endif
        <span class="text-gray-500">{{ $counselor->name }}</span>
    </div>

    {{-- Profile Summary Card --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="flex items-stretch">
            {{-- Photo (left) --}}
            <div class="flex-shrink-0 bg-gray-100">
                @if($counselor->hasProfilePhoto())
                    <img src="{{ $counselor->profile_photo_url }}"
                         alt="{{ $counselor->name }}"
                         class="object-cover w-full h-full" style="width: 220px;">
                @else
                    <div class="flex items-center justify-center text-6xl font-bold text-gray-400 bg-gray-100 w-full h-full" style="width: 220px;">
                        {{ $counselor->initials }}
                    </div>
                @endif
            </div>

            {{-- Summary Details (right) --}}
            <div class="flex-1 flex flex-col">
                <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-white">{{ $counselor->name }}</h1>
                            <p class="text-blue-100 text-sm mt-0.5">School Counselor</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @php
                                $profileStatusColors = [
                                    'draft' => 'bg-gray-100 text-gray-700',
                                    'pending_review' => 'bg-amber-100 text-amber-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'changes_requested' => 'bg-red-100 text-red-800',
                                ];
                                $profileStatusClass = $profileStatusColors[$counselor->counselor_profile_status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold {{ $profileStatusClass }}">
                                Profile: {{ $counselor->counselor_profile_status_label }}
                            </span>
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

                {{-- Key Details Grid --}}
                <div class="px-6 py-4 flex-1 grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    <div>
                        <span class="text-gray-400 text-xs font-medium uppercase">Division</span>
                        <p class="text-gray-900 font-medium">{{ $counselor->division?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-medium uppercase">Email</span>
                        <p class="text-gray-900">{{ $counselor->email }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-medium uppercase">School of Assignment</span>
                        <p class="text-gray-900 font-medium">{{ $counselor->counselor_school ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-medium uppercase">County of Assignment</span>
                        <p class="text-gray-900">{{ $counselor->counselor_county ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-medium uppercase">Phone</span>
                        <p class="text-gray-900">{{ $counselor->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs font-medium uppercase">Highest Qualification</span>
                        <p class="text-gray-900">{{ $counselor->counselor_qualification_label }}</p>
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
                        <a href="{{ route('admin.counselor-profile.edit', $counselor) }}" class="px-3 py-1.5 bg-blue-700 text-white text-xs font-medium hover:bg-blue-600">
                            Edit Counselor Profile
                        </a>
                        <a href="{{ route('admin.users.edit', $counselor) }}" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium hover:bg-slate-700">
                            Edit User Account
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         PROFILE REVIEW PANEL (visible to admin when profile is pending/changes_requested)
         ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->hasFullAccess() && in_array($counselor->counselor_profile_status, ['pending_review', 'changes_requested']))
        <div class="bg-amber-50 border border-amber-300 mb-6 p-5">
            <div class="flex items-start gap-3 mb-4">
                <svg class="w-6 h-6 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <div>
                    <h3 class="text-sm font-bold text-amber-800 uppercase tracking-wide">Profile Review Required</h3>
                    <p class="text-xs text-amber-700 mt-0.5">This counselor's profile is awaiting your review. Please verify the information and attached documents below, then approve or request changes.</p>
                    @if($counselor->counselor_profile_reviewed_at)
                        <p class="text-xs text-amber-600 mt-1">
                            Last reviewed: {{ $counselor->counselor_profile_reviewed_at->format('M j, Y g:i A') }}
                            @if($counselor->profileReviewedBy) by {{ $counselor->profileReviewedBy->name }} @endif
                        </p>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <form method="POST" action="{{ route('admin.counselor-profile.approve', $counselor) }}">
                    @csrf
                    <div class="mb-2">
                        <textarea name="review_notes" rows="2" placeholder="Optional approval notes..."
                                  class="w-full px-3 py-2 border border-green-300 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-700 text-white text-sm font-semibold hover:bg-green-600 flex items-center justify-center gap-2"
                            onclick="return confirm('Approve this counselor\'s profile? This confirms all information has been verified.')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Approve Profile
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.counselor-profile.request-changes', $counselor) }}">
                    @csrf
                    <div class="mb-2">
                        <textarea name="review_notes" rows="2" required placeholder="Describe what needs to be corrected... *"
                                  class="w-full px-3 py-2 border border-red-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-semibold hover:bg-red-500 flex items-center justify-center gap-2"
                            onclick="return confirm('Request changes to this counselor\'s profile?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        Request Changes
                    </button>
                </form>
            </div>
        </div>
    @elseif(auth()->user()->hasFullAccess() && $counselor->counselor_profile_status === 'approved' && $counselor->counselor_profile_reviewed_at)
        <div class="bg-green-50 border border-green-200 mb-6 px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium text-green-800">Profile Approved</span>
                <span class="text-xs text-green-600">
                    {{ $counselor->counselor_profile_reviewed_at->format('M j, Y') }}
                    @if($counselor->profileReviewedBy) by {{ $counselor->profileReviewedBy->name }} @endif
                </span>
            </div>
        </div>
    @elseif($counselor->id === auth()->id())
        {{-- Status banners for the counselor themselves --}}
        @if($counselor->counselor_profile_status === 'pending_review')
            <div class="bg-amber-50 border border-amber-200 mb-6 px-5 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <span class="text-sm font-medium text-amber-800">Your profile is pending administrator review.</span>
                    <p class="text-xs text-amber-600 mt-0.5">An administrator will verify your records and attached documents. You will be notified once reviewed.</p>
                </div>
            </div>
        @elseif($counselor->counselor_profile_status === 'changes_requested')
            <div class="bg-red-50 border border-red-200 mb-6 px-5 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <div>
                    <span class="text-sm font-medium text-red-800">Changes have been requested on your profile.</span>
                    @if($counselor->counselor_profile_review_notes)
                        <div class="mt-1.5 p-2 bg-white border border-red-200 text-xs text-red-800">
                            <strong>Reviewer Notes:</strong> {{ $counselor->counselor_profile_review_notes }}
                        </div>
                    @endif
                    <p class="text-xs text-red-600 mt-1">Please <a href="{{ route('counselor-profile.edit') }}" class="underline font-medium">edit your profile</a> and resubmit.</p>
                </div>
            </div>
        @elseif($counselor->counselor_profile_status === 'approved')
            <div class="bg-green-50 border border-green-200 mb-6 px-5 py-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium text-green-800">Your profile has been verified and approved.</span>
            </div>
        @endif
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 1 & 2: Personal Info + Assignment Details (side-by-side)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- SECTION 1: Personal Information --}}
        <div class="bg-white border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Personal Information
                </h2>
            </div>
            <div class="p-5">
                <dl class="space-y-3 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Full Name</dt>
                            <dd class="text-gray-900 font-medium">{{ $counselor->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Email Address</dt>
                            <dd class="text-gray-900">{{ $counselor->email }}</dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Date of Birth</dt>
                            <dd class="text-gray-900">{{ $counselor->date_of_birth?->format('F j, Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Gender</dt>
                            <dd class="text-gray-900">{{ \App\Models\User::GENDERS[$counselor->gender] ?? '—' }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Nationality</dt>
                        <dd class="text-gray-900">{{ $counselor->nationality ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Residential Address</dt>
                        <dd class="text-gray-900">{{ $counselor->address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">City / Town</dt>
                        <dd class="text-gray-900">{{ $counselor->city ?? '—' }}</dd>
                    </div>
                </dl>

                {{-- Contact Details --}}
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Contact Details</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-gray-500 text-xs font-medium">Phone</dt>
                                <dd class="text-gray-900">{{ $counselor->phone ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 text-xs font-medium">School Phone</dt>
                                <dd class="text-gray-900">{{ $counselor->counselor_school_phone ?? '—' }}</dd>
                            </div>
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

                {{-- Emergency Contact --}}
                @if($counselor->emergency_contact_name || $counselor->emergency_contact_phone)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Emergency Contact</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-gray-500 text-xs font-medium">Contact Name</dt>
                                <dd class="text-gray-900">{{ $counselor->emergency_contact_name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 text-xs font-medium">Contact Phone</dt>
                                <dd class="text-gray-900">{{ $counselor->emergency_contact_phone ?? '—' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Relationship</dt>
                            <dd class="text-gray-900">{{ $counselor->emergency_contact_relationship ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
                @endif
            </div>
        </div>

        {{-- SECTION 2: Assignment Details --}}
        <div class="bg-white border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Assignment Details
                </h2>
            </div>
            <div class="p-5">
                <dl class="space-y-3 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">School of Assignment</dt>
                            <dd class="text-gray-900 font-medium">{{ $counselor->counselor_school ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">County of Assignment</dt>
                            <dd class="text-gray-900 font-medium">{{ $counselor->counselor_county ?? '—' }}</dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Date of Appointment</dt>
                            <dd class="text-gray-900">{{ $counselor->counselor_appointed_at?->format('F j, Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">Assignment / Start Date</dt>
                            <dd class="text-gray-900">{{ $counselor->counselor_assignment_date?->format('F j, Y') ?? '—' }}</dd>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">School District</dt>
                            <dd class="text-gray-900">{{ $counselor->counselor_school_district ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 text-xs font-medium">School Address</dt>
                            <dd class="text-gray-900">{{ $counselor->counselor_school_address ?? '—' }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs font-medium">Principal / Head Teacher</dt>
                        <dd class="text-gray-900">{{ $counselor->counselor_school_principal ?? '—' }}</dd>
                    </div>
                </dl>

                {{-- School Brief Details --}}
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">School Brief Details</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                            <p class="text-xs text-gray-500 font-medium uppercase">School Level</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ \App\Models\User::SCHOOL_LEVELS[$counselor->counselor_school_level] ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                            <p class="text-xs text-gray-500 font-medium uppercase">School Type</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ \App\Models\User::SCHOOL_TYPES[$counselor->counselor_school_type] ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                            <p class="text-xs text-gray-500 font-medium uppercase">Student Population</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_school_population ? number_format($counselor->counselor_school_population) : '—' }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                            <p class="text-xs text-gray-500 font-medium uppercase">No. of Boys</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_num_boys ? number_format($counselor->counselor_num_boys) : '—' }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                            <p class="text-xs text-gray-500 font-medium uppercase">No. of Girls</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $counselor->counselor_num_girls ? number_format($counselor->counselor_num_girls) : '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Training --}}
                @if($counselor->counselor_training)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Training & Development</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($counselor->counselor_training)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 3: Education, Experience & Qualifications (full-width)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                Education, Experience & Qualifications
            </h2>
        </div>
        <div class="p-5">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                <div class="bg-gray-50 border border-gray-100 p-4 text-center">
                    <p class="text-xs text-gray-500 font-medium uppercase">Highest Education</p>
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

            {{-- All Qualifications --}}
            @if($counselor->counselorEducation->count() > 0)
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-semibold text-blue-800 uppercase tracking-wide flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                            Qualifications
                        </h3>
                        <span class="text-xs text-gray-400">{{ $counselor->counselorEducation->count() }} qualification(s)</span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($counselor->counselorEducation as $edu)
                            <div class="py-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 bg-blue-50 border border-blue-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">{{ $edu->degree_level_label }}</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $edu->institution }}</p>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                                            @if($edu->program)
                                                <span><strong>Program:</strong> {{ $edu->program }}</span>
                                            @endif
                                            @if($edu->year_obtained)
                                                <span><strong>Year:</strong> {{ $edu->year_obtained }}</span>
                                            @endif
                                            @if($edu->country)
                                                <span><strong>Country:</strong> {{ $edu->country }}</span>
                                            @endif
                                            @if($edu->year_started || $edu->year_graduated)
                                                <span><strong>Period:</strong> {{ $edu->year_range }}</span>
                                            @endif
                                        </div>
                                        @if($edu->notes)
                                            <p class="text-xs text-gray-500 mt-1.5 italic">{{ $edu->notes }}</p>
                                        @endif
                                        {{-- Attached document --}}
                                        @if($edu->hasDocument())
                                            <div class="mt-2 flex items-center gap-2 text-xs">
                                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <a href="{{ $edu->getDocumentUrl() }}" target="_blank" class="text-blue-700 hover:underline font-medium">{{ $edu->document_name }}</a>
                                                <span class="text-gray-400">({{ $edu->document_size_formatted }})</span>
                                                <span class="px-1.5 py-0.5 bg-green-50 text-green-700 font-medium text-xs">Document Attached</span>
                                            </div>
                                        @else
                                            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span class="px-1.5 py-0.5 bg-gray-50 text-gray-500 font-medium">No Document</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400 italic mb-5">No qualifications added yet.</p>
            @endif

            {{-- Additional Certificates & Achievements --}}
            @if($counselor->counselorCertificates->count() > 0)
                <div class="border-t border-gray-200 pt-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            Additional Certificates & Achievements
                        </h3>
                        <span class="text-xs text-gray-400">{{ $counselor->counselorCertificates->count() }} certificate(s)</span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($counselor->counselorCertificates as $cert)
                            <div class="py-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 bg-amber-50 border border-amber-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">{{ $cert->certificate_name }}</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $cert->institution }}</p>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                                            @if($cert->program)
                                                <span><strong>Program:</strong> {{ $cert->program }}</span>
                                            @endif
                                            @if($cert->year_obtained)
                                                <span><strong>Year:</strong> {{ $cert->year_obtained }}</span>
                                            @endif
                                            @if($cert->certificate_number)
                                                <span><strong>No:</strong> {{ $cert->certificate_number }}</span>
                                            @endif
                                            @if($cert->expiry_date)
                                                <span class="px-1.5 py-0.5 text-xs font-medium {{ $cert->is_expired ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                                                    {{ $cert->status_label }} — {{ $cert->expiry_date->format('M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($cert->description)
                                            <p class="text-xs text-gray-500 mt-1.5 italic">{{ $cert->description }}</p>
                                        @endif
                                        {{-- Attached document --}}
                                        @if($cert->hasDocument())
                                            <div class="mt-2 flex items-center gap-2 text-xs">
                                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <a href="{{ $cert->getDocumentUrl() }}" target="_blank" class="text-blue-700 hover:underline font-medium">{{ $cert->document_name }}</a>
                                                <span class="text-gray-400">({{ $cert->document_size_formatted }})</span>
                                                <span class="px-1.5 py-0.5 bg-green-50 text-green-700 font-medium text-xs">Document Attached</span>
                                            </div>
                                        @else
                                            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span class="px-1.5 py-0.5 bg-gray-50 text-gray-500 font-medium">No Document</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         DOCUMENTS & CERTIFICATES
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Documents & Certificates
            </h2>
            <span class="text-xs text-gray-400">{{ $counselor->counselorDocuments->count() }} document(s)</span>
        </div>
        <div class="p-5">
            @if($counselor->counselorDocuments->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($counselor->counselorDocuments as $doc)
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
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
                                <a href="{{ $doc->getFileUrl() }}" target="_blank" class="px-2 py-1 text-xs text-blue-700 hover:bg-blue-50 border border-blue-200">View</a>
                                @if($counselor->id === auth()->id() || auth()->user()->hasFullAccess())
                                    <form method="POST" action="{{ route('counselor-profile.documents.delete', $doc) }}" onsubmit="return confirm('Delete this document?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 border border-red-200">Delete</button>
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
                            <button type="submit" class="px-3 py-1.5 bg-blue-700 text-white text-xs font-medium hover:bg-blue-600">Upload</button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">PDF, JPG, PNG, WebP, DOC, DOCX. Max 5MB.</p>
                        @error('document') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
