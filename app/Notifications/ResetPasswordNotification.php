<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function __construct(string $token) {
        parent::__construct($token);
    }

    /**
     * Build the mail representation of the notification.
     * Overrides the default Laravel password reset email.
     */
    public function toMail($notifiable): MailMessage {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your Senflux password')
            ->view('emails.reset-password', [
                'url'  => $url,
                'user' => $notifiable,
            ]);
    }
}