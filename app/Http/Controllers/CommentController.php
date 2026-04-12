<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, \App\Models\AccreditationEvent $event, \App\Models\Area $area)
    {
        $comments = \App\Models\Comment::where('accreditation_event_id', $event->id)
            ->where('area_id', $area->id)
            ->whereNull('parent_id')
            ->with(['commentable', 'replies.commentable'])
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, \App\Models\AccreditationEvent $event, \App\Models\Area $area)
    {
        // Check if event is archived/expired - comments disabled
        if ($event->expires_at && $event->expires_at < now()) {
            return response()->json(['message' => 'Commenting is disabled for archived events.'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $user = auth('web')->user() ?? auth('accreditor')->user();

        $comment = \App\Models\Comment::create([
            'commentable_id' => $user->id,
            'commentable_type' => get_class($user),
            'accreditation_event_id' => $event->id,
            'area_id' => $area->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        return response()->json($comment->load('commentable'));
    }

    public function destroy(\App\Models\Comment $comment)
    {
        $user = auth('web')->user() ?? auth('accreditor')->user();

        // Only author or admin can delete
        if ($comment->commentable_id !== $user->id || $comment->commentable_type !== get_class($user)) {
            if (!$user instanceof \App\Models\User || !$user->hasRole(['admin', 'ido_staff'])) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted.']);
    }
}
