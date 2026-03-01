@extends('layouts.app')

@section('title', 'My Staff')
@section('page-title', 'Division Staff Management')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between border-b border-gray-300 pb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">My Staff</h2>
            <p class="text-sm text-gray-500">Manage staff members in {{ $user->division?->name }}</p>
        </div>
        <a href="{{ route('staff.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">
            + Add Staff
        </a>
    </div>

    {{-- Info banner --}}
    <div class="bg-blue-50 border border-blue-200 p-3 text-sm text-blue-700">
        <strong>Note:</strong> New staff members require approval from an administrator before they can log in.
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-3 items-end flex-wrap">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
                   class="px-3 py-2 border border-gray-300 rounded-md text-sm w-48">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Role</label>
            <select name="role" class="px-3 py-2 border border-gray-300 text-sm">
                <option value="">All Roles</option>
                @foreach($roles as $key => $label)
                    <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 text-sm">
                <option value="">All Statuses</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm hover:bg-gray-200">Filter</button>
        @if(request()->hasAny(['search', 'role', 'status']))
            <a href="{{ route('staff.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>

    <div class="bg-white border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Name</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Email</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Role</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Position</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Approval</th>
                        <th class="text-right px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($staff as $member)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <x-user-avatar :user="$member" size="xs" />
                                    <span class="font-medium text-gray-800">{{ $member->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $member->email }}</td>
                            <td class="px-5 py-3">
                                <span class="text-[10px] px-1.5 py-0.5 font-medium
                                    @switch($member->role)
                                        @case('supervisor') bg-purple-100 text-purple-700 @break
                                        @case('coordinator') bg-blue-100 text-blue-700 @break
                                        @case('counselor') bg-teal-100 text-teal-700 @break
                                        @case('record_clerk') bg-orange-100 text-orange-700 @break
                                        @case('secretary') bg-gray-100 text-gray-700 @break
                                        @default bg-slate-100 text-slate-700
                                    @endswitch
                                ">{{ $member->role_label }}</span>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $member->position ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">
                                @if($member->approval_status === 'pending')
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-amber-100 text-amber-700">Pending</span>
                                @elseif($member->approval_status === 'approved')
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-green-100 text-green-700">Approved</span>
                                @elseif($member->approval_status === 'rejected')
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-red-100 text-red-700" title="{{ $member->rejection_reason }}">Rejected</span>
                                @else
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium {{ $member->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $member->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('staff.edit', $member) }}" class="text-xs text-blue-700 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('staff.destroy', $member) }}" onsubmit="return confirm('Are you sure you want to delete {{ $member->name }}? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                <p>No staff members found.</p>
                                <a href="{{ route('staff.create') }}" class="text-xs text-blue-700 hover:underline mt-1 inline-block">+ Add your first staff member</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $staff->links() }}
</div>
@endsection
