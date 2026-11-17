<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\PasswordResetResponse;

class StaffPasswordResetResponse implements PasswordResetResponse
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = app()->getLocale() === 'ar'
            ? 'تمت إعادة تعيين كلمة المرور بنجاح. يرجى تسجيل الدخول بكلمة المرور الجديدة.'
            : 'Your password has been reset successfully. Please sign in with your new password.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('login'),
            ]);
        }

        return redirect()
            ->route('login')
            ->with('status', $message);
    }
}
