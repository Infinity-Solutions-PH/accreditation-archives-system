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
            'name' => 'required_without:accreditor_id|string|max:255',
            'email' => 'required_without:accreditor_id|email',
            'accreditor_id' => 'nullable|exists:accreditors,id'
        ]);

        if ($request->accreditor_id) {
            $accreditor = Accreditor::findOrFail($request->accreditor_id);
        } else {
            // Find or create based on email
            $accreditor = Accreditor::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'password' => Hash::make(Str::random(24)),
                    'role_status' => 'approved',
                    'is_active' => true,
                    'expires_at' => $event->expires_at,
                    'created_by' => auth()->id(),
                ]
            );
        }

        // Attach to event if not already attached
        if (!$accreditor->events()->where('accreditation_event_id', $event->id)->exists()) {
            $accreditor->events()->attach($event->id);
        }

        // Expiration Logic: apply event expiration if account is expired or event expires later than current account
        $currentExpiry = $accreditor->expires_at;
        $eventExpiry = $event->expires_at;

        if (!$currentExpiry || $currentExpiry < now() || $eventExpiry > $currentExpiry) {
            $accreditor->update(['expires_at' => $eventExpiry]);
        }

        activity()
            ->useLog('events')
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log("Added accreditor: {$accreditor->name} to event: {$event->title}");

        return response()->json([
            'message' => 'Accreditor added successfully.',
            'accreditor' => $accreditor->load('events')
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
