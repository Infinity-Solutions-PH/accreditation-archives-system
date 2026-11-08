<?php

namespace App\Notifications;

use App\Models\Area;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use App\Models\AccreditationEvent;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CommentReplyNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Comment $comment,
        public AccreditationEvent $event,
        public Area $area,
        public string $replierName
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('areas.slug', [
            'event' => $this->event->slug,
            'area' => $this->area->slug,
        ]);

        return (new MailMessage)
            ->subject("New Reply in Discussion: {$this->event->title} ({$this->area->code})")
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line("{$this->replierName} replied to a discussion in **{$this->event->title}** ({$this->area->code} - {$this->area->name}):")
            ->line('"' . $this->comment->content . '"')
            ->action('View Discussion', $url)
            ->line('Thank you for using CvSU Archives!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Reply in Discussion',
            'message' => "{$this->replierName} replied in {$this->area->code} ({$this->event->title}): \"{$this->comment->content}\"",
            'type' => 'info',
            'icon' => 'forum',
            'event_id' => $this->event->id,
            'area_id' => $this->area->id,
            'comment_id' => $this->comment->id,
            'url' => route('areas.slug', [
                'event' => $this->event->slug,
                'area' => $this->area->slug,
            ]),
        ];
    }
}

