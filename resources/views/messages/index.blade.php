@extends('layouts.app')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('messages.index', ['folder' => 'inbox']) }}"
               class="px-4 py-2 text-sm font-medium {{ $folder === 'inbox' ? 'bg-slate-800 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                Inbox
                @if($unreadCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('messages.index', ['folder' => 'sent']) }}"
               class="px-4 py-2 text-sm font-medium {{ $folder === 'sent' ? 'bg-slate-800 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                Sent
            </a>
        </div>
        <a href="{{ route('messages.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">
            Compose
        </a>
    </div>

    <div class="bg-white border border-gray-200">
        <div class="divide-y divide-gray-100">
            @forelse($messages as $message)
                <a href="{{ route('messages.show', $message) }}"
                   class="block px-5 py-4 hover:bg-gray-50 {{ $folder === 'inbox' && !$message->is_read ? 'bg-blue-50' : '' }}">
                    <div class="flex items-center gap-4">
                        {{-- Unread indicator --}}
                        <div class="w-2 flex-shrink-0">
                            @if($folder === 'inbox' && !$message->is_read)
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            @endif
                        </div>

                        {{-- Avatar --}}
                        <x-user-avatar :user="$folder === 'inbox' ? $message->sender : $message->receiver" size="sm" />

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm {{ $folder === 'inbox' && !$message->is_read ? 'font-semibold text-gray-900' : 'font-medium text-gray-700' }}">
                                    {{ $folder === 'inbox' ? $message->sender->name : $message->receiver->name }}
                                </p>
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm {{ $folder === 'inbox' && !$message->is_read ? 'font-semibold text-gray-800' : 'text-gray-600' }} truncate">
                                {{ $message->subject }}
                            </p>
                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ Str::limit($message->body, 80) }}</p>
                        </div>

                        {{-- Reply count --}}
                        @if($message->replies_count ?? $message->replies->count() > 0)
                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 flex-shrink-0">
                                {{ $message->replies->count() }} {{ Str::plural('reply', $message->replies->count()) }}
                            </span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="px-5 py-12 text-center text-sm text-gray-500">
                    <p>No messages in your {{ $folder }}.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{ $messages->appends(['folder' => $folder])->links() }}
</div>
@endsection
