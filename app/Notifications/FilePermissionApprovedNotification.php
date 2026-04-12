<?php

namespace App\Notifications;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FilePermissionApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public File $file,
        public string $approverName
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'success',
            'icon' => 'check_circle',
            'title' => 'Permission Approved',
            'message' => "Your request to access '{$this->file->title}' has been approved by {$this->approverName}.",
            'file_id' => $this->file->id,
            'url' => route('events.index') // Specific link can be added later
        ];
    }
}
