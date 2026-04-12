<?php

namespace App\Notifications;

use App\Models\AccreditationEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccreditationEventUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public AccreditationEvent $event,
        public string $causer
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Accreditation Event Updated',
            'message' => "{$this->causer} updated the details of '{$this->event->title}'.",
            'type' => 'info',
            'icon' => 'event_note',
            'event_id' => $this->event->id,
            'url' => route('events.index')
        ];
    }
}
