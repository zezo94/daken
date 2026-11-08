<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\SiteVisit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsExportController extends Controller
{
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'period' => ['nullable', 'in:7,30,90'],
            'report' => [
                'required',
                'in:daily,pages,products',
            ],
        ]);

        $period = (int) ($validated['period'] ?? 30);
        $report = $validated['report'];

        $start = today()->subDays($period - 1);
        $end = today()->addDay();

        $filename = "daken-{$report}-{$period}-days.csv";

        return response()->streamDownload(function () use (
            $report,
            $start,
            $end
        ) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM for Arabic text in Microsoft Excel.
            fwrite($output, "\xEF\xBB\xBF");

            if ($report === 'daily') {
                fputcsv($output, [
                    'Date',
                    'Website Visits',
                    'Product Views',
                ]);

                $visits = SiteVisit::query()
                    ->where('visited_at', '>=', $start)
                    ->where('visited_at', '<', $end)
                    ->selectRaw(
                        'DATE(visited_at) as day, COUNT(*) as total'
                    )
                    ->groupByRaw('DATE(visited_at)')
                    ->pluck('total', 'day');

                $productViews = ProductView::query()
                    ->where('viewed_at', '>=', $start)
                    ->where('viewed_at', '<', $end)
                    ->selectRaw(
                        'DATE(viewed_at) as day, COUNT(*) as total'
                    )
                    ->groupByRaw('DATE(viewed_at)')
                    ->pluck('total', 'day');

                $date = $start->copy();

                while ($date->lt($end)) {
                    $day = $date->format('Y-m-d');

                    fputcsv($output, [
                        $day,
                        $visits->get($day, 0),
                        $productViews->get($day, 0),
                    ]);

                    $date->addDay();
                }
            }

            if ($report === 'pages') {
                fputcsv($output, [
                    'Page',
                    'Visits',
                ]);

                SiteVisit::query()
                    ->where('visited_at', '>=', $start)
                    ->where('visited_at', '<', $end)
                    ->select('path')
                    ->selectRaw('COUNT(*) as total')
                    ->groupBy('path')
                    ->orderByDesc('total')
                    ->cursor()
                    ->each(function ($page) use ($output) {
                        fputcsv($output, [
                            $page->path,
                            $page->total,
                        ]);
                    });
            }

            if ($report === 'products') {
                fputcsv($output, [
                    'Product ID',
                    'Product Name (Arabic)',
                    'Product Name (English)',
                    'Views',
                ]);

                Product::query()
                    ->withCount([
                        'views as period_views_count' =>
                            function ($query) use ($start, $end) {
                                $query
                                    ->where('viewed_at', '>=', $start)
                                    ->where('viewed_at', '<', $end);
                            },
                    ])
                    ->orderByDesc('period_views_count')
                    ->cursor()
                    ->each(function ($product) use ($output) {
                        fputcsv($output, [
                            $product->id,
                            data_get($product->name, 'ar', ''),
                            data_get($product->name, 'en', ''),
                            $product->period_views_count,
                        ]);
                    });
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }
}
