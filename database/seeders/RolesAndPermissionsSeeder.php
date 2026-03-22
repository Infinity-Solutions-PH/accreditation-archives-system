<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        // admin
        // ido_staff
        // college_officer
        // taskforce

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'ido_staff']);
        Role::create(['name' => 'college_officer']);
        Role::create(['name' => 'taskforce']);
    }
}
