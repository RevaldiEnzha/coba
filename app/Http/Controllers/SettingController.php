<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('id')->get();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::where('key', $key)->update([
                'value' => $value,
            ]);
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
