<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccreditorInviteNotification extends Notification
{
    use Queueable;

    public $password;
    public $event;

    /**
     * Create a new notification instance.
     */
    public function __construct($password, $event = null)
    {
        $this->password = $password;
        $this->event = $event;
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
        $mail = (new MailMessage)
                    ->subject('Invitation to Accreditation Drive')
                    ->greeting('Hello, ' . $notifiable->name . '!')
                    ->line('You have been invited as an Accreditor for the IDO Archives System.');
                    
        if ($this->event) {
            $mail->line('You are assigned to the accreditation session: ' . $this->event->title);
        }

        return $mail->line('Your login credentials are:')
                    ->line('Email: ' . $notifiable->email)
                    ->line('Password: ' . $this->password)
                    ->action('Login Now', url('/accreditor/auth'))
                    ->line('If you have a Google account registered with this email, you can also sign in via Google.')
                    ->line('Thank you for using our application!');
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
