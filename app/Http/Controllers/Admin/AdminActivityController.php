<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminActivityController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);

        $action = $filters['action'] ?? '';
        $userId = $filters['user_id'] ?? '';
        $dateFrom = $filters['date_from'] ?? '';
        $dateTo = $filters['date_to'] ?? '';

        $activities = $this->filteredQuery($filters)
            ->with('user:id,name')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $users = User::query()
            ->whereHas('adminActivities')
            ->orderBy('name')
            ->get(['id', 'name']);

        // All statistics respect the selected filters.
        $statistics = [
            'total' => $this->filteredQuery($filters)->count(),

            'today' => $this->filteredQuery($filters)
                ->whereDate('created_at', today())
                ->count(),

            'last_7_days' => $this->filteredQuery($filters)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),

            'product_actions' => $this->filteredQuery($filters)
                ->where('action', 'like', 'product%')
                ->count(),
        ];

        return view('admin.activities.index', compact(
            'activities',
            'action',
            'userId',
            'dateFrom',
            'dateTo',
            'users',
            'statistics'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);

        $filename = 'daken-admin-activities-'
            . now()->format('Y-m-d-His')
            . '.csv';

        return response()->streamDownload(
            function () use ($filters): void {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    throw new \RuntimeException(
                        'Unable to open CSV output stream.'
                    );
                }

                try {
                    // UTF-8 BOM for Excel.
                    fwrite($handle, "\xEF\xBB\xBF");

                    fputcsv($handle, [
                        'ID',
                        'User',
                        'Action',
                        'Subject Type',
                        'Subject ID',
                        'IP Address',
                        'Date',
                    ]);

                    $this->filteredQuery($filters)
                        ->with('user:id,name')
                        ->lazyById(500, 'id')
                        ->each(function (AdminActivity $activity) use ($handle) {
                            fputcsv($handle, [
                                $activity->id,
                                $this->safeCsv($activity->user?->name),
                                $this->safeCsv($activity->action),
                                $this->safeCsv($activity->subject_type),
                                $activity->subject_id,
                                $this->safeCsv($activity->ip_address),
                                $activity->created_at?->format('Y-m-d H:i:s'),
                            ]);
                        });
                } finally {
                    fclose($handle);
                }
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, private',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'action' => [
                'nullable',
                'string',
                'max:50',
            ],
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'date_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:date_from',
            ],
        ]);
    }

    private function filteredQuery(array $filters): Builder
    {
        $query = AdminActivity::query();

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->where(
                'created_at',
                '>=',
                Carbon::parse($filters['date_from'])->startOfDay()
            );
        }

        if (! empty($filters['date_to'])) {
            $query->where(
                'created_at',
                '<=',
                Carbon::parse($filters['date_to'])->endOfDay()
            );
        }

        return $query;
    }

    private function safeCsv(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Spreadsheet software may interpret leading control
        // characters and formula prefixes as executable formulas.
        if (preg_match('/^[\s\x00-\x1F]*[=+\-@]/u', $value)) {
            return "'" . $value;
        }

        return $value;
    }



    public function show(AdminActivity $activity): View
    {
        $activity->load('user:id,name');

        return view(
            'admin.activities.show',
            compact('activity')
        );
    }

}
