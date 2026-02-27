@extends('layouts.app')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Users</h2>
            <p class="text-sm text-gray-500">Manage system user accounts</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">
            + Add User
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
                   class="px-3 py-2 border border-gray-300 rounded-md text-sm w-48">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Role</label>
            <select name="role" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">All Roles</option>
                @foreach(\App\Models\User::ROLES as $key => $label)
                    <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm rounded-md hover:bg-gray-200">Filter</button>
    </form>

    {{-- Main Staff Table (non-counselors) --}}
    @if(!request('role') || request('role') !== 'counselor')
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Staff Members</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Name</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Email</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Role</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Division</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Status</th>
                        <th class="text-right px-5 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ $u->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $u->email }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700">{{ $u->role_label }}</span>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $u->division?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-xs px-2 py-1 rounded-full {{ $u->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $u->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="text-sm text-slate-600 hover:text-slate-800">Edit</a>
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sm {{ $u->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}">
                                            {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Are you sure you want to permanently delete {{ $u->name }}? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-500">No staff members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $users->links() }}
    @endif

    {{-- Counselors Section --}}
    @if($counselors->count() > 0 || request('role') === 'counselor')
    <div class="bg-white rounded-lg border border-blue-200">
        <div class="px-5 py-3 border-b border-blue-100 bg-blue-50 rounded-t-lg">
            <div class="flex items-center gap-2">
                <span class="text-lg">🎓</span>
                <h3 class="text-sm font-semibold text-blue-800">Counselors</h3>
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-200 text-blue-800">{{ $counselors->count() }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-blue-50/50">
                    <tr>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Name</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Email</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">School of Assignment</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">County</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Current Status</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Account</th>
                        <th class="text-right px-5 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($counselors as $c)
                        <tr class="hover:bg-blue-50/30">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ $c->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $c->email }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ $c->counselor_school ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ $c->counselor_county ?? '—' }}</td>
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
                                <span class="text-xs px-2 py-1 rounded-full {{ $color }}">
                                    {{ $c->counselor_status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-xs px-2 py-1 rounded-full {{ $c->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $c->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $c) }}" class="text-sm text-slate-600 hover:text-slate-800">Edit</a>
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $c) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sm {{ $c->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}">
                                            {{ $c->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    @if($c->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $c) }}" onsubmit="return confirm('Are you sure you want to permanently delete {{ $c->name }}? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-gray-500">No counselors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
