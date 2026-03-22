<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index(Request $request)
    {
        $query = Activity::with('causer.roles')->latest();
        
        if ($search = $request->input('search')) {
            $query->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%");
        }

        $logs = $query->paginate(15)->withQueryString();

        return Inertia::render('ActivityLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only(['search'])
        ]);
    }
}
