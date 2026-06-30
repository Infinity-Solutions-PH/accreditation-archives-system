<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\User;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Program;
use App\Models\Accreditor;
use Illuminate\Http\Request;
use App\Models\AccreditationEvent;

class AreaController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    private function authorizeEventAccess(AccreditationEvent $event)
    {
        $user = auth('web')->user() ?? auth('accreditor')->user();

        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        if ($user instanceof Accreditor) {
            if (!$user->events()->where('accreditation_events.id', $event->id)->exists()) {
                abort(403, 'Unauthorized.');
            }
        } elseif ($user instanceof User) {
            if ($user->hasRole('college_officer')) {
                if ($event->college_id !== $user->college_id) {
                    abort(403, 'Unauthorized.');
                }
            } elseif ($user->hasRole('taskforce')) {
                if ($event->college_id !== $user->college_id || $event->program_id !== $user->program_id) {
                    abort(403, 'Unauthorized.');
                }
            }
        } else {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Display a listing of areas for a specific event.
     */
    public function index(AccreditationEvent $event)
    {
        $this->authorizeEventAccess($event);

        $areas = Area::orderBy('order_no')
            ->get()
            ->map(function($area) use ($event) {
                // Count only files shared for THIS specific event in this area
                $area->files_count = (int) $event->files()
                    ->wherePivot('area_id', $area->id)
                    ->count();
                return $area;
            });

        return Inertia::render('Areas/Index', [
            'areas' => $areas,
            'event' => $event->load(['college', 'program'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display files within a specific area for a specific event.
     */
    public function show(Request $request, AccreditationEvent $event, Area $area)
    {
        $this->authorizeEventAccess($event);

        $user = auth()->user() ?? auth('accreditor')->user();
        $search = $request->query('search');
        $sortField = $request->query('sort_field', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');
        $subfolder = $request->query('subfolder');
        $parameter = $request->query('parameter');
        $parameterFolder = $request->query('parameter_folder');

        if (!in_array($sortField, ['title', 'created_at', 'college', 'program'])) {
            $sortField = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        // Fetch file counts for foldering view (calculating based on accessible files)
        $allSharedFiles = $event->files()
            ->wherePivot('area_id', $area->id)
            ->accessibleBy($user)
            ->get(['files.id']);

        $fileCounts = [];
        foreach ($allSharedFiles as $f) {
            $sub = $f->pivot->subfolder ?? '';
            $param = $f->pivot->parameter ?? '';
            $key = $sub . '||' . $param;
            if (!isset($fileCounts[$key])) {
                $fileCounts[$key] = [
                    'subfolder' => $f->pivot->subfolder,
                    'parameter' => $f->pivot->parameter,
                    'parameter_folder' => $f->pivot->parameter_folder,
                    'count' => 0
                ];
            }
            $fileCounts[$key]['count']++;
        }
        $fileCounts = array_values($fileCounts);

        // Fetch only files explicitly shared for THIS event and THIS area
        $filesQuery = $event->files()
            ->wherePivot('area_id', $area->id)
            ->accessibleBy($user)
            ->when($subfolder, function($query) use ($subfolder) {
                return $query->where('accreditation_event_files.subfolder', $subfolder);
            })
            ->when($parameter, function($query) use ($parameter) {
                return $query->where('accreditation_event_files.parameter', $parameter);
            })
            ->when($parameterFolder, function($query) use ($parameterFolder) {
                return $query->where('accreditation_event_files.parameter_folder', $parameterFolder);
            })
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('files.title', 'like', "%{$search}%")
                      ->orWhere('files.description', 'like', "%{$search}%")
                      ->orWhereHas('uploadedBy', function($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%");
                      });
                });
            });

        // If not in a subfolder and not searching, return no files directly
        if (empty($subfolder) && empty($search)) {
            $filesQuery->whereRaw('1 = 0');
        }

        if ($sortField === 'college') {
            $filesQuery->leftJoin('colleges', 'files.college_id', '=', 'colleges.id')
                       ->select('files.*')
                       ->orderBy('colleges.code', $sortOrder);
        } elseif ($sortField === 'program') {
            $filesQuery->leftJoin('programs', 'files.program_id', '=', 'programs.id')
                       ->select('files.*')
                       ->orderBy('programs.code', $sortOrder);
        } elseif ($sortField === 'title') {
            $filesQuery->orderBy('files.title', $sortOrder);
        } else {
            $filesQuery->orderBy('accreditation_event_files.created_at', $sortOrder);
        }

        $files = $filesQuery->with(['college', 'program', 'uploadedBy.googleInfo'])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Areas/Files', [
            'files' => $files,
            'area' => $area,
            'event' => $event->load(['college', 'program']),
            'fileCounts' => $fileCounts,
            'filters' => $request->only(['search', 'sort_field', 'sort_order', 'subfolder', 'parameter', 'parameter_folder']),
            'colleges' => College::orderBy('name')->get(),
            'programs' => Program::orderBy('name')->get(),
            'areas' => Area::orderBy('order_no')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get all areas for API dropdowns.
     */
    public function all()
    {
        return response()->json(Area::orderBy('order_no')->get());
    }
}
