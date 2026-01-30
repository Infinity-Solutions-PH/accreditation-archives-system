<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\GoogleUserInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        if (!$googleInfo) {
            if (!$user) {
                $password = Str::random(12);
                
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'must_change_password' => true,
                ]);
                // $user->assignRole('patient');
            }

            $googleInfo = $user->googleInfo()->create([
                'google_id' => $googleId,
                'name' => $name,
                'email' => $email,
                'hd' => $hd,
                'avatar' => $picture,
                'locale' => $locale,
                'access_token' => null,
                'refresh_token' => null,
                'expires_in' => null,
            ]);
        }

        $user->load('googleInfo');

        Auth::login($user);

        return redirect()->intended('/areas');
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
