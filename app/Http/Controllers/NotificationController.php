<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        Inertia::setRootView('layouts/app');
    }

    private function currentUser()
    {
        return auth('web')->user() ?? auth('accreditor')->user();
    }

    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $user = $this->currentUser();
        $notifications = $user->notifications()->paginate(15);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Get the latest 5 notifications and unread count for the navbar.
     */
    public function recent()
    {
        $user = $this->currentUser();
        
        return response()->json([
            'recent' => $user->notifications()->take(5)->get(),
            'unreadCount' => $user->unreadNotifications()->count()
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $user = $this->currentUser();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = $this->currentUser();
        $user->unreadNotifications->markAsRead();

        return back()->with('message', 'All notifications marked as read.');
    }
}
