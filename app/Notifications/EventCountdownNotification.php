<?php

namespace App\Notifications;

use App\Models\AccreditationEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventCountdownNotification extends Notification
{
    use Queueable;

    public function __construct(public AccreditationEvent $event, public int $days)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Event Countdown Reminder',
            'message' => "'{$this->event->title}' will expire in {$this->days} days. Please ensure all documents are ready.",
            'type' => 'reminder',
            'icon' => 'timer',
            'event_id' => $this->event->id,
            'url' => route('areas', ['event' => $this->event->slug])
        ];
    }
}
