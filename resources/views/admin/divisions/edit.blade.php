@extends('layouts.app')

@section('title', 'Edit Division')
@section('page-title', 'Edit Division')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.divisions.index') }}" class="text-xs text-blue-700 hover:underline">Back to Divisions</a>
    </div>

    <div class="bg-white border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-6">Edit Division: {{ $division->name }}</h2>

        <form method="POST" action="{{ route('admin.divisions.update', $division) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Division Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $division->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Division Code *</label>
                <input type="text" name="code" id="code" value="{{ old('code', $division->code) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">{{ old('description', $division->description) }}</textarea>
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $division->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-slate-600 border-gray-300 rounded focus:ring-slate-500">
                    <span class="text-sm text-gray-700">Active division</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">Update Division</button>
                <a href="{{ route('admin.divisions.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
