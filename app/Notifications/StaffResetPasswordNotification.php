<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class StaffResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        $ar = app()->getLocale() === 'ar';

        return (new MailMessage)
            ->subject($ar
                ? 'إعادة تعيين كلمة المرور - DAKEN'
                : 'Reset Your Password - DAKEN'
            )
            ->greeting($ar
                ? 'مرحبًا،'
                : 'Hello,'
            )
            ->line($ar
                ? 'تلقينا طلبًا لإعادة تعيين كلمة المرور الخاصة بحسابك الوظيفي في DAKEN.'
                : 'We received a request to reset the password for your DAKEN staff account.'
            )
            ->action(
                $ar ? 'إعادة تعيين كلمة المرور' : 'Reset Password',
                $url
            )
            ->line($ar
                ? 'تنتهي صلاحية رابط الاستعادة خلال 60 دقيقة.'
                : 'This password reset link expires in 60 minutes.'
            )
            ->line($ar
                ? 'إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة.'
                : 'If you did not request a password reset, you can ignore this email.'
            );
    }

    protected function resetUrl($notifiable): string
    {
        return route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }

}
