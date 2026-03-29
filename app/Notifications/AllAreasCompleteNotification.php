<?php

namespace App\Notifications;

use App\Models\AccreditationEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AllAreasCompleteNotification extends Notification
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
            'title' => 'All Areas Complete!',
            'message' => "Success! All accreditation areas for '{$this->event->title}' now have at least one document.",
            'type' => 'success',
            'icon' => 'check_circle',
            'event_id' => $this->event->id,
            'url' => route('file-archives', ['event_id' => $this->event->id])
        ];
    }
}
