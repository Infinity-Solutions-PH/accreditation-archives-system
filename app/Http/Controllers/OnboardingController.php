<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\College;
use App\Models\User;
use App\Notifications\UserOnboardingNotification;
use App\Services\OnboardingService;
use App\Http\Requests\StoreCollegeRequest;

class OnboardingController extends Controller
{
    protected $onboardingService;

    public function __construct(OnboardingService $onboardingService) {
        Inertia::setRootView('layouts/auth');
        $this->onboardingService = $onboardingService;
    }

    public function college()
    {
        $user = auth('web')->user();
        if (!$user && !session()->has('pending_google_user')) {
            return redirect('/auth');
        }
        
        return Inertia::render('Onboarding/CollegeSelection', [
            'colleges' => College::orderBy('name')->get(),
            'programs' => \App\Models\Program::orderBy('name')->get()
        ]);
    }

    public function storeCollege(StoreCollegeRequest $request)
    {
        $user = $this->onboardingService->completeCollegeSelection(
            $request->college_id, 
            $request->program_id
        );

        if (!$user) {
            return redirect('/auth');
        }

        // Notify IDO Staff and relevant College Officer
        $recipients = User::role(['ido_staff', 'college_officer'])
            ->where(function($q) use ($user) {
                $q->whereHas('roles', fn($r) => $r->where('name', 'ido_staff'))
                  ->orWhere(function($sub) use ($user) {
                      $sub->whereHas('roles', fn($r) => $r->where('name', 'college_officer'))
                          ->where('college_id', $user->college_id);
                  });
            })
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new UserOnboardingNotification($user));
        }

        return redirect()->route('onboarding.pending');
    }

    public function pending()
    {
        $user = auth('web')->user();
        if (!$user) {
            return redirect()->route('auth');
        }

        // If user has no college_id, they must pick one before seeing 'pending'
        if (!$user->college_id) {
            return redirect()->route('onboarding.college');
        }

        if ($user->role_status !== 'pending') {
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

    public function expired()
    {
        $user = auth('accreditor')->user();
        if (!$user || ($user->expires_at && $user->expires_at >= now())) {
            return redirect('/dashboard');
        }

        return Inertia::render('Accreditor/Expired', [
            'accreditor' => $user
        ]);
    }

    public function noActiveEvent()
    {
        return Inertia::render('Auth/NoActiveEvent');
    }
}
