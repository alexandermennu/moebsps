@extends('layouts.app')

@section('title', 'Manage Divisions')
@section('page-title', 'Manage Divisions')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between border-b border-gray-300 pb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Divisions</h2>
            <p class="text-sm text-gray-500">Manage bureau divisions</p>
        </div>
        <a href="{{ route('admin.divisions.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">
            + Add Division
        </a>
    </div>

    <div class="bg-white border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Code</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Name</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Description</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Users</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Activities</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Status</th>
                        <th class="text-right px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($divisions as $division)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $division->code }}</td>
                            <td class="px-5 py-3 font-medium text-gray-800">{{ $division->name }}</td>
                            <td class="px-5 py-3 text-gray-600 max-w-xs truncate">{{ $division->description ?? '—' }}</td>
                            <td class="px-5 py-3 text-center text-gray-600">{{ $division->users_count }}</td>
                            <td class="px-5 py-3 text-center text-gray-600">{{ $division->activities_count }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-[10px] px-1.5 py-0.5 font-medium {{ $division->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $division->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.divisions.edit', $division) }}" class="text-xs text-blue-700 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-gray-500">No divisions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
