<?php

namespace App\Http\Controllers;

use App\Models\Accreditor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AccreditorAccountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:accreditors',
            'expires_at' => 'required|date',
            'college_id' => 'required|exists:colleges,id',
            'program_id' => 'required|exists:programs,id',
            'level' => 'nullable|string',
            'accreditation_event_id' => 'nullable|exists:accreditation_events,id',
        ]);

        $password = Str::random(10);

        $accreditorData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'expires_at' => $request->expires_at,
            'college_id' => $request->college_id,
            'program_id' => $request->program_id,
            'level' => $request->level,
            'created_by' => auth()->id(),
            'accreditation_event_id' => $request->accreditation_event_id,
        ];

        $accreditor = Accreditor::create($accreditorData);

        // Send invite notification with credentials
        $event = $request->accreditation_event_id 
            ? \App\Models\AccreditationEvent::find($request->accreditation_event_id) 
            : null;
            
        $accreditor->notify(new \App\Notifications\AccreditorInviteNotification($password, $event));

        return response()->json([
            'message' => 'Accreditor created successfully and invitation sent.',
            'password' => $password, // Return mostly for dev/admin visibility
            'accreditor' => $accreditor
        ], 201);
    }
}
