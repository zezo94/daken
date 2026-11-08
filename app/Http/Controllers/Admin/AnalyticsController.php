<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\SiteVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'period' => ['nullable', 'in:7,30,90'],
        ]);

        $period = (int) ($validated['period'] ?? 30);

        $start = today()->subDays($period - 1);
        $end = today()->addDay();

        $previousStart = $start->copy()->subDays($period);
        $previousEnd = $start->copy();

        $visits = fn ($from, $to) =>
        SiteVisit::query()
            ->where('visited_at', '>=', $from)
            ->where('visited_at', '<', $to);

        $productViews = fn ($from, $to) =>
        ProductView::query()
            ->where('viewed_at', '>=', $from)
            ->where('viewed_at', '<', $to);

        // Current period
        $totalVisits = $visits($start, $end)->count();

        $uniqueVisitors = $visits($start, $end)
            ->distinct()
            ->count('visitor_id');

        $totalProductViews = $productViews($start, $end)
            ->count();

        $averageViews = $uniqueVisitors > 0
            ? round($totalVisits / $uniqueVisitors, 1)
            : 0;

        // Previous period
        $previousVisits = $visits(
            $previousStart,
            $previousEnd
        )->count();

        $previousVisitors = $visits(
            $previousStart,
            $previousEnd
        )
            ->distinct()
            ->count('visitor_id');

        $previousProductViews = $productViews(
            $previousStart,
            $previousEnd
        )->count();

        $previousAverage = $previousVisitors > 0
            ? $previousVisits / $previousVisitors
            : 0;

        // Percentage changes
        $changes = [
            'visits' => $this->percentageChange(
                $totalVisits,
                $previousVisits
            ),
            'visitors' => $this->percentageChange(
                $uniqueVisitors,
                $previousVisitors
            ),
            'products' => $this->percentageChange(
                $totalProductViews,
                $previousProductViews
            ),
            'average' => $this->percentageChange(
                $averageViews,
                $previousAverage
            ),
        ];

        // Top pages
        $topPages = $visits($start, $end)
            ->select('path')
            ->selectRaw('COUNT(*) as visits_count')
            ->groupBy('path')
            ->orderByDesc('visits_count')
            ->limit(10)
            ->get();

        // Top products
        $topProducts = Product::query()
            ->withCount([
                'views as period_views_count' => function ($query) use (
                    $start,
                    $end
                ) {
                    $query
                        ->where('viewed_at', '>=', $start)
                        ->where('viewed_at', '<', $end);
                },
            ])
            ->orderByDesc('period_views_count')
            ->limit(10)
            ->get();

        // Aggregate daily visits in MySQL
        $dailyVisits = $visits($start, $end)
            ->selectRaw(
                'DATE(visited_at) as day, COUNT(*) as total'
            )
            ->groupBy(DB::raw('DATE(visited_at)'))
            ->pluck('total', 'day');

        // Aggregate daily product views in MySQL
        $dailyProductViews = $productViews($start, $end)
            ->selectRaw(
                'DATE(viewed_at) as day, COUNT(*) as total'
            )
            ->groupBy(DB::raw('DATE(viewed_at)'))
            ->pluck('total', 'day');

        $chartData = collect(range(0, $period - 1))
            ->map(function ($offset) use (
                $start,
                $dailyVisits,
                $dailyProductViews
            ) {
                $date = $start->copy()
                    ->addDays($offset)
                    ->format('Y-m-d');

                return [
                    'date' => $date,
                    'visits' => (int) $dailyVisits->get($date, 0),
                    'product_views' => (int) $dailyProductViews->get(
                        $date,
                        0
                    ),
                ];
            })
            ->all();

        return view('admin.analytics.index', compact(
            'period',
            'totalVisits',
            'uniqueVisitors',
            'totalProductViews',
            'averageViews',
            'changes',
            'topPages',
            'topProducts',
            'chartData'
        ));
    }

    private function percentageChange(
        float|int $current,
        float|int $previous
    ): ?float {
        if ($previous == 0) {
            return $current == 0 ? 0.0 : null;
        }

        return round(
            (($current - $previous) / $previous) * 100,
            1
        );
    }
}
