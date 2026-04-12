<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\FileAccessRequest;
use App\Services\ChunkedUploadService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UploadChunkRequest;
use App\Http\Requests\CreateTempFileRequest;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FilePermissionApprovedNotification;
use App\Notifications\FilePermissionRequestedNotification;

class FileController extends Controller
{
    public function __construct(
        protected ChunkedUploadService $service
    ) {
    }

    private function checkActive() {
        /** @var \App\Models\User|null $user */
        $user = auth('web')->user();
        if ($user && $user->is_active === false) {
            abort(403, 'Inactive users cannot upload or modify files.');
        }
    }

    // 1️⃣ Create temporary metadata
    public function temp(CreateTempFileRequest $request)
    {
        $this->checkActive();
        $file = $this->service->createTemporaryMetadata($request->validated());

        return response()->json(['tmp_id' => $file->tmp_id]);
    }

    // 2️⃣ Upload chunk
    public function uploadChunk(UploadChunkRequest $request)
    {
        $this->checkActive();
        $file = File::where('tmp_id', $request->tmp_id)->firstOrFail();
        $this->service->saveChunk($file, $request->file('chunk'), $request->index);

        return response()->json(['status' => 'ok']);
    }

    // 3️⃣ Complete upload
    public function complete(Request $request)
    {
        $this->checkActive();
        $request->validate(['tmp_id' => 'required|string|exists:files,tmp_id']);

        $file = File::where('tmp_id', $request->tmp_id)->firstOrFail();
        $this->service->mergeChunks($file);

        return response()->json(['status' => 'completed']);
    }

    // 4️⃣ Update metadata
    public function updateMetadata(Request $request)
    {
        $this->checkActive();
        $request->validate([
            'tmp_id' => 'required|string|exists:files,tmp_id',
            'metadata.title' => 'nullable|string|max:255',
            'metadata.description' => 'nullable|string|max:1000',
            'metadata.college_id' => ['nullable', 'exists:colleges,id'],
            'metadata.program_id' => ['nullable', 'exists:programs,id'],
            'metadata.area_id' => ['nullable', 'exists:areas,id'],
            'metadata.level' => ['nullable', 'int'],
            'metadata.is_general' => ['nullable', 'boolean']
        ]);

        $file = File::where('tmp_id', $request->tmp_id)->firstOrFail();
        
        // Authorization check for existing files (replacement/update)
        if ($file->path) {
            $this->authorizeEdit($file);
        }

        $this->service->updateMetadata($file, $request->metadata);

        return response()->json(['status' => 'ok']);
    }

    // 5️⃣ Abort upload
    public function abort(Request $request)
    {
        $this->checkActive();
        $request->validate(['tmp_id' => 'required|string|exists:files,tmp_id']);

        $file = File::where('tmp_id', $request->tmp_id)->first();
        if ($file) {
            $this->service->abort($file);
        }

        return response()->json(['status' => 'aborted']);
    }

