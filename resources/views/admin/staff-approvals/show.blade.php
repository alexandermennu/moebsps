@extends('layouts.app')

@section('title', 'Review Staff')
@section('page-title', 'Review Staff Member')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.staff-approvals.index') }}" class="text-xs text-blue-700 hover:underline">Back to Pending Approvals</a>
    </div>

    <div class="bg-white border border-gray-200 p-6 space-y-6">
        {{-- Header --}}
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
            <span class="text-[10px] px-1.5 py-0.5 font-medium bg-amber-100 text-amber-700">Pending Approval</span>
        </div>

        {{-- Details --}}
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Role</span>
                <p class="font-medium text-gray-800 mt-0.5">{{ $user->role_label }}</p>
            </div>
            <div>
                <span class="text-gray-500">Division</span>
                <p class="font-medium text-gray-800 mt-0.5">{{ $user->division?->name ?? '—' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Position</span>
                <p class="font-medium text-gray-800 mt-0.5">{{ $user->position ?? '—' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Phone</span>
                <p class="font-medium text-gray-800 mt-0.5">{{ $user->phone ?? '—' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Created By</span>
                <p class="font-medium text-gray-800 mt-0.5">{{ $user->createdByUser?->name ?? 'System' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Submitted</span>
                <p class="font-medium text-gray-800 mt-0.5">{{ $user->created_at->format('M d, Y \a\t g:ia') }}</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="border-t border-gray-200 pt-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700">Actions</h3>

            <div class="flex gap-3">
                {{-- Approve --}}
                <form method="POST" action="{{ route('admin.staff-approvals.approve', $user) }}"
                      onsubmit="return confirm('Approve {{ $user->name }}? They will be able to log in immediately.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-5 py-2 bg-green-600 text-white text-sm font-medium hover:bg-green-700">
                        Approve
                    </button>
                </form>

                {{-- Toggle Reject Form --}}
                <button type="button" onclick="document.getElementById('rejectForm').classList.toggle('hidden')"
                        class="inline-flex items-center px-5 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                    Reject
                </button>
            </div>

            {{-- Reject Form --}}
            <form id="rejectForm" method="POST" action="{{ route('admin.staff-approvals.reject', $user) }}" class="hidden">
                @csrf
                <div class="bg-red-50 border border-red-200 p-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-red-700 mb-2">Reason for Rejection *</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                              placeholder="Explain why this staff account is being rejected..."
                              class="w-full px-3 py-2 border border-red-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="mt-3 px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
