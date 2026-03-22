<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@cvsu.edu.ph'],
            ['name' => 'Administrator', 'password' => bcrypt('password')]
        );
        $admin->assignRole('admin');

        $this->call([
            AreaSeeder::class,
            CollegeSeeder::class,
            ProgramSeeder::class,
        ]);
    }
}
