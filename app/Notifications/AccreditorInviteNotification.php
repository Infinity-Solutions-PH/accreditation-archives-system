<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccreditorInviteNotification extends Notification
{
    use Queueable;

    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitation to CvSU Accreditation Archives')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('You have been invited to join the CvSU Accreditation Archives system as an Accreditor.')
            ->line('You can access the system using the following credentials:')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Password:** ' . $this->password)
            ->action('Login to System', route('accreditor.auth'))
            ->line('Your access is temporary and will expire on ' . ($notifiable->expires_at ? $notifiable->expires_at->format('M d, Y') : 'N/A') . '.')
            ->line('Thank you for your service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
