<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserOnboardingNotification extends Notification
{
    use Queueable;

    public function __construct(public User $user)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New User Registration',
            'message' => "A new user, {$this->user->name}, has just completed college selection and is pending approval.",
            'type' => 'onboarding',
            'icon' => 'person_add',
            'user_id' => $this->user->id,
            'url' => route('user-management')
        ];
    }
}
