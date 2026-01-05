<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class LandingController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index() {
        return Inertia::render('Landing');
    }

    public function userManagement() {
        return Inertia::render('UserManagement/Index');
    }

    public function fileArchives() {
        return Inertia::render('FileRepository/Index');
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
