<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\File;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Program;

class LandingController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index() {
        return Inertia::render('Landing');
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
