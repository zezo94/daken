<?php

namespace App\Http\Middleware;

use App\Support\AdminLoginPolicy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if ($user && AdminLoginPolicy::allows($user)) {
            return $next($request);
        }

        if ($user) {
            auth()->guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __('auth.failed'),
                ]);
        }

        abort(403);
    }
}
