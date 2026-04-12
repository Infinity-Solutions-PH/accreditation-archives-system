<?php

namespace App\Notifications;

use App\Models\File;
use App\Models\Area;
use App\Models\AccreditationEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FileRemovedFromEventNotification extends Notification
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
            'title' => 'File Removed from Event',
            'message' => "{$this->causer} removed '{$this->file->title}' from {$this->area->code} in '{$this->event->title}'.",
            'type' => 'warning',
            'icon' => 'delete_sweep',
            'event_id' => $this->event->id,
            'url' => route('areas.slug', ['event' => $this->event->slug, 'area' => $this->area->slug])
        ];
    }
}
