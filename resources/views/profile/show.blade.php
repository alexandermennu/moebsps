@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-4xl">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Photo Card --}}
        <div class="bg-white border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide border-b border-gray-200 pb-2 mb-6">Profile Photo</h2>

            <div class="flex flex-col items-center">
                <x-user-avatar :user="$user" size="xl" />

                <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500">{{ $user->role_label }}</p>
                @if($user->division)
                    <p class="text-xs text-gray-400 mt-1">{{ $user->division->name }}</p>
                @endif

                <div class="mt-6 w-full space-y-2">
                    <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" id="photo-form">
                        @csrf
                        <label for="profile_photo" class="block w-full text-center px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700 cursor-pointer">
                            {{ $user->hasProfilePhoto() ? 'Change Photo' : 'Upload Photo' }}
                        </label>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/webp" class="hidden"
                               onchange="document.getElementById('photo-form').submit()">
                    </form>

                    @if($user->hasProfilePhoto())
                        <form method="POST" action="{{ route('profile.photo.remove') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50"
                                    onclick="return confirm('Remove your profile photo?')">
                                Remove Photo
                            </button>
                        </form>
                    @endif
                </div>

                @error('profile_photo')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <p class="mt-3 text-xs text-gray-400 text-center">JPG, PNG or WebP. Max 2MB.</p>
            </div>
        </div>

        {{-- Profile Details Card --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 p-6">
            <div class="flex items-center justify-between border-b border-gray-200 pb-2 mb-6">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Profile Details</h2>
                <a href="{{ route('profile.edit') }}" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium hover:bg-slate-700">
                    Edit Profile
                </a>
            </div>

            <dl class="divide-y divide-gray-100">
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $user->name }}</dd>
                </div>

                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $user->email }}</dd>
                </div>

                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="text-sm text-gray-900 col-span-2">
                        <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-medium">{{ $user->role_label }}</span>
                    </dd>
                </div>

                @if($user->division)
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Division</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $user->division->name }}</dd>
                </div>
                @endif

                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Position</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $user->position ?? '—' }}</dd>
                </div>

                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $user->phone ?? '—' }}</dd>
                </div>

                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                    <dd class="text-sm col-span-2">
                        @if($user->is_active)
                            <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium">Active</span>
                        @else
                            <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium">Inactive</span>
                        @endif
                    </dd>
                </div>

                @if($user->role === 'counselor')
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">School</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $user->counselor_school ?? '—' }}</dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">County</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $user->counselor_county ?? '—' }}</dd>
                </div>
                @endif

                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                    <dd class="text-sm text-gray-900 col-span-2">{{ $user->created_at->format('F j, Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
