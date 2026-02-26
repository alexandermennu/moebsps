@extends('layouts.app')

@section('title', 'New Weekly Plan')
@section('page-title', 'Create Weekly Plan')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('weekly-plans.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Weekly Plans</a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">New Weekly Plan</h2>
        <p class="text-sm text-gray-500 mb-6">{{ $user->division?->name }}</p>

        <form method="POST" action="{{ route('weekly-plans.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="week_start" class="block text-sm font-medium text-gray-700 mb-1">Week Start</label>
                    <input type="date" name="week_start" id="week_start" value="{{ old('week_start', now()->addWeek()->startOfWeek()->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="week_end" class="block text-sm font-medium text-gray-700 mb-1">Week End</label>
                    <input type="date" name="week_end" id="week_end" value="{{ old('week_end', now()->addWeek()->endOfWeek()->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>

            <div class="mb-4">
                <label for="planned_activities" class="block text-sm font-medium text-gray-700 mb-1">Planned Activities *</label>
                <textarea name="planned_activities" id="planned_activities" rows="5" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="List the activities planned for the coming week...">{{ old('planned_activities') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="objectives" class="block text-sm font-medium text-gray-700 mb-1">Objectives</label>
                <textarea name="objectives" id="objectives" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="What are the key objectives for this week?">{{ old('objectives') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="expected_outcomes" class="block text-sm font-medium text-gray-700 mb-1">Expected Outcomes</label>
                <textarea name="expected_outcomes" id="expected_outcomes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="What outcomes do you expect to achieve?">{{ old('expected_outcomes') }}</textarea>
            </div>

            <div class="mb-6">
                <label for="resources_needed" class="block text-sm font-medium text-gray-700 mb-1">Resources Needed</label>
                <textarea name="resources_needed" id="resources_needed" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="Any resources or support needed?">{{ old('resources_needed') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" name="status" value="submitted" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">Submit for Review</button>
                <button type="submit" name="status" value="draft" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Save as Draft</button>
            </div>
        </form>
    </div>
</div>
@endsection
