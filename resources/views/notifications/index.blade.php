@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="max-w-3xl space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Notifications</h2>
            <p class="text-sm text-gray-500">Stay updated on activities and approvals</p>
        </div>
        @if($notifications->where('is_read', false)->count() > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="text-sm text-slate-600 hover:text-slate-800">Mark all as read</button>
            </form>
        @endif
    </div>

    <div class="bg-white border border-gray-200">
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <a href="{{ route('notifications.read', $notification) }}"
                   class="block px-5 py-4 hover:bg-gray-50 {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex items-center gap-1.5">
                            @switch($notification->type)
                                @case('overdue')
                                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-amber-100 text-amber-800">Overdue</span>
                                    @break
                                @case('escalation')
                                    <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-red-100 text-red-800">Escalation</span>
                                    @break
                                @case('approval')
                                    <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-green-100 text-green-800">Approval</span>
                                    @break
                                @case('rejection')
                                    <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-red-100 text-red-800">Rejection</span>
                                    @break
                                @default
                                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <span class="text-[10px] px-1.5 py-0.5 font-medium bg-gray-100 text-gray-700">Notice</span>
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
                    <p>No notifications yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{ $notifications->links() }}
</div>
@endsection
