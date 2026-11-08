<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Accreditor;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Activitylog\Models\Activity;
use App\Http\Resources\ActivityLogResource;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index(Request $request)
    {
        $query = Activity::with(['causer' => function (MorphTo $morphTo) {
            $morphTo->morphWith([
                User::class => ['roles', 'googleInfo'],
                Accreditor::class => ['googleInfo'],
            ]);
        }])->latest();
        
        // Search filter (User name/email or Description)
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHasMorph('causer', [User::class, Accreditor::class], function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Action Type Filter (log_name)
        if ($type = $request->input('type')) {
            $query->where('log_name', $type);
        }

        // Role Filter
        if ($role = $request->input('role')) {
            if ($role === 'accreditor') {
                $query->where('causer_type', Accreditor::class);
            } else {
                $query->whereHasMorph('causer', [User::class], function($userQuery) use ($role) {
                    $userQuery->whereHas('roles', function($roleQuery) use ($role) {
                        $roleQuery->where('name', $role);
                    });
                });
            }
        }

        $logs = $query->paginate(15)->withQueryString();

        return Inertia::render('ActivityLogs/Index', [
            'logs' => $logs->through(fn($log) => ActivityLogResource::make($log)->resolve()),
            'filters' => $request->only(['search', 'type', 'role']),
            'logTypes' => Activity::distinct()->pluck('log_name'),
            'roles' => Role::all()
        ]);
    }
}
