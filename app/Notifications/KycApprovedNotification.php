<?php

namespace App\Notifications;

use App\Models\KycSubmission;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycApprovedNotification extends Notification
{
    public function __construct(private readonly KycSubmission $submission) {}

    public function via($notifiable): array {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Your Senflux identity verification was approved')
            ->view('emails.kyc-approved', [
                'user'       => $notifiable,
                'submission' => $this->submission,
            ]);
    }
}
