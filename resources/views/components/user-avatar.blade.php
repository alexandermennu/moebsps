@props([
    'user' => null,
    'size' => 'md',
    'class' => '',
])

@php
    $sizes = [
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-14 h-14 text-lg',
        'xl' => 'w-20 h-20 text-2xl',
    ];

    $imgSizes = [
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-14 h-14',
        'xl' => 'w-20 h-20',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $imgSizeClass = $imgSizes[$size] ?? $imgSizes['md'];
    $initials = $user ? $user->initials : '?';
    $hasPhoto = $user && $user->hasProfilePhoto();
    $photoUrl = $hasPhoto ? $user->profile_photo_url : null;
@endphp

@if($hasPhoto)
    <img src="{{ $photoUrl }}"
         alt="{{ $user->name }}"
         class="{{ $imgSizeClass }} rounded-full object-cover flex-shrink-0 {{ $class }}">
@else
    <div class="{{ $sizeClass }} bg-slate-200 text-slate-600 rounded-full flex items-center justify-center font-semibold flex-shrink-0 {{ $class }}" title="{{ $user?->name }}">
        {{ $initials }}
    </div>
@endif
