<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\File;
use App\Models\User;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Program;
use Spatie\Activitylog\Models\Activity;

class LandingController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index() {
        $totalDocuments = File::count();
        $activeUsers = User::where('is_active', true)->where('role_status', 'approved')->count();
        $pendingApprovals = User::where('role_status', 'pending')->count();
        $expiringSoon = File::whereNotNull('expiration')
            ->where('expiration', '<=', now()->addDays(30))
            ->count();

        $pendingUsers = User::with(['college', 'roles', 'googleInfo'])
            ->where('role_status', 'pending')
            ->latest()
            ->limit(3)
            ->get();

        $expiringFiles = File::whereNotNull('expiration')
            ->where('expiration', '<=', now()->addDays(30))
            ->orderBy('expiration', 'asc')
            ->limit(5)
            ->get();

        $recentActivity = Activity::with('causer')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'causer_name' => $activity->causer ? $activity->causer->name : 'System',
                    'created_at_human' => $activity->created_at->diffForHumans(),
                    'properties' => $activity->properties,
                ];
            });

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $isAdministrative = $user->hasRole(['admin', 'ido_staff', 'college_officer']);

        return Inertia::render('Landing', [
            'counts' => [
                'totalDocuments' => $totalDocuments,
                'activeUsers' => $activeUsers,
                'pendingApprovals' => $isAdministrative ? $pendingApprovals : 0,
                'expiringSoon' => $expiringSoon,
            ],
            'pendingUsers' => $isAdministrative ? $pendingUsers : [],
            'expiringFiles' => $expiringFiles,
            'recentActivity' => $recentActivity,
        ]);
    }

    public function profile() {
        /** @var \App\Models\User $user */
        $user = auth()->user()->load(['college', 'program', 'roles', 'permissions']);
        
        return Inertia::render('Profile/Index', [
            'user' => $user
        ]);
    }

    public function taskforce() {
        /** @var \App\Models\User|null $user */
        $user = auth('web')->user();
        $collegeStats = [];

        if ($user && $user->hasRole(['admin', 'ido_staff'])) {
            $collegeStats = College::with(['users' => function($q) {
                $q->whereHas('roles', fn($r) => $r->where('name', 'college_officer'));
            }])->withCount([
                'users as active_users_count' => fn($q) => $q->where('is_active', true)->where('role_status', 'approved'),
                'users as inactive_users_count' => fn($q) => $q->where('is_active', false)->where('role_status', 'approved')
            ])->get()->map(function($college) {
                $officer = $college->users->first();
                return [
                    'id' => $college->id,
                    'name' => $college->name,
                    'code' => $college->code,
                    'officer' => $officer ? $officer->name : 'Unassigned',
                    'active_users' => $college->active_users_count,
                    'inactive_users' => $college->inactive_users_count,
                ];
            });
        }

        return Inertia::render('Taskforce/Index', [
            'collegeStats' => $collegeStats
        ]);
    }

    public function fileArchives() {
        $files = File::with('college', 'program', 'uploadedBy.googleInfo')
            ->where('is_general', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return Inertia::render('FileRepository/Index', [
            'files' => $files,
            'colleges' => College::orderBy('name')->get(),
            'programs' => Program::orderBy('name')->get(),
            'areas' => Area::orderBy('order_no')->get(),
        ]);
    }

    public function activityLogs() {
        return Inertia::render('ActivityLogs/Index');
    }

    public function staff() {
        return Inertia::render('UserManagement/Staff');
    }

    public function fileShare() {
        return Inertia::render('FileRepository/Share');
    }
}
