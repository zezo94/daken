<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $supported = config('brand.languages', ['ar', 'en']);

        $locale = $request->session()->get(
            'locale',
            config('brand.default_language', 'ar')
        );

        if (! in_array($locale, $supported, true)) {
            $locale = 'ar';
        }

        App::setLocale($locale);

        return $next($request);
    }
}