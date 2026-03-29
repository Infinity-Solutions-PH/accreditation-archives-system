<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\User;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Notification;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Models\AccreditationEvent;
use App\Notifications\EventCreatedNotification;
use App\Notifications\AllAreasCompleteNotification;

class AccreditationEventController extends Controller
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
        $user = auth()->user();
        $query = AccreditationEvent::with(['college', 'program', 'creator']);

        if ($user->hasRole('college_officer')) {
            $query->where('college_id', $user->college_id);
        } elseif ($user->hasRole('taskforce')) {
             $query->where('college_id', $user->college_id)
                   ->where('program_id', $user->program_id);
        }

        return Inertia::render('Events/Index', [
            'events' => $query->latest()->paginate(10),
            'colleges' => College::all(),
            'programs' => Program::all(),
            'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames() : [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'ido_staff'])) {
            return back()->withErrors(['message' => 'Unauthorized action.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'college_id' => 'required|exists:colleges,id',
            'program_id' => 'required|exists:programs,id',
            'level' => 'nullable|string',
            'expires_at' => 'required|date|after:today',
        ]);

        $event = AccreditationEvent::create([
            ...$request->all(),
            'created_by' => auth()->id(),
            'status' => 'active'
        ]);

        activity()
            ->useLog('events')
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log("Created accreditation event: {$event->title}");

        // Notify involved users
        $recipients = User::role(['college_officer', 'taskforce'])
            ->where('college_id', $event->college_id)
            ->where(function($q) use ($event) {
                $q->whereHas('roles', fn($r) => $r->where('name', 'college_officer'))
                  ->orWhere(function($sub) use ($event) {
                      $sub->whereHas('roles', fn($r) => $r->where('name', 'taskforce'))
                          ->where('program_id', $event->program_id);
                  });
            })
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new EventCreatedNotification($event));
        }

        return back()->with('message', 'Accreditation event created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccreditationEvent $accreditationEvent)
    {
        if (!auth()->user()->hasRole(['admin', 'ido_staff'])) {
            return back()->withErrors(['message' => 'Unauthorized action.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
            'expires_at' => 'required|date',
        ]);

        $accreditationEvent->update($request->all());

        activity()
            ->useLog('events')
            ->performedOn($accreditationEvent)
            ->causedBy(auth()->user())
            ->log("Updated accreditation event: {$accreditationEvent->title}");

        return back()->with('message', 'Accreditation event updated successfully.');
    }

    /**
     * Share a file to the accreditation event virtual drive.
     */
    public function shareToFile(Request $request)
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
            'accreditation_event_id' => 'required|exists:accreditation_events,id',
            'area_id' => 'required|exists:areas,id',
        ]);

        $event = AccreditationEvent::findOrFail($request->accreditation_event_id);
        
        // Check if file is already shared to this event
        if ($event->files()->where('file_id', $request->file_id)->exists()) {
            return response()->json(['message' => 'File already shared to this event.'], 422);
        }

        $event->files()->attach($request->file_id, [
            'area_id' => $request->area_id,
            'shared_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        activity()
            ->useLog('events')
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log("Shared file (ID: {$request->file_id}) to event virtual drive: {$event->title}");

        // Check if all areas are now complete
        $totalAreas = Area::count();
        $filledAreasCount = $event->files()
            ->distinct('accreditation_event_files.area_id')
            ->count('accreditation_event_files.area_id');

        if ($filledAreasCount >= $totalAreas) {
            // Only notify if we haven't sent a completion notification for this event yet
            $alreadyNotified = Notification::where('notifiable_id', auth()->id())
                ->where('data->event_id', $event->id)
                ->where('data->type', 'success')
                ->exists();

            if (!$alreadyNotified) {
                // Notify all involved users
                $recipients = User::role(['ido_staff', 'college_officer', 'taskforce'])
                ->where(function($q) use ($event) {
                    $q->whereHas('roles', fn($r) => $r->where('name', 'ido_staff'))
                      ->orWhere(function($sub) use ($event) {
                          $sub->where('college_id', $event->college_id)
                              ->where(function($inner) use ($event) {
                                  $inner->whereHas('roles', fn($r) => $r->where('name', 'college_officer'))
                                        ->orWhere(function($tf) use ($event) {
                                            $tf->whereHas('roles', fn($r) => $r->where('name', 'taskforce'))
                                               ->where('program_id', $event->program_id);
                                        });
                              });
                      });
                })
                ->get();

                foreach ($recipients as $recipient) {
                    $recipient->notify(new AllAreasCompleteNotification($event));
                }
            }
        }

        return response()->json(['message' => 'File shared to virtual drive successfully.']);
    }
}
