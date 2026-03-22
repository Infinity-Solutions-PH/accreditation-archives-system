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
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();

        if (!$currentUser->hasRole(['admin', 'ido_staff', 'college_officer'])) {
            abort(403, 'Unauthorized access to User Management.');
        }

        $query = User::with(['googleInfo', 'roles', 'college', 'program']);

        if ($currentUser->hasRole('college_officer') && !$currentUser->hasRole(['admin', 'ido_staff'])) {
            $query->where('college_id', $currentUser->college_id);
        }

        $activeUsers = (clone $query)->where('is_active', true)->count();
        $inactiveUsers = (clone $query)->where('is_active', false)->count();
        $pendingUsers = (clone $query)->where('role_status', 'pending')->count();
        $officerUsers = (clone $query)->whereHas('roles', fn($q) => $q->where('name', 'college_officer'))->count();

        $users = $query->get();
        
        $roles =Role::all();
        $colleges = College::all();
        $programs = Program::all();

        return Inertia::render('UserManagement/Index', [
            'userStats' => [
                'active' => $activeUsers,
                'inactive' => $inactiveUsers,
                'pending' => $pendingUsers,
                'officers' => $officerUsers,
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

        if ($request->role === 'college_officer' && $request->role_status === 'approved') {
            $collegeId = $request->has('college_id') ? $request->college_id : $user->college_id;
            $existingOfficer = User::where('college_id', $collegeId)
                    ->where('id', '!=', $user->id)
                    ->whereHas('roles', fn($q) => $q->where('name', 'college_officer'))
                    ->first();
            if ($existingOfficer) {
                return response()->json(['message' => 'This college already has a College Accreditation Officer.'], 422);
            }
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
