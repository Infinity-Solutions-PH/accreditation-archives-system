<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return Inertia::render('Settings/Index', [
            'settings' => [
                'auth_strict_domains' => (bool) Setting::get('auth_strict_domains', true),
                'auth_whitelisted_domains' => Setting::get('auth_whitelisted_domains', 'cvsu.edu.ph'),
            ]
        ]);
    }

    public function update(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'auth_strict_domains' => 'required|boolean',
            'auth_whitelisted_domains' => 'nullable|string',
        ]);

        Setting::set('auth_strict_domains', $request->auth_strict_domains);
        Setting::set('auth_whitelisted_domains', $request->auth_whitelisted_domains);

        return back()->with('message', 'Settings updated successfully.');
    }
}
