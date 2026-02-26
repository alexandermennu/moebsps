@extends('layouts.app')

@section('title', 'Edit Weekly Update')
@section('page-title', 'Edit Weekly Update')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('weekly-updates.show', $weeklyUpdate) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Update</a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Edit Weekly Update</h2>
        <p class="text-sm text-gray-500 mb-6">{{ $user->division?->name }}</p>

        <form method="POST" action="{{ route('weekly-updates.update', $weeklyUpdate) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="week_start" class="block text-sm font-medium text-gray-700 mb-1">Week Start</label>
                    <input type="date" name="week_start" id="week_start" value="{{ old('week_start', $weeklyUpdate->week_start->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
                <div>
                    <label for="week_end" class="block text-sm font-medium text-gray-700 mb-1">Week End</label>
                    <input type="date" name="week_end" id="week_end" value="{{ old('week_end', $weeklyUpdate->week_end->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                </div>
            </div>

            <div class="mb-4">
                <label for="accomplishments" class="block text-sm font-medium text-gray-700 mb-1">Accomplishments *</label>
                <textarea name="accomplishments" id="accomplishments" rows="5" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">{{ old('accomplishments', $weeklyUpdate->accomplishments) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="challenges" class="block text-sm font-medium text-gray-700 mb-1">Challenges</label>
                <textarea name="challenges" id="challenges" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">{{ old('challenges', $weeklyUpdate->challenges) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="support_needed" class="block text-sm font-medium text-gray-700 mb-1">Support Needed</label>
                <textarea name="support_needed" id="support_needed" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">{{ old('support_needed', $weeklyUpdate->support_needed) }}</textarea>
            </div>

            <div class="mb-6">
                <label for="key_metrics" class="block text-sm font-medium text-gray-700 mb-1">Key Metrics</label>
                <textarea name="key_metrics" id="key_metrics" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">{{ old('key_metrics', $weeklyUpdate->key_metrics) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" name="status" value="submitted" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">Submit for Review</button>
                <button type="submit" name="status" value="draft" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Save as Draft</button>
            </div>
        </form>
    </div>
</div>
@endsection
