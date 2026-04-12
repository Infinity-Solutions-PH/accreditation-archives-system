<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RegistrationPendingNotification;

class OnboardingService
{
    /**
     * Handle the store college logic.
     *
     * @param int|string $collegeId
     * @return User|null
     * @throws \Exception
     */
    public function completeCollegeSelection($collegeId, $programId = null): ?User
    {
        $user = auth('web')->user();

        // 1. If user is already logged in, just update their college.
        if ($user) {
            $user->update([
                'college_id' => $collegeId,
                'program_id' => $programId,
                'role_status' => 'pending'
            ]);

            activity()
                ->useLog('onboarding')
                ->performedOn($user)
                ->causedBy($user)
                ->log("User updated college selection to: " . ($user->college->name ?? $collegeId));

            return $user;
        }

        // 2. If user is not logged in, retrieve from session.
        $pendingUser = session()->get('pending_google_user');
        if (!$pendingUser) {
            return null;
        }

        // 3. Create the User.
        $user = User::create([
            'name' => $pendingUser['name'],
            'email' => $pendingUser['email'],
            'password' => Hash::make(Str::random(12)),
            'college_id' => $collegeId,
            'program_id' => $programId,
            'role_status' => 'pending',
            'is_active' => true,
        ]);

        activity()
            ->useLog('onboarding')
            ->performedOn($user)
            ->causedBy($user)
            ->log("User completed onboarding and selected college: " . ($user->college->name ?? $collegeId));

        // 4. Create the linked GoogleInfo.
        $user->googleInfo()->create([
            'google_id' => $pendingUser['google_id'],
            'name' => $pendingUser['name'],
            'email' => $pendingUser['email'],
            'avatar' => $pendingUser['avatar'],
            'hd' => $pendingUser['hd'],
            'locale' => $pendingUser['locale'],
        ]);

        // 5. Cleanup session.
        session()->forget('pending_google_user');

        // 6. Log the user in.
        Auth::login($user);

        // 7. Notify the College Accreditation Officer (CAO)
        $cao = User::role('college_officer')
            ->where('college_id', $collegeId)
            ->get();
            
        Notification::send($cao, new RegistrationPendingNotification($user));

        return $user;
    }
}
