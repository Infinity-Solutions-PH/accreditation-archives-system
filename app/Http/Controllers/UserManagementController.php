<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Program;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index() {
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        $users = User::with(['googleInfo', 'roles', 'college', 'program'])->get();
        
        $roles =Role::all();
        $colleges = College::all();
        $programs = Program::all();

        return Inertia::render('UserManagement/Index', [
            'userStats' => [
                'active' => $activeUsers,
                'inactive' => $inactiveUsers
            ],
            'users' => $users,
            'roles' => $roles,
            'colleges' => $colleges,
            'programs' => $programs
        ]);
    }
    public function updateRoleStatus(Request $request, User $user)
    {
        $request->validate([
            'role_status' => 'required|in:pending,approved,rejected',
            'role' => 'nullable|string',
            'college_id' => 'nullable|exists:colleges,id',
            'program_id' => 'nullable|exists:programs,id',
            'is_active' => 'nullable|boolean'
        ]);

        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();

        // If College Officer, they can only approve for their own college
        if ($currentUser->hasRole('college_officer')) {
            if ($user->college_id !== $currentUser->college_id) {
                return response()->json(['message' => 'Unauthorized college user'], 403);
            }
        } else if (!$currentUser->hasRole('admin') && !$currentUser->hasRole('ido_staff')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->update([
            'role_status' => $request->role_status,
            'college_id' => $request->has('college_id') ? $request->college_id : $user->college_id,
            'program_id' => $request->has('program_id') ? $request->program_id : $user->program_id,
            'is_active' => $request->has('is_active') ? $request->is_active : $user->is_active,
        ]);
        
        if ($request->role_status === 'approved' && $request->role) {
            $user->syncRoles([$request->role]);
        }

        return response()->json(['message' => 'User updated successfully']);
    }
}
