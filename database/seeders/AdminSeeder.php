<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@cvsu.edu.ph'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'role_status' => 'approved',
                'is_active' => true,
            ]
        );

        $admin->assignRole('admin');
    }
}
