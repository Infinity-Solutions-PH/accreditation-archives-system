<?php

namespace App\Notifications;

use App\Models\File;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FilePermissionRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public File $file,
        public User $requester
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'info',
            'icon' => 'key',
            'title' => 'Permission Requested',
            'message' => "{$this->requester->name} has requested permission to access '{$this->file->title}'.",
            'file_id' => $this->file->id,
            'url' => route('events.index') // Will link to sharing management in future
        ];
    }
}
