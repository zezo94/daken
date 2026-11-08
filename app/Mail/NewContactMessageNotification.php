<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewContactMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $senderName,
        public readonly string $senderEmail,
        public readonly string $senderPhone,
        public readonly string $messageSubject,
        public readonly string $messageBody,
        public readonly ?string $adminUrl = null,
    ) {
    }

    public function build(): static
    {
        $mail = $this
            ->subject('DAKEN | رسالة تواصل جديدة')
            ->view('emails.new-contact-message');

        if (
            filter_var(
                $this->senderEmail,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $mail->replyTo(
                $this->senderEmail,
                $this->senderName
            );
        }

        return $mail;
    }
}
