<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index() {
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        $users = User::with('googleInfo')->get();

        return Inertia::render('UserManagement/Index', [
            'userStats' => [
                'active' => $activeUsers,
                'inactive' => $inactiveUsers
            ],
            'users' => $users
        ]);
    }
}
