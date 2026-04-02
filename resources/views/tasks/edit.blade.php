@extends('layouts.app')

@section('title', 'Edit Task')
@section('page-title', 'Edit Task')

@section('content')
<div class="max-w-2xl">
    {{-- Page Header --}}
    <div class="mb-6">
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to My Tasks
        </a>
        <h1 class="text-xl font-semibold text-gray-900">Edit Task</h1>
    </div>

    {{-- Form --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Task Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="What do you need to do?">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Add more details about this task...">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                {{-- Related To --}}
                <div>
                    <label for="related_to" class="block text-sm font-medium text-gray-700 mb-1">Related To <span class="text-red-500">*</span></label>
                    <select name="related_to" id="related_to" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        @foreach($relatedToOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('related_to', $task->related_to) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('related_to')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Priority --}}
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" id="priority" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        @foreach($priorityOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', $task->priority) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $task->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Due Date --}}
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                @error('due_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Task Info --}}
            <div class="bg-gray-50 rounded-md p-4 text-sm text-gray-600">
                <p>Created: {{ $task->created_at->format('M d, Y \a\t g:i A') }}</p>
                @if($task->completed_at)
                    <p>Completed: {{ $task->completed_at->format('M d, Y \a\t g:i A') }}</p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-5 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                        Save Changes
                    </button>
                    <a href="{{ route('tasks.index') }}" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2 bg-red-50 border border-red-200 text-red-600 text-sm font-medium rounded hover:bg-red-100">
                        Delete Task
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
