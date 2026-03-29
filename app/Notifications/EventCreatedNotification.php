<?php

namespace App\Notifications;

use App\Models\AccreditationEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public AccreditationEvent $event)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Accreditation Event',
            'message' => "An event '{$this->event->title}' has been created for your program.",
            'type' => 'event',
            'icon' => 'event',
            'event_id' => $this->event->id,
            'url' => route('file-archives', ['event_id' => $this->event->id])
        ];
    }
}
