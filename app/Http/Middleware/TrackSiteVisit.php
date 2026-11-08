<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisit
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $response = $next($request);

        // Count only successful page views.
        if (
            ! $request->isMethod('GET') ||
            $response->getStatusCode() !== 200 ||
            ! $request->acceptsHtml()
        ) {
            return $response;
        }

        // Track only selected public pages.
        $routeName = $request->route()?->getName();

        $trackedRoutes = [
            'home',
            'about',
            'vision',
            'products.index',
            'locations.index',
            'contact.index',
        ];

        if (! in_array($routeName, $trackedRoutes, true)) {
            return $response;
        }

        // Use the existing Laravel session rather than
        // creating an additional tracking cookie.
        $sessionId = $request->session()->getId();

        if (! $sessionId) {
            return $response;
        }

        $visitorId = hash_hmac(
            'sha256',
            $sessionId,
            config('app.key')
        );

        $path = '/' . ltrim($request->path(), '/');

        // Avoid duplicate views of the same page
        // within 30 minutes for the same session.
        $recentlyCounted = SiteVisit::query()
            ->where('visitor_id', $visitorId)
            ->where('path', $path)
            ->where(
                'visited_at',
                '>=',
                now()->subMinutes(30)
            )
            ->exists();

        if (! $recentlyCounted) {
            SiteVisit::create([
                'visitor_id' => $visitorId,
                'path' => $path,
                'visited_at' => now(),
            ]);
        }

        return $response;
    }
}
