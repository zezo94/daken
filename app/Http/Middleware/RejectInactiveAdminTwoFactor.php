<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\AdminLoginPolicy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RejectInactiveAdminTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        // Protect only Fortify's 2FA completion endpoint.
        if (! $request->isMethod('POST')
            || ! $request->is('two-factor-challenge')) {
            return $next($request);
        }

        $userId = $request->session()->get('login.id');

        if ($userId === null) {
            return $next($request);
        }

        $user = User::query()->find($userId);

        if (
            $user instanceof User
            && ! AdminLoginPolicy::allows($user)
        ) {
            $request->session()->forget([
                'login.id',
                'login.remember',
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __('auth.failed'),
                ]);
        }

        return $next($request);
    }
}
