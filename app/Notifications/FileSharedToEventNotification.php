<?php

namespace App\Notifications;

use App\Models\File;
use App\Models\Area;
use App\Models\AccreditationEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FileSharedToEventNotification extends Notification
{
    use Queueable;

    public function __construct(
        public AccreditationEvent $event,
        public File $file,
        public Area $area,
        public string $causer
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'File Shared to Event',
            'message' => "{$this->causer} shared '{$this->file->title}' to {$this->area->code} in '{$this->event->title}'.",
            'type' => 'info',
            'icon' => 'share',
            'event_id' => $this->event->id,
            'url' => route('areas.slug', ['event' => $this->event->slug, 'area' => $this->area->slug])
        ];
    }
}
