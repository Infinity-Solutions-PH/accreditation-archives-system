<?php

namespace App\Notifications;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FileUploadedNotification extends Notification
{
    use Queueable;

    public function __construct(public File $file)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New File Uploaded',
            'message' => "{$this->file->uploadedBy->name} uploaded '{$this->file->title}' to your college/program repository.",
            'type' => 'upload',
            'icon' => 'upload_file',
            'file_id' => $this->file->id,
            'url' => route('file-archives', ['type' => $this->file->is_general ? 'general' : 'personal'])
        ];
    }
}
