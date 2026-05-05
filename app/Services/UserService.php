<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserService
{
    /**
     * Create a new user with auto-generated password
     */
    public function create(array $data): User
    {
        $password = config('auth.password-default', Str::random(12));

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'college_id' => $data['college_id'],
            'program_id' => $data['program_id'] ?? null,
            'role_status' => 'approved', // Admin created accounts are pre-approved
            'is_active' => true,
        ]);

        // Assign Role via Spatie
        $user->assignRole($data['role']);

        // Optional: send password to user via email if requested
        if (!empty($data['send_email'])) {
            Mail::to($user->email)->queue(new WelcomeUserMail($user, $password));
            // For now, let's log this action
            Log::info("Welcome email requested for user: {$user->email} with password: {$password}");
        }

        return $user;
    }
}