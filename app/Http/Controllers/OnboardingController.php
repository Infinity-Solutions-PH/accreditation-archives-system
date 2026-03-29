<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\College;
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
            'colleges' => College::orderBy('name')->get()
        ]);
    }

    public function storeCollege(StoreCollegeRequest $request)
    {
        $user = $this->onboardingService->completeCollegeSelection($request->college_id);

        if (!$user) {
            return redirect('/auth');
        }

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
