@extends('layouts.app')

@section('title', $message->subject)
@section('page-title', 'Message Thread')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('messages.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Messages</a>
        <form method="POST" action="{{ route('messages.destroy', $message) }}" onsubmit="return confirm('Delete this conversation?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm text-red-500 hover:text-red-700">Delete</button>
        </form>
    </div>

    {{-- Subject header --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5 mb-4">
        <h2 class="text-lg font-semibold text-gray-800">{{ $message->subject }}</h2>
        <p class="text-xs text-gray-400 mt-1">
            Between <span class="font-medium text-gray-600">{{ $message->sender->name }}</span>
            and <span class="font-medium text-gray-600">{{ $message->receiver->name }}</span>
        </p>
    </div>

    {{-- Original message --}}
    <div class="space-y-3 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-5 {{ $message->sender_id === auth()->id() ? 'border-l-4 border-l-slate-400' : 'border-l-4 border-l-blue-400' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-slate-600">{{ strtoupper(substr($message->sender->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $message->sender->name }}</p>
                        <p class="text-xs text-gray-400">{{ $message->sender->role_label }}</p>
                    </div>
                </div>
                <span class="text-xs text-gray-400">{{ $message->created_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $message->body }}</div>
        </div>

        {{-- Replies --}}
        @foreach($message->replies as $reply)
            <div class="bg-white rounded-lg border border-gray-200 p-5 {{ $reply->sender_id === auth()->id() ? 'border-l-4 border-l-slate-400' : 'border-l-4 border-l-blue-400' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-slate-600">{{ strtoupper(substr($reply->sender->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $reply->sender->name }}</p>
                            <p class="text-xs text-gray-400">{{ $reply->sender->role_label }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">{{ $reply->created_at->format('M d, Y \a\t h:i A') }}</span>
                </div>
                <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $reply->body }}</div>
            </div>
        @endforeach
    </div>

    {{-- Reply form --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Reply</h3>
        <form method="POST" action="{{ route('messages.reply', $message) }}">
            @csrf
            <textarea name="body" rows="4" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500"
                      placeholder="Type your reply...">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

            <div class="mt-3 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">
                    Send Reply
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
