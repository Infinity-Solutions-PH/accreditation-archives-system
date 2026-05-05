<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AccreditationEvent;
use App\Http\Resources\UserResource;

class SearchController extends Controller
{
    /**
     * Perform a role-based global search across files, events, and users.
     */
    public function globalSearch(Request $request)
    {
        $q = $request->query('q');
        
        if (!$q || strlen($q) < 2) {
            return response()->json([
                'files' => [],
                'events' => [],
                'users' => []
            ]);
        }

        /** @var User $user */
        $user = auth()->user();

        // 1. Files: Leverages existing accessibility logic
        $files = File::accessibleBy($user)
            ->where(function($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('original_filename', 'like', "%$q%");
            })
            ->limit(5)
            ->get(['id', 'title', 'is_general', 'extension', 'area_id']);

        // 2. Events: Filtered by college/program scope
        $eventsQuery = AccreditationEvent::query();
        
        if ($user->hasRole('college_officer')) {
            $eventsQuery->where('college_id', $user->college_id);
        } elseif ($user->hasRole('taskforce')) {
            $eventsQuery->where('program_id', $user->program_id);
        } elseif ($user->hasRole('accreditor')) {
            $eventsQuery->where('id', $user->accreditation_event_id);
        }
        
        $events = $eventsQuery->where('title', 'like', "%$q%")
            ->limit(5)
            ->get(['id', 'title', 'slug', 'expires_at']);

        // 3. Users: Highly contextual visibility
        $users = [];
        if ($user->hasRole(['admin', 'ido_staff', 'college_officer'])) {
            $usersQuery = User::with(['googleInfo', 'roles', 'college']);
            
            // College officers only see their own college
            if ($user->hasRole('college_officer') && !$user->hasRole(['admin', 'ido_staff'])) {
                $usersQuery->where('college_id', $user->college_id);
            } 
            // IDO Staff see everyone except Admins
            elseif ($user->hasRole('ido_staff') && !$user->hasRole('admin')) {
                $usersQuery->whereDoesntHave('roles', fn($r) => $r->where('name', 'admin'));
            }
            // Admin sees everyone

            $usersRaw = $usersQuery->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            })
            ->limit(5)
            ->get();
            
            $users = UserResource::collection($usersRaw)->resolve();
        }

        return response()->json([
            'files' => $files,
            'events' => $events,
            'users' => $users
        ]);
    }
}
