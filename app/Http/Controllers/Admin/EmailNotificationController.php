<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailNotificationController extends Controller
{
    public function edit(SiteSettings $settings): View
    {
        return view('admin.email-notifications.edit', [
            'enabled' => $settings->get(
                    'contact_email_notifications_enabled',
                    '0'
                ) === '1',

            'emails' => $settings->get(
                'contact_notification_emails',
                ''
            ),
        ]);
    }

    public function update(
        Request $request,
        SiteSettings $settings
    ): RedirectResponse {
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
            'emails' => ['nullable', 'string', 'max:5000'],
        ]);

        $lines = preg_split(
            '/[\r\n,;]+/',
            $validated['emails'] ?? ''
        );

        $emails = [];

        foreach ($lines as $line) {
            $email = trim($line);

            if ($email === '') {
                continue;
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return back()
                    ->withErrors([
                        'emails' => "عنوان البريد غير صالح: {$email}",
                    ])
                    ->withInput();
            }

            $emails[strtolower($email)] = $email;
        }

        if (count($emails) > 20) {
            return back()
                ->withErrors([
                    'emails' => 'الحد الأقصى هو 20 بريدًا إلكترونيًا.',
                ])
                ->withInput();
        }

        if ((int) $validated['enabled'] === 1 && empty($emails)) {
            return back()
                ->withErrors([
                    'emails' => 'أضف بريدًا إلكترونيًا واحدًا على الأقل لتفعيل الإشعارات.',
                ])
                ->withInput();
        }

        $settings->setMany([
            'contact_email_notifications_enabled' =>
                (string) (int) $validated['enabled'],

            'contact_notification_emails' =>
                implode("\n", array_values($emails)),
        ]);

        return redirect()
            ->route('admin.email-notifications.edit')
            ->with('success', 'تم حفظ إعدادات إشعارات البريد الإلكتروني بنجاح.');
    }
}
