<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;

class StaffProfileInformationUpdatedResponse implements ProfileInformationUpdatedResponse
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $emailChanged = $user !== null
            && $user->wasChanged('email');

        if (! $emailChanged) {
            if ($request->wantsJson()) {
                return response()->json('', 200);
            }

            return back()->with(
                'status',
                'profile-information-updated'
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = app()->getLocale() === 'ar'
            ? 'تم تغيير البريد الإلكتروني بنجاح. يرجى تسجيل الدخول مجددًا.'
            : 'Your email address has been changed successfully. Please sign in again.';

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
