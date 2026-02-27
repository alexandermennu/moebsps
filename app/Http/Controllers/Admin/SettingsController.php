<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            SystemSetting::where('key', $key)->update([
                'value' => $value ?? '',
            ]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
