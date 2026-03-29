<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use App\Models\Accreditor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\GoogleUserInfo;
use App\Models\AccreditationEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $googleId = $googleUser->getId() ?? null;
        $email = $googleUser->getEmail() ?? null;
        $name = $googleUser->getName() ?? null;
        $picture = $googleUser->getAvatar() ?? null;
        $hd = $googleUser->user['hd'] ?? null;
        $locale = $googleUser->user['locale'] ?? null;

        if (!$googleId || !$email) {
            return response()->json([
                'error' => 'Google token missing required fields'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Find existing google user info
        $googleInfo = GoogleUserInfo::where('google_id', $googleId)->first();
        $user = User::where('email', $email)->first();
        $accreditor = Accreditor::where('email', $email)->first();

        // Enforce domain check
        if (!$accreditor) {
            $strictDomains = (bool) Setting::get('auth_strict_domains', true);
            $whitelistedDomainsString = Setting::get('auth_whitelisted_domains', 'cvsu.edu.ph');
            $whitelistedDomains = array_map('trim', explode(',', $whitelistedDomainsString));

            $isDomainValid = false;
            foreach ($whitelistedDomains as $whitelistedDomain) {
                if (Str::endsWith($email, '@' . $whitelistedDomain)) {
                    $isDomainValid = true;
                    break;
                }
            }

            // If it's a new regular user and domain check is strict, it must pass
            // Existing users are allowed regardless of domain (they were manually added)
            if (!$user && $strictDomains && !$isDomainValid) {
                return redirect('/auth')->withErrors(['email' => 'Your email domain is not authorized for staff registration.']);
            }
        }

        if (!$user && !$accreditor) {
            session()->put('pending_google_user', [
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $picture,
                'hd' => $hd,
                'locale' => $locale,
            ]);
            return redirect()->route('onboarding.college');
        }

        if (!$googleInfo) {
            if ($user) {
                $googleInfo = $user->googleInfo()->create([
                    'google_id' => $googleId,
                    'name' => $name,
                    'email' => $email,
                    'hd' => $hd,
                    'avatar' => $picture,
                    'locale' => $locale,
                ]);
            } elseif ($accreditor) {
                $googleInfo = $accreditor->googleInfo()->create([
                    'google_id' => $googleId,
                    'name' => $name,
                    'email' => $email,
                    'hd' => $hd,
                    'avatar' => $picture,
                    'locale' => $locale,
                ]);
            }
        }

        if ($user) {
            Auth::guard('web')->login($user);

            activity()
                ->useLog('authentication')
                ->performedOn($user)
                ->causedBy($user)
                ->log('User logged in via Google Auth');
            
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
        } elseif ($accreditor) {
            Auth::guard('accreditor')->login($accreditor);

            activity()
                ->useLog('authentication')
                ->performedOn($accreditor)
                ->causedBy($accreditor)
                ->log('Accreditor logged in via Google Auth');

            $latestEvent = AccreditationEvent::where('status', 'active')->latest()->first();

            if (!$latestEvent) {
                return redirect()->route('accreditor.no-active-event');
            }

            return redirect()->route('file-archives', [
                'type' => 'event',
                'event_id' => $latestEvent->id
            ]);
        }

        return redirect()->intended('/file-archives?type=personal');
    }
 
    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
    
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }
}
