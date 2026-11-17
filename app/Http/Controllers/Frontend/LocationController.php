<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'in:6,12,24,48'],
        ]);

        $search = trim($filters['search'] ?? '');
        $city = trim($filters['city'] ?? '');
        $perPage = (int) ($filters['per_page'] ?? 12);

        $cities = Location::query()
            ->where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $locations = Location::query()
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name->ar', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%")
                        ->orWhere('address->ar', 'like', "%{$search}%")
                        ->orWhere('address->en', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->when($city !== '', function ($query) use ($city) {
                $query->where('city', $city);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('pages.locations.index', compact(
            'locations',
            'cities',
            'search',
            'city',
            'perPage'
        ));
    }
}
