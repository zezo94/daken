<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Location;
use App\Models\Product;
use App\Models\SiteVisit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            $user
            && $user->role === 'admin'
            && $user->is_active,
            403
        );

        $canProducts = $user->hasAdminPermission('products.manage');
        $canCategories = $user->hasAdminPermission('categories.manage');
        $canLocations = $user->hasAdminPermission('locations.manage');
        $canMessages = $user->hasAdminPermission('messages.manage');
        $canAnalytics = $user->hasAdminPermission('analytics.view');
        $canActivities = $user->hasAdminPermission('activities.view');

        $stats = [];

        $recentProducts = collect();
        $recentMessages = collect();
        $recentActivities = collect();

        $topProducts = collect();
        $topPages = collect();

        $visitChart = [];
        $uniqueVisitorsThisWeek = 0;
        $yesterdayVisits = 0;

        /*
        |--------------------------------------------------------------------------
        | Product statistics
        |--------------------------------------------------------------------------
        */

        if ($canProducts) {
            $stats['products'] = Product::query()->count();

            $stats['active_products'] = Product::query()
                ->where('is_active', true)
                ->count();

            $stats['featured_products'] = Product::query()
                ->where('is_featured', true)
                ->count();

            $recentProducts = Product::query()
                ->with('category')
                ->latest()
                ->limit(5)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        if ($canCategories) {
            $stats['categories'] = Category::query()->count();
        }

        /*
        |--------------------------------------------------------------------------
        | Locations
        |--------------------------------------------------------------------------
        */

        if ($canLocations) {
            $stats['locations'] = Location::query()->count();

            $stats['active_locations'] = Location::query()
                ->where('is_active', true)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | Contact messages
        |--------------------------------------------------------------------------
        */

        if ($canMessages) {
            $stats['messages'] = ContactMessage::query()->count();

            $stats['unread_messages'] = ContactMessage::query()
                ->where('is_read', false)
                ->count();

            $recentMessages = ContactMessage::query()
                ->latest()
                ->limit(5)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Visitor analytics
        |--------------------------------------------------------------------------
        */

        if ($canAnalytics) {
            $today = today()->startOfDay();
            $tomorrow = $today->copy()->addDay();
            $weekStart = $today->copy()->subDays(6);
            $yesterday = $today->copy()->subDay();

            $stats['total_visits'] = SiteVisit::query()->count();

            $stats['unique_visitors'] = SiteVisit::query()
                ->distinct()
                ->count('visitor_id');

            $stats['today_visits'] = SiteVisit::query()
                ->where('visited_at', '>=', $today)
                ->where('visited_at', '<', $tomorrow)
                ->count();

            $stats['week_visits'] = SiteVisit::query()
                ->where('visited_at', '>=', $weekStart)
                ->count();

            $uniqueVisitorsThisWeek = SiteVisit::query()
                ->where('visited_at', '>=', $weekStart)
                ->distinct()
                ->count('visitor_id');

            $yesterdayVisits = SiteVisit::query()
                ->where('visited_at', '>=', $yesterday)
                ->where('visited_at', '<', $today)
                ->count();

            $visitsByDay = SiteVisit::query()
                ->where('visited_at', '>=', $weekStart)
                ->get(['visited_at'])
                ->groupBy(
                    fn ($visit) => $visit->visited_at->format('Y-m-d')
                );

            $visitChart = collect(range(6, 0))
                ->map(function (int $daysAgo) use (
                    $today,
                    $visitsByDay
                ): array {
                    $date = $today->copy()
                        ->subDays($daysAgo)
                        ->format('Y-m-d');

                    return [
                        'date' => $date,
                        'visits' => $visitsByDay
                            ->get($date, collect())
                            ->count(),
                    ];
                })
                ->values()
                ->all();

            $topPages = SiteVisit::query()
                ->select('path')
                ->selectRaw('COUNT(*) AS visits_count')
                ->groupBy('path')
                ->orderByDesc('visits_count')
                ->limit(5)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Product performance
        |--------------------------------------------------------------------------
        |
        | Product view statistics are only loaded when the user
        | can access both products and analytics.
        |
        */

        if ($canProducts && $canAnalytics) {
            $topProducts = Product::query()
                ->withCount('views')
                ->orderByDesc('views_count')
                ->limit(5)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Recent administrator activities
        |--------------------------------------------------------------------------
        */

        if ($canActivities) {
            $recentActivities = AdminActivity::query()
                ->with('user:id,name')
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentProducts' => $recentProducts,
            'recentMessages' => $recentMessages,
            'recentActivities' => $recentActivities,
            'topProducts' => $topProducts,
            'topPages' => $topPages,
            'visitChart' => $visitChart,
            'uniqueVisitorsThisWeek' => $uniqueVisitorsThisWeek,
            'yesterdayVisits' => $yesterdayVisits,
        ]);
    }
}
