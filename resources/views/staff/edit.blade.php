@extends('layouts.app')

@section('title', 'Edit Staff')
@section('page-title', 'Edit Staff Member')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('staff.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Staff</a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Edit: {{ $staff->name }}</h2>
        <p class="text-sm text-gray-500 mb-6">Update staff member details and access level.</p>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('staff.update', $staff) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $staff->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $staff->email) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400">(leave blank to keep)</span></label>
                    <input type="password" name="password" id="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Access Level / Role *</label>
                <select name="role" id="role" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}" {{ old('role', $staff->role) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">
                    <strong>Supervisor / Coordinator / Counselor</strong> — Can view division activities (read-only).<br>
                    <strong>Record Clerk / Secretary</strong> — Can only see personally assigned tasks.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $staff->position) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $staff->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>

            {{-- Counselor-specific Fields --}}
            <div id="counselor-fields" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md" style="display: none;">
                <h3 class="text-sm font-semibold text-blue-800 mb-3">📋 Counselor Details</h3>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label for="counselor_school" class="block text-sm font-medium text-gray-700 mb-1">School of Assignment *</label>
                        <input type="text" name="counselor_school" id="counselor_school" value="{{ old('counselor_school', $staff->counselor_school) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    </div>
                    <div>
                        <label for="counselor_county" class="block text-sm font-medium text-gray-700 mb-1">County *</label>
                        <select name="counselor_county" id="counselor_county"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <option value="">Select County...</option>
                            @foreach(\App\Models\User::COUNTIES as $county)
                                <option value="{{ $county }}" {{ old('counselor_county', $staff->counselor_county) === $county ? 'selected' : '' }}>{{ $county }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label for="counselor_status" class="block text-sm font-medium text-gray-700 mb-1">Current Status *</label>
                    <select name="counselor_status" id="counselor_status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        @foreach(\App\Models\User::COUNSELOR_STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ old('counselor_status', $staff->counselor_status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($staff->isPending())
                <div class="mb-6 p-3 bg-amber-50 border border-amber-200 rounded-md text-sm text-amber-700">
                    <strong>⏳ Pending Approval</strong> — This account is awaiting administrator approval. Active status cannot be changed until approved.
                </div>
            @elseif($staff->isRejected())
                <div class="mb-6 p-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-700">
                    <strong>✗ Rejected</strong> — {{ $staff->rejection_reason ?? 'This account was rejected by an administrator.' }}
                </div>
            @endif

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">Update Staff</button>
                <a href="{{ route('staff.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const counselorFields = document.getElementById('counselor-fields');

        function toggleCounselorFields() {
            counselorFields.style.display = roleSelect.value === 'counselor' ? 'block' : 'none';
        }

        roleSelect.addEventListener('change', toggleCounselorFields);
        toggleCounselorFields();
    });
</script>
@endsection