    /**
     * Inline file viewer support
     */
    public function view(File $file)
    {
        // Enforce the same accessibility scope
        $isAccessible = File::accessibleBy(auth()->user())->where('id', $file->id)->exists();
        abort_unless($isAccessible, 403, 'You do not have permission to view this file.');

        $cleanPath = str_replace('private/', '', $file->path);
        
        if (!Storage::disk('local')->exists($cleanPath)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk('local')->response($cleanPath, $file->original_filename, [
            'Content-Disposition' => 'inline; filename="' . $file->original_filename . '"',
            'Content-Type' => Storage::disk('local')->mimeType($cleanPath)
        ]);
    }

    /**
     * Standard force-download support
     */
    public function download(File $file)
    {
        $isAccessible = File::accessibleBy(auth()->user())->where('id', $file->id)->exists();
        abort_unless($isAccessible, 403, 'You do not have permission to download this file.');

        $cleanPath = str_replace('private/', '', $file->path);

        if (!Storage::disk('local')->exists($cleanPath)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk('local')->download($cleanPath, $file->original_filename);
    }

    /**
     * Shared view via permanent link (UUID)
     */
    public function sharedView($uuid)
    {
        $file = File::where('uuid', $uuid)->with(['uploadedBy', 'college', 'program'])->firstOrFail();
        $user = auth()->user();

        // Check if user has access via existing scope logic
        $isAccessible = File::accessibleBy($user)->where('id', $file->id)->exists();
        
        // Also check if they have a pending request
        $pendingRequest = null;
        if ($user) {
            $pendingRequest = $file->accessRequests()
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();
        }

        // Organization Boundary Enforcement
        $orgRestricted = false;
        if ($user && !$user->hasRole(['admin', 'ido_staff'])) {
            // If file has a college/program context, user must match it
            if ($file->college_id && $user->college_id != $file->college_id) {
                $orgRestricted = true;
            }
            if ($file->program_id && $user->program_id != $file->program_id) {
                $orgRestricted = true;
            }
        }

        return Inertia::render('Files/SharedView', [
            'file' => $file,
            'isAccessible' => $isAccessible,
            'isOrgRestricted' => $orgRestricted,
            'hasPendingRequest' => !!$pendingRequest,
            'viewUrl' => $isAccessible ? route('files.view', $file->id) : null
        ]);
    }

    /**
     * Request access to a restricted file
     */
    public function requestAccess(File $file)
    {
        $user = auth()->user();
        if (!$user) abort(401);

        // Check if already has access
        if (File::accessibleBy($user)->where('id', $file->id)->exists()) {
            return back()->with('message', 'You already have access to this file.');
        }

        // Create or update request
        $accessRequest = FileAccessRequest::updateOrCreate(
            ['file_id' => $file->id, 'user_id' => $user->id],
            ['status' => 'pending', 'reason' => request('reason')]
        );

        // Notify Uploader and Coordinators
        $uploader = $file->uploadedBy;
        $recipients = collect([$uploader]);

        // Find college coordinators/officers
        if ($file->college_id) {
            $coordinators = User::role(['college_officer', 'coordinator'])
                ->where('college_id', $file->college_id)
                ->get();
            $recipients = $recipients->concat($coordinators);
        }

        Notification::send($recipients->filter()->unique('id'), new FilePermissionRequestedNotification($file, $user));

        return back()->with('message', 'Access request submitted successfully.');
    }

    /**
     * Get users with access and pending requests
     */
    public function getShares(File $file)
    {
        $this->authorizeAccess($file);

        return response()->json([
            'shared_users' => $file->sharedWithUsers()->with('googleInfo')->get(),
            'pending_requests' => $file->accessRequests()->where('status', 'pending')->with('user.googleInfo')->get(),
        ]);
    }

    /**
     * Approve a file access request
     */
    public function approveAccess(FileAccessRequest $accessRequest)
    {
        $file = $accessRequest->file;
        $this->authorizeAccess($file);

        $accessRequest->update([
            'status' => 'approved',
            'handled_by' => auth()->id(),
            'handled_at' => now()
        ]);

        // Add to file_user_shares for persistent access
        $file->sharedWithUsers()->syncWithoutDetaching([$accessRequest->user_id => ['shared_by' => auth()->id()]]);

        // Notify User
        $accessRequest->user->notify(new FilePermissionApprovedNotification($file, auth()->user()->name));

        return back()->with('message', 'Access request approved.');
    }

    /**
     * Replace file content while keeping metadata
     */
    public function updateFile(Request $request, File $file)
    {
        $this->authorizeEdit($file);

        $request->validate([
            'tmp_id' => 'required|string|exists:files,tmp_id',
            'metadata.title' => 'nullable|string|max:255',
            'metadata.description' => 'nullable|string|max:1000',
        ]);

        // Delete old file if new chunks were merged
        $oldPath = str_replace('private/', '', $file->path);
        if ($request->tmp_id !== $file->tmp_id) {
             if (Storage::disk('local')->exists($oldPath)) {
                Storage::disk('local')->delete($oldPath);
            }
        }

        // Update with new physical file (the merge service should have already updated paths if complete was called)
        // Here we just ensure metadata is updated
        $this->service->updateMetadata($file, $request->metadata);

        return response()->json(['status' => 'ok', 'file' => $file->fresh()]);
    }

    /**
     * Search events for sharing (Role-aware)
     */
    public function searchEvents(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\AccreditationEvent::with(['college', 'program']);

        if ($user->hasRole('admin') || $user->hasRole('ido_staff')) {
            // Full access
        } elseif ($user->hasRole('coordinator') || $user->hasRole('college_officer')) {
            $query->where('college_id', $user->college_id);
        } else {
            // Taskforce
            $query->where('program_id', $user->program_id);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->latest()->take(10)->get()->map(function($event) use ($user) {
            $isArchived = $event->status === 'archived' || ($event->expires_at && $event->expires_at->isPast());
            
            // Stakeholders calculation
            $accreditors = $event->accreditors()->with('googleInfo')->get();
            
            // To be truly comprehensive, we'd add coordinators and taskforce members here
            // For now, let's just return the counts and avatars
            return [
                'id' => $event->id,
                'title' => $event->title,
                'program_code' => $event->program?->code,
                'level' => $event->level,
                'status' => $event->status,
                'is_selectable' => !($user->hasRole('taskforce') && $isArchived),
                'stakeholder_count' => $accreditors->count(), // simplified for now
                'stakeholders' => $accreditors->take(5)->map(fn($acc) => [
                    'name' => $acc->name,
                    'avatar' => $acc->googleInfo?->avatar
                ])
            ];
        });

        return response()->json($events);
    }

    /**
     * Search users for direct sharing (Role-aware)
     */
    public function searchUsers(Request $request)
    {
        $user = auth()->user();
        $query = User::with('googleInfo');

        if ($user->hasRole('admin') || $user->hasRole('ido_staff')) {
            // Full access
        } else {
            // Restricted to college
            $query->where('college_id', $user->college_id);
            if ($user->hasRole('taskforce')) {
                $query->where('program_id', $user->program_id);
            }
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->take(10)->get();

        return response()->json($users);
    }

    private function authorizeEdit(File $file)
    {
        $user = auth()->user();
        $isOwner = $file->uploaded_by === $user->id;
        $isAdmin = $user->hasRole('admin');
        
        if (!$isOwner && !$isAdmin) abort(403, 'Only the uploader or administrators can modify this file.');
    }

    private function authorizeAccess(File $file)
    {
        $user = auth()->user();
        $isOwner = $file->uploaded_by === $user->id;
        $isHigher = $user->hasRole(['admin', 'ido_staff', 'college_officer', 'coordinator']);
        
        if (!$isOwner && !$isHigher) abort(403);
    }
}
