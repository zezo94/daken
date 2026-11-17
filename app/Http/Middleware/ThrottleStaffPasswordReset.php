<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

class ThrottleStaffPasswordReset
{
    public function __construct(
        private readonly ThrottleRequests $throttle
    ) {}

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Password Reset Requests Only
        |--------------------------------------------------------------------------
        |
        | Apply the named rate limiter only to Fortify's password
        | reset-link submission route.
        |
        */

        if (! $request->routeIs('password.email')) {
            return $next($request);
        }

        return $this->throttle->handle(
            $request,
            $next,
            'staff-password-reset'
        );
    }
}
