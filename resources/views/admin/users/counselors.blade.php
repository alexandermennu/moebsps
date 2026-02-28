@extends('layouts.app')

@section('title', 'Counselors')
@section('page-title', 'Counselors')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-700 hover:underline">Back to All Users</a>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mt-2">Counselors</h2>
            <p class="text-sm text-gray-500">CGPC Division — {{ $counselors->total() }} total counselors</p>
        </div>
        <a href="{{ route('admin.users.create') }}?prefill_role=counselor" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
            + Add Counselor
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, school..."
                   class="px-3 py-2 border border-gray-300 rounded-md text-sm w-56">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">County</label>
            <select name="county" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">All Counties</option>
                @foreach(\App\Models\User::COUNTIES as $county)
                    <option value="{{ $county }}" {{ request('county') === $county ? 'selected' : '' }}>{{ $county }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">All Statuses</option>
                @foreach(\App\Models\User::COUNSELOR_STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm hover:bg-gray-200">Filter</button>
        @if(request()->hasAny(['search', 'county', 'status']))
            <a href="{{ route('admin.users.counselors') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>

    {{-- Counselors Table --}}
    <div class="bg-white border border-blue-200">
        <div class="px-5 py-3 border-b border-blue-100 bg-blue-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-blue-800">Counselor List</h3>
                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-blue-200 text-blue-800">
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
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Email</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">School of Assignment</th>
                        <th class="text-left px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">County</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Current Status</th>
                        <th class="text-center px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Account</th>
                        <th class="text-right px-5 py-3 text-[11px] text-gray-500 uppercase tracking-wide font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($counselors as $c)
                        <tr class="hover:bg-blue-50/30">
                            <td class="px-5 py-3 text-gray-400 text-xs">{{ $counselors->firstItem() + $loop->index }}</td>
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
                                <span class="text-[10px] px-1.5 py-0.5 font-medium {{ $color }}">
                                    {{ $c->counselor_status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-[10px] px-1.5 py-0.5 font-medium {{ $c->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $c->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                @include('admin.users._actions', ['u' => $c])
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
        <div class="px-5 py-4 border-t border-blue-100 bg-blue-50/30">
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
                        <a href="{{ $counselors->nextPageUrl() }}" class="px-4 py-2 bg-blue-600 text-white text-sm hover:bg-blue-700">Next</a>
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