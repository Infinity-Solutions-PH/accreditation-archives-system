<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Program;
use App\Models\Accreditor;
use Illuminate\Http\Request;
use App\Models\AccreditationEvent;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AccreditorResource;

class UserManagementController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index(Request $request) {
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();

        if (!$currentUser->hasRole(['admin', 'ido_staff', 'college_officer'])) {
            abort(403, 'Unauthorized access to User Management.');
        }

        $tab = $request->input('tab', 'users');

        // 1. Calculate Statistics (Fixed - not affected by filters)
        $statQuery = User::query();
        $accreditorStatQuery = Accreditor::query();

        if ($currentUser->hasRole('college_officer') && !$currentUser->hasRole(['admin', 'ido_staff'])) {
            $statQuery->where('college_id', $currentUser->college_id);
            $accreditorStatQuery->where('college_id', $currentUser->college_id);
        }

        $userStats = [
            'active' => (clone $statQuery)->where('is_active', true)->where('role_status', 'approved')->count(),
            'pending' => (clone $statQuery)->where('role_status', 'pending')->count(),
            'officers' => (clone $statQuery)->whereHas('roles', fn($q) => $q->where('name', 'college_officer'))->count(),
            'accreditors' => $accreditorStatQuery->count(),
            'deleted_users' => (clone $statQuery)->onlyTrashed()->count(),
            'deleted_accreditors' => (clone $accreditorStatQuery)->onlyTrashed()->count(),
        ];

        // 2. Fetch Listing Data based on Tab
        if ($tab === 'accreditors' || $tab === 'deleted_accreditors') {
            $query = Accreditor::with(['college', 'program', 'creator', 'events']);
            
            if ($tab === 'deleted_accreditors') {
                $query->onlyTrashed();
            }

            if ($currentUser->hasRole('college_officer') && !$currentUser->hasRole(['admin', 'ido_staff'])) {
                $query->where('college_id', $currentUser->college_id);
            }

            // Apply Filters to Accreditors
            $query->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('college', fn($c) => $c->where('name', 'like', "%{$search}%"));
                });
            });

            if ($tab !== 'deleted_accreditors') {
                $query->when($request->status && $request->status !== 'All Status', function ($q) use ($request) {
                    switch (strtolower($request->status)) {
                        case 'active': $q->where('is_active', true)->where('role_status', 'approved'); break;
                        case 'pending': $q->where('role_status', 'pending'); break;
                        case 'inactive': $q->where('is_active', false); break;
                        case 'rejected': $q->where('role_status', 'rejected'); break;
                    }
                });
            }

            $data = $query->latest()->paginate(20)->withQueryString();
            $resources = AccreditorResource::collection($data);
        } else {
            $query = User::with(['googleInfo', 'roles', 'college', 'program']);

            if ($tab === 'deleted_users') {
                $query->onlyTrashed();
            }

            if ($currentUser->hasRole('college_officer') && !$currentUser->hasRole(['admin', 'ido_staff'])) {
                $query->where('college_id', $currentUser->college_id);
            }

            // Apply Filters to Users
            $query->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('college', fn($c) => $c->where('name', 'like', "%{$search}%"));
                });
            });

            $query->when($request->role && $request->role !== 'All Roles', function ($q) use ($request) {
                $roleSlug = str_replace(' ', '_', strtolower($request->role));
                $q->whereHas('roles', fn($r) => $r->where('name', $roleSlug));
            });

            if ($tab !== 'deleted_users') {
                $query->when($request->status && $request->status !== 'All Status', function ($q) use ($request) {
                    switch (strtolower($request->status)) {
                        case 'active': $q->where('is_active', true)->where('role_status', 'approved'); break;
                        case 'pending': $q->where('role_status', 'pending'); break;
                        case 'inactive': $q->where('is_active', false); break;
                        case 'rejected': $q->where('role_status', 'rejected'); break;
                    }
                });
            }

            $data = $query->latest()->paginate(20)->withQueryString();
            $resources = UserResource::collection($data);
        }
        
        return Inertia::render('UserManagement/Index', [
            'filters' => $request->only(['search', 'role', 'status', 'tab']),
            'userStats' => $userStats,
            'users' => $resources, // This will be the paginated resource collection
            'roles' => Role::all(),
            'colleges' => College::all(),
            'programs' => Program::all(),
            'events' => AccreditationEvent::all()
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

        // Ensure user has at least one of the required roles
        if (!$currentUser->hasRole(['admin', 'ido_staff', 'college_officer'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // If user is ONLY a college officer (not admin or ido_staff)
        if ($currentUser->hasRole('college_officer') && !$currentUser->hasRole(['admin', 'ido_staff'])) {
            if ($user->college_id !== $currentUser->college_id) {
                return response()->json(['message' => 'Unauthorized college user'], 403);
            }
            
            // Prevent college officer from assigning admin or ido_staff roles
            if ($request->has('role') && in_array($request->role, ['admin', 'ido_staff'])) {
                return response()->json(['message' => 'Unauthorized to assign this role'], 403);
            }
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
        
        if ($request->role_status === 'approved') {
            if ($request->role) {
                $user->syncRoles([$request->role]);
            } elseif ($user->roles->isEmpty()) {
                $user->assignRole('taskforce');
            }
        }

        activity()
            ->useLog('user_management')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Updated user status/role for: {$user->email}. New Status: {$request->role_status}, Role: " . ($request->role ?? 'N/A'));

        return back()->with('message', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        activity()
            ->useLog('user_management')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Soft deleted user: {$user->email}");

        return back()->with('message', 'User deleted successfully');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        activity()
            ->useLog('user_management')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Restored user: {$user->email}");

        return back()->with('message', 'User restored successfully');
    }

    public function destroyAccreditor(Accreditor $accreditor)
    {
        $accreditor->delete();

        activity()
            ->useLog('user_management')
            ->performedOn($accreditor)
            ->causedBy(auth()->user())
            ->log("Soft deleted accreditor: {$accreditor->email}");

        return back()->with('message', 'Accreditor deleted successfully');
    }

    public function restoreAccreditor($id)
    {
        $accreditor = Accreditor::withTrashed()->findOrFail($id);
        $accreditor->restore();

        activity()
            ->useLog('user_management')
            ->performedOn($accreditor)
            ->causedBy(auth()->user())
            ->log("Restored accreditor: {$accreditor->email}");

        return back()->with('message', 'Accreditor restored successfully');
    }
}
