@extends('layouts.app')

@section('title', 'Compose Message')
@section('page-title', 'Compose Message')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('messages.index') }}" class="text-xs text-blue-700 hover:underline">Back to Messages</a>
    </div>

    <div class="bg-white border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">
            @if($replyTo)
                Reply to: {{ $replyTo->subject }}
            @else
                New Message
            @endif
        </h2>

        <form method="POST" action="{{ route('messages.store') }}">
            @csrf

            @if($replyTo)
                <input type="hidden" name="parent_id" value="{{ $replyTo->id }}">
                <input type="hidden" name="receiver_id" value="{{ $replyTo->sender_id === auth()->id() ? $replyTo->receiver_id : $replyTo->sender_id }}">
                <input type="hidden" name="subject" value="Re: {{ $replyTo->subject }}">

                <div class="mb-4 p-4 bg-gray-50 border border-gray-200">
                    <p class="text-xs text-gray-400 mb-1">Replying to {{ $replyTo->sender_id === auth()->id() ? $replyTo->receiver->name : $replyTo->sender->name }}</p>
                    <p class="text-sm text-gray-600">{{ Str::limit($replyTo->body, 200) }}</p>
                </div>
            @else
                <div class="mb-4">
                    <label for="receiver_id" class="block text-sm font-medium text-gray-700 mb-1">To *</label>
                    <select name="receiver_id" id="receiver_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                        <option value="">Select recipient...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->role_label }}{{ $user->division ? ' — ' . $user->division->name : '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('receiver_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="mb-6">
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                <textarea name="body" id="body" rows="8" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                          placeholder="Type your message...">{{ old('body') }}</textarea>
                @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">
                    Send Message
                </button>
                <a href="{{ route('messages.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
