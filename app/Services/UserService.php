<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new user with auto-generated password
     */
    public function create(array $data): User
    {
        $password = Str::random(12); // generate 12-char random password

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
        ]);

        // Optional: send password to user via email
        // Mail::to($user->email)->send(new WelcomeUserMail($user, $password));

        // Attach plain password if you want to return it (optional)
        // $user->plain_password = $password;

        return $user;
    }
}