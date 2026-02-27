@extends('layouts.app')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Users</h2>
            <p class="text-sm text-gray-500">Manage system user accounts — organized by division</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">
            + Add User
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
                   class="px-3 py-2 border border-gray-300 rounded-md text-sm w-64">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm rounded-md hover:bg-gray-200">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>

    {{-- ══════════════════════════════════════════════════════
         Full Access Users (Minister, Admin Asst, Tech Asst)
         ══════════════════════════════════════════════════════ --}}
    @if($fullAccessUsers->count() > 0)
    <div class="bg-white rounded-lg border border-amber-200">
        <div class="px-5 py-3 border-b border-amber-100 bg-amber-50 rounded-t-lg">
            <div class="flex items-center gap-2">
                <span class="text-lg">⭐</span>
                <h3 class="text-sm font-semibold text-amber-800">Full Access Users</h3>
                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-200 text-amber-800">{{ $fullAccessUsers->count() }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-amber-50/50">
                    <tr>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Name</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Email</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Role</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Status</th>
                        <th class="text-right px-5 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($fullAccessUsers as $u)
                    <tr class="hover:bg-amber-50/30">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $u->name }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $u->email }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-700">{{ $u->role_label }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-xs px-2 py-1 rounded-full {{ $u->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $u->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @include('admin.users._actions', ['u' => $u])
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         Division Sections
         ══════════════════════════════════════════════════════ --}}
    @foreach($divisions as $division)
        @php
            $staff = $divisionStaff[$division->id] ?? collect();
            $hasCounselors = isset($counselorCounts[$division->id]) && $counselorCounts[$division->id] > 0;
            $isCGPC = $division->code === 'CGPC';
        @endphp

        @if($staff->count() > 0 || $isCGPC)
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-5 py-3 border-b border-gray-100 bg-slate-50 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🏢</span>
                        <h3 class="text-sm font-semibold text-slate-800">{{ $division->name }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">{{ $staff->count() }} staff</span>
                    </div>
                    @if($isCGPC)
                        <a href="{{ route('admin.users.counselors') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                            🎓 Counselors
                            @if($hasCounselors)
                                <span class="px-2 py-0.5 rounded-full bg-blue-400 text-white text-xs font-bold">{{ $counselorCounts[$division->id] }}</span>
                            @endif
                        </a>
                    @endif
                </div>
            </div>
            @if($staff->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-5 py-3 text-gray-600 font-medium">Name</th>
                            <th class="text-left px-5 py-3 text-gray-600 font-medium">Email</th>
                            <th class="text-left px-5 py-3 text-gray-600 font-medium">Role</th>
                            <th class="text-center px-5 py-3 text-gray-600 font-medium">Status</th>
                            <th class="text-right px-5 py-3 text-gray-600 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($staff as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ $u->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $u->email }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700">{{ $u->role_label }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-xs px-2 py-1 rounded-full {{ $u->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $u->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                @include('admin.users._actions', ['u' => $u])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                @if(!$isCGPC)
                <div class="px-5 py-8 text-center text-gray-500 text-sm">No staff assigned to this division.</div>
                @endif
            @endif
        </div>
        @endif
    @endforeach

    {{-- ══════════════════════════════════════════════════════
         Users with no division
         ══════════════════════════════════════════════════════ --}}
    @if($noDivisionUsers->count() > 0)
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 rounded-t-lg">
            <div class="flex items-center gap-2">
                <span class="text-lg">📂</span>
                <h3 class="text-sm font-semibold text-gray-700">No Division Assigned</h3>
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-600">{{ $noDivisionUsers->count() }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Name</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Email</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Role</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Status</th>
                        <th class="text-right px-5 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($noDivisionUsers as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $u->name }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $u->email }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700">{{ $u->role_label }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-xs px-2 py-1 rounded-full {{ $u->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $u->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @include('admin.users._actions', ['u' => $u])
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
