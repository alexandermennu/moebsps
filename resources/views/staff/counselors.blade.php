@extends('layouts.app')

@section('title', 'My Counselors')
@section('page-title', 'My Counselors')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('staff.index') }}" class="text-xs text-blue-700 hover:underline">← Back to My Staff</a>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mt-2">My Counselors</h2>
            <p class="text-sm text-gray-500">CGPC Division — {{ $counselors->total() }} total counselors</p>
        </div>
        <div class="flex items-center gap-3">
            @php $pendingCount = \App\Models\User::where('role', 'counselor')->where('division', auth()->user()->division)->pendingProfileReview()->count(); @endphp
            @if($pendingCount > 0)
                <a href="{{ route('staff.counselors', ['profile_status' => 'pending_review']) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-50 border border-amber-300 text-amber-800 text-sm font-medium hover:bg-amber-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    {{ $pendingCount }} Pending Review
                </a>
            @endif
            <a href="{{ route('staff.create') }}?prefill_role=counselor" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-medium hover:bg-teal-700">
                + Add Counselor
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, school..."
                   class="px-3 py-2 border border-gray-300 rounded-md text-sm w-56">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Account Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Counselor Status</label>
            <select name="counselor_status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">All Statuses</option>
                @foreach(\App\Models\User::COUNSELOR_STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('counselor_status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Profile Status</label>
            <select name="profile_status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">All</option>
                @foreach(\App\Models\User::PROFILE_STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('profile_status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm hover:bg-gray-200">Filter</button>
        @if(request()->hasAny(['search', 'status', 'counselor_status', 'profile_status']))
            <a href="{{ route('staff.counselors') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>

    {{-- Counselors Table --}}
    <div class="bg-white border border-teal-200">
        <div class="px-5 py-3 border-b border-teal-100 bg-teal-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-teal-800">Counselor List</h3>
                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-teal-200 text-teal-800">
                        Showing {{ $counselors->firstItem() ?? 0 }}–{{ $counselors->lastItem() ?? 0 }} of {{ $counselors->total() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">#</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Name</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">School of Assignment</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Qualification</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Current Status</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Profile</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Account</th>
                        <th class="text-right px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($counselors as $c)
                        <tr class="hover:bg-teal-50/30">
                            <td class="px-5 py-3 text-gray-400 text-xs">{{ $counselors->firstItem() + $loop->index }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <x-user-avatar :user="$c" size="xs" />
                                    <div>
                                        <a href="{{ route('counselor-profile.show', $c) }}" class="font-medium text-gray-800 hover:text-teal-700">{{ $c->name }}</a>
                                        <p class="text-xs text-gray-400">{{ $c->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-700">{{ $c->counselor_school ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if($c->counselor_qualification)
                                    <span class="text-gray-700">{{ $c->counselor_qualification_label }}</span>
                                    @if($c->counselor_specialization)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $c->counselor_specialization_label }}</p>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs italic">Not set</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-700',
                                        'abandoned_resigned' => 'bg-red-100 text-red-700',
                                        'transferred' => 'bg-yellow-100 text-yellow-700',
                                        'on_study_leave' => 'bg-purple-100 text-purple-700',
                                        'on_sick_leave' => 'bg-orange-100 text-orange-700',
                                        'returned_from_study' => 'bg-blue-100 text-blue-700',
                                    ];
                                    $color = $statusColors[$c->counselor_status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="text-[10px] px-1.5 py-0.5 font-medium {{ $color }}">
                                    {{ $c->counselor_status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @php
                                    $profileColors = [
                                        'draft' => 'bg-gray-100 text-gray-600',
                                        'pending_review' => 'bg-amber-100 text-amber-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'changes_requested' => 'bg-red-100 text-red-700',
                                    ];
                                    $profileColor = $profileColors[$c->counselor_profile_status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="text-[10px] px-1.5 py-0.5 font-medium {{ $profileColor }}">
                                    {{ $c->counselor_profile_status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-[10px] px-1.5 py-0.5 font-medium {{ $c->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $c->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('counselor-profile.show', $c) }}" class="px-2 py-1 text-[10px] text-teal-700 border border-teal-200 hover:bg-teal-50 font-medium">Profile</a>
                                    <a href="{{ route('staff.edit', $c) }}" class="px-2 py-1 text-[10px] text-gray-600 border border-gray-200 hover:bg-gray-50 font-medium">Edit</a>
                                    <form method="POST" action="{{ route('staff.destroy', $c) }}" onsubmit="return confirm('Are you sure you want to delete this counselor?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-[10px] text-red-600 border border-red-200 hover:bg-red-50 font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-gray-500">No counselors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination with Previous / Next --}}
        @if($counselors->hasPages())
        <div class="px-5 py-4 border-t border-teal-100 bg-teal-50/30">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Page {{ $counselors->currentPage() }} of {{ $counselors->lastPage() }}
                </div>
                <div class="flex gap-2">
                    @if($counselors->onFirstPage())
                        <span class="px-4 py-2 bg-gray-100 text-gray-400 text-sm cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $counselors->previousPageUrl() }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">Previous</a>
                    @endif

                    @if($counselors->hasMorePages())
                        <a href="{{ $counselors->nextPageUrl() }}" class="px-4 py-2 bg-teal-600 text-white text-sm hover:bg-teal-700">Next</a>
                    @else
                        <span class="px-4 py-2 bg-gray-100 text-gray-400 text-sm cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
