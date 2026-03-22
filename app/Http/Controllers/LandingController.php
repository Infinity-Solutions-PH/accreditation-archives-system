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
