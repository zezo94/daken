<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AdminActivityLogger;
class ProductController extends Controller
{
    public function __construct(
        private ProductImageService $images
    ) {}



    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');
        $categoryId = $request->query('category_id', '');
        $featured = $request->query('featured', '');
        $sort = $request->query('sort', 'default');
        $viewMode = $request->query('view', 'table');

        if (! in_array($viewMode, ['table', 'grid'], true)) {
            $viewMode = 'table';
        }
        $products = Product::query()
            ->with('category')
            ->withCount('views')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name->ar', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when(
                in_array((string) $status, ['0', '1'], true),
                fn ($query) => $query->where(
                    'is_active',
                    (int) $status
                )
            )
            ->when(
                filter_var(
                    $categoryId,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                ) !== false,
                fn ($query) => $query->where(
                    'category_id',
                    $categoryId
                )
            )

            ->when(
                in_array((string) $featured, ['0', '1'], true),
                fn ($query) => $query->where(
                    'is_featured',
                    (int) $featured
                )
            )
            ->when(
                $sort === 'most_viewed',
                fn ($query) => $query->orderByDesc('views_count')
            )
            ->when(
                $sort === 'newest',
                fn ($query) => $query->orderByDesc('created_at')
            )
            ->when(
                $sort === 'oldest',
                fn ($query) => $query->orderBy('created_at')
            )
            ->when(
                ! in_array($sort, ['most_viewed', 'newest', 'oldest'], true),
                fn ($query) => $query->orderBy('sort_order')
            )
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.products.index',
            compact(
                'products',
                'categories',
                'search',
                'status',
                'categoryId',
                'featured',
                'sort',
                'viewMode'
            )
        );
    }

    public function create(): View
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->upload(
                $request->file('image')
            );
        }

        try {
            DB::transaction(function () use ($data): void {
                $product = Product::create($data);

                app(AdminActivityLogger::class)->log(
                    'product.created',
                    $product,
                    ['name' => $product->name]
                );
            });
        } catch (\Throwable $e) {
            $this->images->delete($data['image'] ?? null);

            throw $e;
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم إنشاء المنتج بنجاح.');
    }
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ): RedirectResponse {
        $data = $request->safe()->except('image');

        $oldImage = $product->image;
        $newImage = null;

        if ($request->hasFile('image')) {
            $newImage = $this->images->upload(
                $request->file('image')
            );

            $data['image'] = $newImage;
        }

        try {
            DB::transaction(function () use ($product, $data): void {
                $product->update($data);

                app(AdminActivityLogger::class)->log(
                    'product.updated',
                    $product,
                    ['name' => $product->name]
                );
            });
        } catch (\Throwable $e) {
            $this->images->delete($newImage);

            throw $e;
        }

        if ($newImage) {
            $this->images->delete($oldImage);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم تحديث المنتج بنجاح.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // حذف المنتج دون حذف ملف صورته
        DB::transaction(function () use ($product): void {
            $product->delete();

            app(AdminActivityLogger::class)->log(
                'product.deleted',
                $product,
                ['name' => $product->name]
            );
        });
        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'تم حذف المنتج بنجاح مع الاحتفاظ بملفات الصور.'
            );
    }



    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => [
                'required',
                'in:activate,deactivate,delete',
            ],
            'product_ids' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],
            'product_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],
        ]);

        $ids = $validated['product_ids'];
        $action = $validated['action'];
        $activityDetails = [
            'product_ids' => array_map('intval', $ids),
            'count' => count($ids),
        ];

        $activityActions = [
            'activate' => 'products.bulk_activated',
            'deactivate' => 'products.bulk_deactivated',
            'delete' => 'products.bulk_deleted',
        ];

        DB::transaction(function () use ($ids, $action, $activityActions, $activityDetails) {
            $products = Product::query()
                ->whereIn('id', $ids)
                ->get();

            if ($action === 'activate') {
                Product::query()
                    ->whereIn('id', $ids)
                    ->update(['is_active' => true]);
            }

            if ($action === 'deactivate') {
                Product::query()
                    ->whereIn('id', $ids)
                    ->update(['is_active' => false]);
            }

            if ($action === 'delete') {
                foreach ($products as $product) {
                    $product->delete();
                }
            }

            app(AdminActivityLogger::class)->log(
                $activityActions[$action],
                null,
                $activityDetails
            );
        });


        $messages = [
            'activate' => 'تم تفعيل المنتجات المحددة بنجاح.',
            'deactivate' => 'تم إلغاء تفعيل المنتجات المحددة بنجاح.',
            'delete' => 'تم حذف المنتجات المحددة بنجاح.',
        ];

        return redirect()
            ->route('admin.products.index')
            ->with('success', $messages[$action]);
    }

}
