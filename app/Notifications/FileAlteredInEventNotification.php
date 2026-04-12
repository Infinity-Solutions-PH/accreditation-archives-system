<?php

namespace App\Notifications;

use App\Models\File;
use App\Models\AccreditationEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FileAlteredInEventNotification extends Notification
{
    use Queueable;

    public function __construct(
        public AccreditationEvent $event,
        public File $file,
        public string $causer,
        public string $changeType = 'updated'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Shared File Altered',
            'message' => "{$this->causer} {$this->changeType} the metadata of '{$this->file->title}' in '{$this->event->title}'.",
            'type' => 'info',
            'icon' => 'edit_document',
            'event_id' => $this->event->id,
            'url' => route('events.index') // Or link to the specific event drive
        ];
    }
}
