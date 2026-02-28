@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6 border-b border-gray-300 pb-4">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">System Settings</h2>
        <p class="text-sm text-gray-500">Configure bureau tracking system parameters</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        @foreach($settings as $group => $items)
            <div class="bg-white border border-gray-200 p-6 mb-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4 capitalize">{{ $group }} Settings</h3>

                <div class="space-y-4">
                    @foreach($items as $setting)
                        <div>
                            <label for="setting_{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $setting->description ?? ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>

                            @if($setting->type === 'boolean')
                                <label class="flex items-center gap-2">
                                    <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                    <input type="checkbox" name="settings[{{ $setting->key }}]" value="1"
                                           {{ $setting->value ? 'checked' : '' }}
                                           class="h-4 w-4 text-slate-600 border-gray-300 rounded focus:ring-slate-500">
                                    <span class="text-sm text-gray-600">Enabled</span>
                                </label>
                            @elseif($setting->type === 'integer')
                                <input type="number" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}"
                                       value="{{ $setting->value }}"
                                       class="w-32 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}"
                                       value="{{ $setting->value }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium hover:bg-slate-700">Save Settings</button>
    </form>
</div>
@endsection
