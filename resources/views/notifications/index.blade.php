@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="max-w-3xl space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Notifications</h2>
            <p class="text-sm text-gray-500">Stay updated on activities and approvals</p>
        </div>
        @if($notifications->where('is_read', false)->count() > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="text-sm text-slate-600 hover:text-slate-800">Mark all as read</button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-lg border border-gray-200">
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <a href="{{ route('notifications.read', $notification) }}"
                   class="block px-5 py-4 hover:bg-gray-50 {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5">
                            @switch($notification->type)
                                @case('overdue')
                                    <span class="text-lg">⚠️</span>
                                    @break
                                @case('escalation')
                                    <span class="text-lg">🔺</span>
                                    @break
                                @case('approval')
                                    <span class="text-lg">✅</span>
                                    @break
                                @case('rejection')
                                    <span class="text-lg">❌</span>
                                    @break
                                @default
                                    <span class="text-lg">🔔</span>
                            @endswitch
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 {{ !$notification->is_read ? 'font-semibold' : '' }}">
                                {{ $notification->title }}
                            </p>
                            <p class="text-sm text-gray-600 mt-0.5">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notification->is_read)
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="px-5 py-12 text-center text-sm text-gray-500">
                    <p class="text-2xl mb-2">🔔</p>
                    <p>No notifications yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{ $notifications->links() }}
</div>
@endsection
