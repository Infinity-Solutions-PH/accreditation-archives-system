<?php

namespace App\Http\Controllers\Accreditor;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/auth');
    }

    public function index()
    {
        return Inertia::render('Accreditor/Auth');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('accreditor')->attempt($credentials)) {
            $user = Auth::guard('accreditor')->user();
            
            if ($user->expires_at && $user->expires_at->isPast()) {
                Auth::guard('accreditor')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Your account has expired.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('accreditor.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
