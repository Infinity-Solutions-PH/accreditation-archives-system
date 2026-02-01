<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Program;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::withCount('files')
            ->orderBy('order_no')
            ->get()
            ->map(function($area) {
                $area->files_count = (int) $area->files_count;
                return $area;
            });

        return Inertia::render('Areas/Index', [
            'areas' => $areas
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
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        $files = $area->files()->with('college', 'program', 'uploadedBy.googleInfo')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Areas/Files', [
            'files' => $files,
            'area' => $area,
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
