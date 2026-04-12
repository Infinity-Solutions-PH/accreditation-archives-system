<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Accreditor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\AccreditorInviteNotification;

class AccreditorAccountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:accreditors',
            'password' => 'required|string|min:8',
            'expires_at' => 'required|date',
            'selected_events' => 'nullable|array',
            'selected_events.*' => 'exists:accreditation_events,id'
        ]);

        $accreditor = Accreditor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'expires_at' => $request->expires_at,
            'role_status' => 'approved',
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        if ($request->has('selected_events')) {
            $accreditor->events()->attach($request->selected_events);
        }

        // Send invite notification
        if ($request->has('send_email') && $request->send_email) {
            $accreditor->notify(new AccreditorInviteNotification($request->password));
        }

        return back()->with('message', 'Accreditor created successfully.');
    }

    public function update(Request $request, Accreditor $accreditor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:accreditors,email,'.$accreditor->id,
            'password' => 'nullable|string|min:8',
            'expires_at' => 'required|date',
            'selected_events' => 'nullable|array',
            'selected_events.*' => 'exists:accreditation_events,id',
            'role_status' => 'required|string|in:pending,approved,rejected',
            'is_active' => 'required|boolean'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'expires_at' => $request->expires_at,
            'role_status' => $request->role_status,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $accreditor->update($data);

        if ($request->has('selected_events')) {
            $accreditor->events()->sync($request->selected_events);
        } else {
            $accreditor->events()->detach();
        }

        return back()->with('message', 'Accreditor updated successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $exclude_event_id = $request->get('exclude_event_id');

        $accreditors = Accreditor::when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($exclude_event_id, function ($q) use ($exclude_event_id) {
                $q->whereDoesntHave('events', function ($e) use ($exclude_event_id) {
                    $e->where('accreditation_events.id', $exclude_event_id);
                });
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($accreditors);
    }

    public function requestExtension(Request $request)
    {
        $accreditor = auth('accreditor')->user();
        
        // Notify admins/staff
        $admins = User::role(['admin', 'ido_staff'])->get();
        
        // Use a simple notification or email
        foreach ($admins as $admin) {
            // Logic to notify admin about the extension request
            // For now, we'll just log it or you can create a specific notification
            activity()
                ->useLog('accreditors')
                ->performedOn($accreditor)
                ->causedBy($accreditor)
                ->log("Requested an access extension.");
        }

        return response()->json(['message' => 'Extension request sent successfully.']);
    }
}
