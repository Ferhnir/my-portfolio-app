<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $onQueue = 'emails';

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $token
    )
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function viaQueues(): array
    {
        return [
            'mail' => $this->onQueue,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        \Log::info($url);

        return (new MailMessage)
            ->subject('Password Reset')
            ->view('emails.password-reset', [
                'user' => $notifiable,
                'resetUrl' => $url,
                'expire' => config('auth.passwords.users.expire'),
            ]);

    }
}

