<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Models\AccreditationEvent;

class AreaController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    /**
     * Display a listing of areas for a specific event.
     */
    public function index(AccreditationEvent $event)
    {
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
        $user = auth()->user() ?? auth('accreditor')->user();
        $search = $request->query('search');

        // Fetch only files explicitly shared for THIS event and THIS area
        $files = $event->files()
            ->wherePivot('area_id', $area->id)
            ->accessibleBy($user)
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhereHas('uploadedBy', function($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->with(['college', 'program', 'uploadedBy.googleInfo'])
            ->orderBy('accreditation_event_files.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Areas/Files', [
            'files' => $files,
            'area' => $area,
            'event' => $event->load(['college', 'program']),
            'filters' => $request->only(['search']),
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
}
