<?php

namespace App\Http\Middleware;

use App\Support\AdminLoginPolicy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveStaffAuthentication
{
    public function handle(
        Request $request,
        Closure $next,
        string $guard = 'web'
    ): Response {
        $auth = Auth::guard($guard);

        if (! $auth->check()) {
            return redirect()->guest(route('login'));
        }

        $user = $auth->user();

        if (! $user || ! AdminLoginPolicy::allows($user)) {
            $auth->logout();

            $request->session()->invalidate();
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
