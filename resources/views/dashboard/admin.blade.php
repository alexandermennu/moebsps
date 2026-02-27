@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">System Administration</h2>
        <p class="text-sm text-gray-500">Welcome back, {{ $user->name }}</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Users</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Active Users</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_users'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Divisions</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_divisions'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Assignments</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_activities'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Overdue</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue_activities'] }}</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.users.create') }}" class="bg-white rounded-lg border border-gray-200 p-5 hover:border-slate-400 transition group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-slate-200">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Add New User</p>
                    <p class="text-xs text-gray-500">Create a user account</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.divisions.create') }}" class="bg-white rounded-lg border border-gray-200 p-5 hover:border-slate-400 transition group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-slate-200">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Add Division</p>
                    <p class="text-xs text-gray-500">Create a new division</p>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="bg-white rounded-lg border border-gray-200 p-5 hover:border-slate-400 transition group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-slate-200">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">System Settings</p>
                    <p class="text-xs text-gray-500">Configure the system</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Recent Users --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Recent Users</h3>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 hover:text-slate-800">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Name</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Email</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Role</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentUsers as $u)
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
