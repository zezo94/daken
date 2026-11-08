<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:255'],
            'sort' => [
                'nullable',
                'in:default,newest,oldest,name_asc,name_desc',
            ],
        ]);

        $locale = app()->getLocale();

        $search = trim($filters['search'] ?? '');
        $category = $filters['category'] ?? null;
        $sort = $filters['sort'] ?? 'default';

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $query = Product::query()
            ->with(['category', 'primaryImage'])
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            });

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name->ar', 'like', "%{$search}%")
                    ->orWhere('name->en', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        switch ($sort) {
            case 'newest':
                $query->orderByDesc('created_at');
                break;

            case 'oldest':
                $query->orderBy('created_at');
                break;

            case 'name_asc':
                $query->orderBy("name->{$locale}");
                break;

            case 'name_desc':
                $query->orderByDesc("name->{$locale}");
                break;

            default:
                $query->orderBy('sort_order')
                    ->orderByDesc('created_at');
                break;
        }

        $products = $query
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        return view('pages.products.index', compact(
            'products',
            'categories',
            'search',
            'category',
            'sort'
        ));
    }

    public function show(Request $request, string $slug): View
    {
        $product = Product::query()
            ->with([
                'category',
                'images',
                'primaryImage',
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('category', fn ($query) =>
            $query->where('is_active', true)
            )
            ->firstOrFail();

        // Record a view only for a visitor session,
        // and at most once per product every 30 minutes.
        if ($request->hasSession()) {
            $visitorId = hash_hmac(
                'sha256',
                $request->session()->getId(),
                config('app.key')
            );

            $recentViewExists = ProductView::query()
                ->where('product_id', $product->id)
                ->where('visitor_id', $visitorId)
                ->where('viewed_at', '>=', now()->subMinutes(30))
                ->exists();

            if (! $recentViewExists) {
                ProductView::create([
                    'product_id' => $product->id,
                    'visitor_id' => $visitorId,
                    'viewed_at' => now(),
                ]);
            }
        }

        $relatedProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        return view('pages.products.show', compact(
            'product',
            'relatedProducts'
        ));
    }
}
