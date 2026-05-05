<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\AccreditationEvent;

class AuthController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Auth/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (\App\Models\User::onlyTrashed()->where('email', $credentials['email'])->exists()) {
            return back()->withErrors([
                'email' => 'Your account has been deleted. Please contact the administrator.',
            ]);
        }

        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            if (!$user->is_active) {
                auth()->logout();
                return back()->withErrors([
                    'email' => 'Your account is inactive or deleted. Please contact the administrator.',
                ]);
            }

            $request->session()->regenerate();

            activity()
                ->useLog('authentication')
                ->performedOn($user)
                ->causedBy($user)
                ->log('User logged in via institutional credentials.');

            if ($user->role_status === 'pending') {
                return redirect()->route('onboarding.pending');
            } elseif ($user->role_status === 'rejected') {
                return redirect()->route('onboarding.rejected');
            }
            
            if ($user->hasRole('taskforce')) {
                return redirect()->route('file-archives', ['type' => 'personal']);
            }

            if ($user->hasRole('accreditor')) {
                $latestEvent = AccreditationEvent::where('status', 'active')->latest()->first();
                
                if (!$latestEvent) {
                    return redirect()->route('accreditor.no-active-event');
                }

                return redirect()->route('file-archives', [
                    'type' => 'event',
                    'event_id' => $latestEvent->id
                ]);
            }
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
