<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\AccreditationEvent;
use Illuminate\Support\Facades\Log;
use App\Notifications\CommentReplyNotification;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, AccreditationEvent $event, Area $area)
    {
        $comments = Comment::where('accreditation_event_id', $event->id)
            ->where('area_id', $area->id)
            ->whereNull('parent_id')
            ->with(['commentable', 'replies.commentable'])
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, AccreditationEvent $event, Area $area)
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

        $comment = Comment::create([
            'commentable_id' => $user->id,
            'commentable_type' => get_class($user),
            'accreditation_event_id' => $event->id,
            'area_id' => $area->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        if ($request->parent_id) {
            $parentComment = Comment::with(['commentable', 'replies.commentable'])->find($request->parent_id);

            if ($parentComment) {
                $participants = collect();

                if ($parentComment->commentable) {
                    $participants->push($parentComment->commentable);
                }

                foreach ($parentComment->replies as $reply) {
                    if ($reply->commentable) {
                        $participants->push($reply->commentable);
                    }
                }

                $recipients = $participants
                    ->filter(fn ($participant) => !(
                        $participant->id === $user->id && get_class($participant) === get_class($user)
                    ))
                    ->unique(fn ($participant) => get_class($participant) . ':' . $participant->id);

                foreach ($recipients as $recipient) {
                    try {
                        $recipient->notify(new CommentReplyNotification($comment, $event, $area, $user->name));
                    } catch (\Throwable $e) {
                        Log::warning('Failed to send comment reply notification: ' . $e->getMessage());
                    }
                }
            }
        }

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
