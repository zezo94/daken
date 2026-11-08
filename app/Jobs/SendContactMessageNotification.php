<?php

namespace App\Jobs;

use App\Mail\NewContactMessageNotification;
use App\Models\ContactMessage;
use App\Services\SiteSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendContactMessageNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public array $backoff = [30, 120, 300];

    public function __construct(
        public int $messageId,
        public string $recipient
    ) {
        $this->onQueue('emails');
    }

    public function handle(SiteSettings $settings): void
    {
        // Respect the current notification switch.
        if (
            $settings->get(
                'contact_email_notifications_enabled',
                '0'
            ) !== '1'
        ) {
            return;
        }

        // Do not send to addresses removed by the administrator.
        $configuredEmails = preg_split(
            '/[\r\n,;]+/',
            $settings->get('contact_notification_emails', '')
        );

        $configuredEmails = array_map(
            fn ($email) => strtolower(trim($email)),
            $configuredEmails
        );

        if (
            ! in_array(
                strtolower($this->recipient),
                $configuredEmails,
                true
            )
        ) {
            return;
        }

        $message = ContactMessage::query()->find($this->messageId);

        if (! $message) {
            return;
        }

        Mail::to($this->recipient)->send(
            new NewContactMessageNotification(
                senderName: (string) $message->name,
                senderEmail: (string) $message->email,
                senderPhone: (string) ($message->phone ?? ''),
                messageSubject: (string) (
                $message->subject ?: 'رسالة تواصل جديدة'
                ),
                messageBody: (string) $message->message,
                adminUrl: route(
                    'admin.messages.show',
                    ['message' => $message->getKey()]
                ),
            )
        );
    }

    public function failed(?Throwable $exception): void
    {
        logger()->error('DAKEN email notification job failed', [
            'message_id' => $this->messageId,
            'recipient' => $this->recipient,
            'error' => $exception?->getMessage(),
        ]);
    }
}
