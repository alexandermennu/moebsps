@extends('layouts.app')

@section('title', 'Weekly Plans')
@section('page-title', 'Weekly Plans')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Weekly Plans</h2>
            <p class="text-sm text-gray-500">Plan and manage upcoming weekly activities</p>
        </div>
        @if($user->canManageDivision())
            <a href="{{ route('weekly-plans.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">
                + New Plan
            </a>
        @endif
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 text-sm rounded-md hover:bg-gray-200">Filter</button>
    </form>

    {{-- Plans Table --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Week</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Division</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Submitted By</th>
                        <th class="text-center px-5 py-3 text-gray-600 font-medium">Status</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-medium">Created</th>
                        <th class="text-right px-5 py-3 text-gray-600 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($plans as $plan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800">
                                {{ $plan->week_start->format('M d') }} - {{ $plan->week_end->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $plan->division->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $plan->submitter->name }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $plan->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $plan->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $plan->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $plan->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $plan->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('weekly-plans.show', $plan) }}" class="text-slate-600 hover:text-slate-800 text-sm">View →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-500">No weekly plans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $plans->links() }}
</div>
@endsection
