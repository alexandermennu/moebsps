@extends('layouts.app')

@section('title', 'Staff Approvals')
@section('page-title', 'Pending Staff Approvals')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Pending Staff Approvals</h2>
            <p class="text-sm text-gray-500">Review and approve staff members created by Division Directors</p>
        </div>
        @if($pendingStaff->total() > 0)
            <span class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-800 text-sm font-medium rounded-full">
                {{ $pendingStaff->total() }} pending
            </span>
        @endif
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Division</label>
            <select name="division_id" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">All Divisions</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm rounded-md hover:bg-gray-200">Filter</button>
        @if(request()->hasAny(['division_id']))
            <a href="{{ route('admin.staff-approvals.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>

    @if($pendingStaff->isEmpty())
        <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-gray-500">No pending staff approvals.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($pendingStaff as $staff)
                <div class="bg-white rounded-lg border border-amber-200 p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-700 font-bold">
                                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800">{{ $staff->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $staff->email }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-700">Pending</span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3 text-sm">
                                <div>
                                    <span class="text-gray-500">Role:</span>
                                    <span class="ml-1 font-medium text-gray-700">{{ $staff->role_label }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Division:</span>
                                    <span class="ml-1 font-medium text-gray-700">{{ $staff->division?->name ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Position:</span>
                                    <span class="ml-1 font-medium text-gray-700">{{ $staff->position ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Created by:</span>
                                    <span class="ml-1 font-medium text-gray-700">{{ $staff->createdByUser?->name ?? '—' }}</span>
                                </div>
                            </div>

                            <p class="text-xs text-gray-400 mt-2">Submitted {{ $staff->created_at->diffForHumans() }}</p>
                        </div>

                        <div class="flex items-center gap-2 ml-4">
                            <a href="{{ route('admin.staff-approvals.show', $staff) }}" class="px-4 py-2 bg-gray-100 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                                View
                            </a>
                            <form method="POST" action="{{ route('admin.staff-approvals.approve', $staff) }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                                        onclick="return confirm('Approve {{ $staff->name }} as {{ $staff->role_label }}?')">
                                    Approve
                                </button>
                            </form>

                            <button type="button"
                                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700"
                                    onclick="document.getElementById('reject-form-{{ $staff->id }}').classList.toggle('hidden')">
                                Reject
                            </button>
                        </div>
                    </div>

                    {{-- Rejection form (hidden by default) --}}
                    <form id="reject-form-{{ $staff->id }}" method="POST" action="{{ route('admin.staff-approvals.reject', $staff) }}" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        @csrf
                        <label for="rejection_reason_{{ $staff->id }}" class="block text-sm font-medium text-red-700 mb-1">Reason for rejection *</label>
                        <textarea name="rejection_reason" id="rejection_reason_{{ $staff->id }}" rows="2" required
                                  class="w-full px-3 py-2 border border-red-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                                  placeholder="Explain why this staff member is being rejected..."></textarea>
                        <div class="flex gap-2 mt-2">
                            <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Confirm Rejection</button>
                            <button type="button" class="px-3 py-1.5 bg-white border border-gray-300 text-sm rounded-md hover:bg-gray-50"
                                    onclick="document.getElementById('reject-form-{{ $staff->id }}').classList.add('hidden')">Cancel</button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>

        {{ $pendingStaff->links() }}
    @endif
</div>
@endsection
