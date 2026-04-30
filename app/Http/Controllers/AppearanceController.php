<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppearanceSetting;
use Illuminate\Support\Facades\Storage;

class AppearanceController extends Controller
{
    public function index()
    {
        $appearance = AppearanceSetting::first();
        if (!$appearance) {
            $appearance = AppearanceSetting::create([]);
        }
        return view('admin.appearance.index', compact('appearance'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'header_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico|max:1024',
        ]);

        $appearance = AppearanceSetting::first();
        if (!$appearance) {
            $appearance = AppearanceSetting::create([]);
        }

        if ($request->hasFile('header_logo')) {
            if ($appearance->header_logo) {
                Storage::disk('public')->delete($appearance->header_logo);
            }
            $appearance->header_logo = $request->file('header_logo')->store('appearance', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($appearance->favicon) {
                Storage::disk('public')->delete($appearance->favicon);
            }
            $appearance->favicon = $request->file('favicon')->store('appearance', 'public');
        }

        $appearance->save();

        return redirect()->route('admin.appearance.index')->with('success', 'Appearance settings updated successfully.');
    }
}
