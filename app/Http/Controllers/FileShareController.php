<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;

class FileShareController extends Controller
{
    /**
     * Search for users eligible to receive shared files.
     */
    public function searchShareableUsers(Request $request)
    {
        $query = $request->get('query', '');

        $users = User::with('roles', 'college', 'googleInfo')
            ->where('is_active', true)
            ->where('role_status', 'approved')
            ->where('id', '!=', auth()->id()) // don't show self
            ->whereDoesntHave('roles', function ($q) {
                // EXCLUDE admins
                $q->whereIn('name', ['admin']);
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->limit(10)
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'avatar' => $u->googleInfo ? $u->googleInfo->avatar : null,
                    'college' => $u->college ? $u->college->code : 'IDO',
                    'role' => $u->roles->first() ? ucwords(str_replace('_', ' ', $u->roles->first()->name)) : 'User'
                ];
            });

        return response()->json($users);
    }

    /**
     * Share a specific file with a peer user.
     */
    public function shareToUser(Request $request)
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $file = File::findOrFail($request->file_id);
        $user = User::findOrFail($request->user_id);

        if ($file->sharedWithUsers()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'File already shared with this user.'], 422);
        }

        $file->sharedWithUsers()->attach($user->id, [
            'shared_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        activity()
            ->useLog('files')
            ->performedOn($file)
            ->causedBy(auth()->user())
            ->log("Shared file (ID: {$file->id}) with User: {$user->name}");

        return response()->json(['message' => 'File shared successfully.']);
    }
}
