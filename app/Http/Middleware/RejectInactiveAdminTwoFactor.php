<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\AdminLoginPolicy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RejectInactiveAdminTwoFactor
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Pending Two-Factor Login
        |--------------------------------------------------------------------------
        |
        | Fortify stores the pending user's ID in login.id.
        | A missing ID means there is no pending challenge to validate.
        |
        */

        $userId = $request->session()->get('login.id');

        if ($userId === null) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Active Admin Account
        |--------------------------------------------------------------------------
        */

        $user = User::query()->find($userId);

        if (
            ! $user instanceof User ||
            ! AdminLoginPolicy::allows($user)
        ) {
            /*
            |--------------------------------------------------------------------------
            | Cancel Pending Login
            |--------------------------------------------------------------------------
            */

            $request->session()->forget([
                'login.id',
                'login.remember',
            ]);

            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __('auth.failed'),
                ]);
        }

        return $next($request);
    }
}
