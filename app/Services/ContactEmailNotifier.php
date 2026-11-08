<?php

namespace App\Services;

use App\Jobs\SendContactMessageNotification;
use App\Models\ContactMessage;

class ContactEmailNotifier
{
    public function __construct(
        private readonly SiteSettings $settings
    ) {
    }

    public function dispatch(ContactMessage $message): void
    {
        if (
            $this->settings->get(
                'contact_email_notifications_enabled',
                '0'
            ) !== '1'
        ) {
            return;
        }

        $rawEmails = $this->settings->get(
            'contact_notification_emails',
            ''
        );

        $recipients = preg_split('/[\r\n,;]+/', $rawEmails);

        $recipients = array_values(
            array_unique(
                array_filter(
                    array_map(
                        fn ($email) => strtolower(trim($email)),
                        $recipients
                    ),
                    fn ($email) => filter_var(
                        $email,
                        FILTER_VALIDATE_EMAIL
                    )
                )
            )
        );

        foreach ($recipients as $recipient) {
            SendContactMessageNotification::dispatch(
                (int) $message->getKey(),
                $recipient
            )->onQueue('emails')->afterCommit();
        }
    }
}
