<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\PasswordUpdateResponse;

class StaffPasswordUpdateResponse implements PasswordUpdateResponse
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = app()->getLocale() === 'ar'
            ? 'تم تغيير كلمة المرور بنجاح. يرجى تسجيل الدخول مجددًا.'
            : 'Your password has been changed successfully. Please sign in again.';

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
