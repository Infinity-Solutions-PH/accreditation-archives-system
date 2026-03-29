<?php

namespace App\Http\Controllers;

use App\Models\Accreditor;
use App\Models\AccreditationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EventAccreditorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AccreditationEvent $event)
    {
        return response()->json($event->accreditors()->with('college', 'program')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AccreditationEvent $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:accreditors,email',
        ]);

        $accreditor = Accreditor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(24)), // Random password, primarily using Google Auth
            'accreditation_event_id' => $event->id,
            'college_id' => $event->college_id,
            'program_id' => $event->program_id,
            'level' => $event->level,
            'expires_at' => $event->expires_at,
            'created_by' => auth()->id(),
        ]);

        activity()
            ->useLog('events')
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log("Added accreditor: {$accreditor->name} ({$accreditor->email}) to event: {$event->title}");

        return response()->json([
            'message' => 'Accreditor added successfully.',
            'accreditor' => $accreditor->load('college', 'program')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Accreditor $accreditor)
    {
        $eventName = $accreditor->accreditationEvent->title;
        $accreditorName = $accreditor->name;

        $accreditor->delete();

        activity()
            ->useLog('events')
            ->performedOn($accreditor)
            ->causedBy(auth()->user())
            ->log("Removed accreditor: {$accreditorName} from event: {$eventName}");

        return response()->json(['message' => 'Accreditor access removed successfully.']);
    }
}
