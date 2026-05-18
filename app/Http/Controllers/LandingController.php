<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\File;
use App\Models\User;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Models\AccreditationEvent;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;

class LandingController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    public function index() {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->hasRole('taskforce')) {
            return redirect()->route('file-archives', ['type' => 'personal']);
        }

        $totalDocumentsQuery = File::query();
        $expiringSoonQuery = AccreditationEvent::whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(30));
        $expiringEventsQuery = AccreditationEvent::whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(30))
            ->orderBy('expires_at', 'asc')
            ->limit(5);

        if ($user->hasRole('college_officer')) {
            $totalDocumentsQuery->where('college_id', $user->college_id);
            $expiringSoonQuery->where('college_id', $user->college_id);
            $expiringEventsQuery->where('college_id', $user->college_id);
        }

        $totalDocuments = $totalDocumentsQuery->count();
        $activeUsers = User::where('is_active', true)->where('role_status', 'approved')->count();
        $pendingApprovals = User::where('role_status', 'pending')->count();
        $expiringSoon = $expiringSoonQuery->count();

        $pendingUsers = User::with(['college', 'roles', 'googleInfo'])
            ->where('role_status', 'pending')
            ->latest()
            ->limit(3)
            ->get();

        $expiringEvents = $expiringEventsQuery->get();

        $recentActivity = Activity::with('causer')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'log_name' => $activity->log_name,
                    'description' => $activity->description,
                    'causer_name' => $activity->causer ? $activity->causer->name : 'System',
                    'created_at_human' => $activity->created_at->diffForHumans(),
                    'properties' => $activity->properties,
                ];
            });

        $isAdministrative = $user->hasRole(['admin', 'ido_staff', 'college_officer']);

        return Inertia::render('Landing', [
            'counts' => [
                'totalDocuments' => $totalDocuments,
                'activeUsers' => $activeUsers,
                'pendingApprovals' => $isAdministrative ? $pendingApprovals : 0,
                'expiringSoon' => $expiringSoon,
            ],
            'pendingUsers' => $isAdministrative ? $pendingUsers : [],
            'expiringEvents' => $expiringEvents,
            'recentActivity' => $recentActivity,
        ]);
    }

    public function profile(Request $request) {
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();
        $targetId = $request->query('id');

        if ($targetId && $targetId != $currentUser->id) {
            // Find target user
            $user = User::with(['college', 'program', 'roles', 'permissions', 'googleInfo'])->findOrFail($targetId);
            
            // Contextual validation
            if ($currentUser->hasRole('college_officer') && !$currentUser->hasRole(['admin', 'ido_staff'])) {
                if ($user->college_id !== $currentUser->college_id) {
                    abort(403, 'Unauthorized access to this user profile.');
                }
            } elseif ($user->hasRole('admin') && !$currentUser->hasRole('admin')) {
                abort(403, 'Unauthorized access to administrator profiles.');
            }
        } else {
            $user = $currentUser->load(['college', 'program', 'roles', 'permissions', 'googleInfo']);
        }
        
        return Inertia::render('Profile/Index', [
            'user' => $user,
            'isOwnProfile' => $user->id == $currentUser->id
        ]);
    }

    public function updateProfile(Request $request) {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        $user->name = $request->name;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('message', 'Profile updated successfully.');
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

    public function fileArchives(Request $request) {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $type = $request->get('type', 'general');
        $eventId = $request->get('event_id');

        // Force 'all' view for staff/admin, otherwise keep user requested type
        $isAdmin = $user->hasRole(['admin', 'ido_staff']);
        if ($isAdmin) {
            $type = 'all';
        }

        // Base query with accessibility constraints
        $query = File::with(['college', 'program', 'uploadedBy.googleInfo'])
            ->accessibleBy($user);

        // Partitioning: Narrow down based on drive type
        if ($type === 'personal') {
            $query->where('uploaded_by', $user->id)
                  ->where('is_general', false);
        } elseif ($type === 'shared') {
            $query->whereIn('id', $user->sharedFiles()->pluck('files.id'));
        } elseif ($type === 'event' && $eventId) {
            $query->whereHas('accreditationEvents', function($q) use ($eventId) {
                $q->where('accreditation_events.id', $eventId);
            });
        } elseif ($type === 'general') {
            $query->where('is_general', true);
        }

        // Filtering: Apply user-selected filters
        $search = $request->get('search');
        $status = $request->get('status');
        $programId = $request->get('program_id');
        $collegeIdFilter = $isAdmin ? $request->get('college_id') : $user->college_id;

        $query->when($search, function($q, $search) {
            $q->where(function($sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        });

        $query->when($status && $status !== 'All', function($q, $status) {
            $q->where('status', strtolower($status));
        });

        $query->when($programId && $programId !== 'All', function($q, $pid) {
            $q->where('program_id', $pid);
        });

        // Apply college filter dropdown (primarily for Admin/Staff)
        if ($type !== 'personal') {
            $query->when($collegeIdFilter && $collegeIdFilter !== 'All', function($q) use ($collegeIdFilter) {
                $q->where(function($sub) use ($collegeIdFilter) {
                    $sub->where('college_id', $collegeIdFilter)
                        ->orWhereNull('college_id');
                });
            });
        }

        // Sorting: Apply user-selected sort field and sort order
        $sortField = $request->get('sort_field', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (!in_array($sortField, ['title', 'created_at', 'college', 'program'])) {
            $sortField = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        if ($sortField === 'college') {
            $query->leftJoin('colleges', 'files.college_id', '=', 'colleges.id')
                  ->select('files.*')
                  ->orderBy('colleges.code', $sortOrder);
        } elseif ($sortField === 'program') {
            $query->leftJoin('programs', 'files.program_id', '=', 'programs.id')
                  ->select('files.*')
                  ->orderBy('programs.code', $sortOrder);
        } elseif ($sortField === 'title') {
            $query->orderBy('files.title', $sortOrder);
        } else {
            $query->orderBy('files.created_at', $sortOrder);
        }

        // Finalize results and render
        if ($type === 'event' && $eventId) {
            $event = AccreditationEvent::findOrFail($eventId);
            if ($user->hasRole('college_officer')) {
                if ($event->college_id !== $user->college_id) {
                    abort(403, 'Unauthorized.');
                }
            } elseif ($user->hasRole('taskforce')) {
                if ($event->college_id !== $user->college_id || $event->program_id !== $user->program_id) {
                    abort(403, 'Unauthorized.');
                }
            }

            $files = $query->get()->map(function($file) use ($event) {
                $attachedFile = $event->files()->where('file_id', $file->id)->first();
                $file->assigned_area_id = $attachedFile && $attachedFile->pivot ? $attachedFile->pivot->area_id : null;
                return $file;
            });
            $paginatedFiles = ['data' => $files];
        } else {
            $paginatedFiles = $query->paginate(10)
                ->withQueryString();
        }

        $activeEventsQuery = AccreditationEvent::where('status', 'active');
        if ($user->hasRole('college_officer')) {
            $activeEventsQuery->where('college_id', $user->college_id);
        } elseif ($user->hasRole('taskforce')) {
            $activeEventsQuery->where('college_id', $user->college_id)
                              ->where('program_id', $user->program_id);
        }

        return Inertia::render('FileRepository/Index', [
            'files' => $paginatedFiles,
            'colleges' => College::orderBy('name')->get(),
            'programs' => Program::orderBy('name')->get(),
            'areas' => Area::orderBy('order_no')->get(),
            'activeEvents' => $activeEventsQuery->get(),
            'currentType' => $type,
            'currentEvent' => $eventId ? AccreditationEvent::find($eventId) : null,
            'filters' => $request->only(['search', 'status', 'program_id', 'college_id', 'sort_field', 'sort_order'])
        ]);
    }

    public function activityLogs() {
        return Inertia::render('ActivityLogs/Index');
    }

    public function staff() {
        return Inertia::render('UserManagement/Staff');
    }

    // Replaced entirely by fileArchives?type=shared
}
