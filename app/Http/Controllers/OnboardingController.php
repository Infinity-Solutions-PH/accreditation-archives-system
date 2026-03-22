<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function __construct() {
        Inertia::setRootView('layouts/auth');
    }

    public function college()
    {
        $user = auth('web')->user();
        if (!$user && !session()->has('pending_google_user')) {
            return redirect('/auth');
        }
        
        return Inertia::render('Onboarding/CollegeSelection', [
            'colleges' => \App\Models\College::orderBy('name')->get()
        ]);
    }

    public function storeCollege(Request $request)
    {
        $request->validate(['college_id' => 'required|exists:colleges,id']);
        
        $user = auth('web')->user();

        if ($user) {
            $user->update([
                'college_id' => $request->college_id,
                'role_status' => 'pending'
            ]);
            return redirect()->route('onboarding.pending');
        }

        $pendingUser = session()->get('pending_google_user');
        if (!$pendingUser) return redirect('/auth');

        $user = User::create([
            'name' => $pendingUser['name'],
            'email' => $pendingUser['email'],
            'password' => Hash::make(Str::random(12)),
            'college_id' => $request->college_id,
            'role_status' => 'pending',
            'is_active' => true,
        ]);

        $user->googleInfo()->create([
            'google_id' => $pendingUser['google_id'],
            'name' => $pendingUser['name'],
            'email' => $pendingUser['email'],
            'avatar' => $pendingUser['avatar'],
            'hd' => $pendingUser['hd'],
            'locale' => $pendingUser['locale'],
        ]);

        session()->forget('pending_google_user');
        
        Auth::login($user);
        
        return redirect()->route('onboarding.pending');
    }

    public function pending()
    {
        $user = auth('web')->user();
        if (!$user || $user->role_status !== 'pending') {
            return redirect('/dashboard');
        }
        return Inertia::render('Onboarding/Pending');
    }

    public function rejected()
    {
        $user = auth('web')->user();
        if (!$user || $user->role_status !== 'rejected') {
            return redirect('/dashboard');
        }
        return Inertia::render('Onboarding/Rejected');
    }
}
