<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\User;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Program;
use App\Models\Accreditor;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\AccreditationEvent;
use App\Notifications\EventCreatedNotification;
use App\Notifications\FileSharedToEventNotification;
use App\Notifications\AllAreasCompleteNotification;
use App\Notifications\FileRemovedFromEventNotification;

class AccreditationEventController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth('web')->user() ?? auth('accreditor')->user();

        if ($user instanceof Accreditor) {
            return redirect()->route('accreditor.dashboard');
        }

        $tab = $request->query('tab', 'active');
        $query = AccreditationEvent::with(['college', 'program', 'creator']);

        if ($user && method_exists($user, 'hasRole')) {
            if ($user->hasRole('college_officer')) {
                $query->where('college_id', $user->college_id);
            } elseif ($user->hasRole('taskforce')) {
                 $query->where('college_id', $user->college_id)
                       ->where('program_id', $user->program_id);
            }
        }

        if ($tab === 'active') {
            $query->where('status', 'active');
        } else {
            $query->where('status', '!=', 'active');
        }

        return Inertia::render('Events/Index', [
            'events' => $query->latest()->paginate(10),
            'colleges' => College::all(),
            'programs' => Program::all(),
            'activeTab' => $tab,
            'roles' => $user && method_exists($user, 'getRoleNames') ? $user->getRoleNames() : [],
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
            'status' => 'required|in:draft,active,archived,completed,cancelled',
            'expires_at' => 'required|date',
        ]);

        $accreditationEvent->update($request->all());

        activity()
            ->useLog('events')
            ->performedOn($accreditationEvent)
            ->causedBy(auth()->user())
            ->log("Updated accreditation event: {$accreditationEvent->title}");

        // Notify Stakeholders
        $user = auth()->user();
        $recipients = User::role(['admin', 'ido_staff', 'college_officer', 'taskforce'])
            ->where(function($q) use ($accreditationEvent, $user) {
                $q->where('id', $accreditationEvent->created_by)
                  ->orWhere(function($sub) use ($accreditationEvent) {
                      $sub->where('college_id', $accreditationEvent->college_id)
                          ->whereHas('roles', fn($r) => $r->whereIn('name', ['college_officer', 'taskforce']));
                  });
            })
            ->where('id', '!=', $user->id)
            ->distinct()
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new \App\Notifications\AccreditationEventUpdatedNotification($accreditationEvent, $user->name));
        }

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

        // Notify Stakeholders about the shared file
        $file = \App\Models\File::find($request->file_id);
        $area = Area::find($request->area_id);
        $user = auth()->user();

        $recipients = User::role(['admin', 'ido_staff', 'college_officer', 'taskforce'])
            ->where(function($q) use ($event, $user) {
                $q->where('id', $event->created_by)
                  ->orWhere(function($sub) use ($event) {
                      $sub->where('college_id', $event->college_id)
                          ->whereHas('roles', fn($r) => $r->whereIn('name', ['college_officer', 'taskforce']));
                  });
            })
            ->where('id', '!=', $user->id)
            ->distinct()
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new FileSharedToEventNotification($event, $file, $area, $user->name ?? 'A user'));
        }

        return response()->json(['message' => 'File shared to virtual drive successfully.']);
    }

    /**
     * Remove a file from the accreditation event virtual drive.
     */
    public function unshareFile(Request $request, AccreditationEvent $event, Area $area)
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);

        $file = \App\Models\File::findOrFail($request->file_id);
        $user = auth()->user() ?? auth('accreditor')->user();

        // Permissions: Only uploader or IDO Staff/Admin/College Officer
        $isUploader = $file->uploaded_by === $user->id;
        $hasHigherRole = $user instanceof \App\Models\User && $user->hasRole(['admin', 'ido_staff', 'college_officer']);

        if (!$isUploader && !$hasHigherRole) {
            return response()->json(['message' => 'Unauthorized to remove this file from the event.'], 403);
        }

        $event->files()->wherePivot('area_id', $area->id)->detach($file->id);

        activity()
            ->useLog('events')
            ->performedOn($event)
            ->causedBy($user)
            ->log("Removed file '{$file->title}' from {$area->code} in event: {$event->title}");

        // Notify Stakeholders
        $recipients = User::role(['admin', 'ido_staff', 'college_officer', 'taskforce'])
            ->where(function($q) use ($event, $user) {
                $q->where('id', $event->created_by)
                  ->orWhere(function($sub) use ($event) {
                      $sub->where('college_id', $event->college_id)
                          ->whereHas('roles', fn($r) => $r->whereIn('name', ['college_officer', 'taskforce']));
                  });
            })
            ->where('id', '!=', $user->id)
            ->distinct()
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new FileRemovedFromEventNotification($event, $file, $area, $user->name));
        }

        return response()->json(['message' => 'File removed from event successfully.']);
    }

    /**
     * Share a file to the accreditation event virtual drive.
     */
    public function accreditorDashboard(Request $request)
    {
        $user = auth('accreditor')->user();
        if (!$user) return redirect()->route('accreditor.auth');

        $tab = $request->query('tab', 'active');
        $query = $user->events()->with(['college', 'program']);

        if ($tab === 'active') {
            $query->where('accreditation_events.expires_at', '>', now());
        } else {
            $query->where('accreditation_events.expires_at', '<=', now());
        }

        return Inertia::render('Accreditor/Dashboard', [
            'events' => $query->latest()->paginate(12),
            'activeTab' => $tab,
            'accreditor' => $user
        ]);
    }
}
