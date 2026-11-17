<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StaffPasswordResetLinkResponse implements Responsable
{
    public function __construct(
        protected string $status = ''
    ) {}

    public function toResponse($request): JsonResponse|RedirectResponse
    {
        $message = app()->getLocale() === 'ar'
            ? 'إذا كان البريد الإلكتروني مرتبطًا بحساب موظف نشط، فستصلك رسالة تحتوي على رابط استعادة كلمة المرور.'
            : 'If this email belongs to an active staff account, you will receive a password reset link.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }
}
