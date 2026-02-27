@php $user = auth()->user(); @endphp

<aside class="fixed inset-y-0 left-0 w-64 bg-slate-800 text-white flex flex-col z-20">
    {{-- Logo / Brand --}}
    <div class="px-6 py-5 border-b border-slate-700">
        <h2 class="text-lg font-bold tracking-wide">MOEBSPS</h2>
        <p class="text-xs text-slate-400 mt-1">Bureau Activity Tracker</p>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 py-4 overflow-y-auto">
        <ul class="space-y-1 px-3">
            {{-- Dashboard --}}
            <li>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
            </li>

            {{-- Activities --}}
            <li>
                <a href="{{ route('activities.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('activities.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    {{ $user->hasPersonalAccessOnly() ? 'My Tasks' : 'Activities' }}
                </a>
            </li>

            {{-- Weekly Updates (only for users with division access or higher) --}}
            @if(!$user->hasPersonalAccessOnly())
            <li>
                <a href="{{ route('weekly-updates.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('weekly-updates.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Weekly Updates
                </a>
            </li>

            {{-- Weekly Plans --}}
            <li>
                <a href="{{ route('weekly-plans.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('weekly-plans.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Weekly Plans
                </a>
            </li>
            @endif

            {{-- Director Staff Management --}}
            @if($user->canCreateStaff())
            <li>
                <a href="{{ route('staff.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('staff.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    My Staff
                </a>
            </li>
            @endif

            {{-- Messages --}}
            <li>
                @php $unreadMsgCount = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->where('receiver_deleted', false)->whereNull('parent_id')->count(); @endphp
                <a href="{{ route('messages.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('messages.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Messages
                    <span data-sidebar-badge="messages" class="ml-auto bg-blue-500 text-white text-xs rounded-full px-2 py-0.5 {{ $unreadMsgCount > 0 ? '' : 'hidden' }}">{{ $unreadMsgCount }}</span>
                </a>
            </li>

            {{-- Notifications --}}
            <li>
                <a href="{{ route('notifications.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('notifications.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifications
                    <span data-sidebar-badge="notifications" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5 {{ $user->unreadNotificationCount() > 0 ? '' : 'hidden' }}">{{ $user->unreadNotificationCount() }}</span>
                </a>
            </li>

            @if($user->isAdmin() || $user->isMinister())
                <li class="pt-4">
                    <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Administration</p>
                </li>

                @php $pendingApprovalCount = \App\Models\User::pendingApproval()->count(); @endphp
                <li>
                    <a href="{{ route('admin.staff-approvals.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('admin.staff-approvals.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Staff Approvals
                        @if($pendingApprovalCount > 0)
                            <span class="ml-auto bg-amber-500 text-white text-xs rounded-full px-2 py-0.5">{{ $pendingApprovalCount }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('admin.users.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                        Manage Users
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.divisions.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('admin.divisions.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Manage Divisions
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.settings.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('admin.settings.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Settings
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    {{-- User Info at Bottom --}}
    <div class="px-4 py-3 border-t border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-slate-600 rounded-full flex items-center justify-center text-sm font-bold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $user->division?->name ?? $user->role_label }}</p>
            </div>
        </div>
    </div>
</aside>
