<?php

namespace App\Notifications;

use App\Models\KycSubmission;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycRejectedNotification extends Notification
{
    public function __construct(private readonly KycSubmission $submission) {}

    public function via($notifiable): array {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Action needed: your Senflux identity verification')
            ->view('emails.kyc-rejected', [
                'user'       => $notifiable,
                'submission' => $this->submission,
            ]);
    }
}
