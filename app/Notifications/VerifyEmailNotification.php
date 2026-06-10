<?php

// app/Notifications/VerifyEmailNotification.php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify your Senflux email address')
            ->view('emails.verify-email', [
                'url'  => $url,
                'user' => $notifiable,
            ]);
    }
}