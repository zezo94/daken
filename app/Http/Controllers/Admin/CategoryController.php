<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AdminActivityLogger;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');

        $categories = Category::query()
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
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.categories.index',
            compact('categories', 'search', 'status')
        );
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(
        StoreCategoryRequest $request
    ): RedirectResponse {
        $data = $request->safe()->except('image');

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('categories', 'public');
            if ($imagePath === false) {
                throw new \RuntimeException('Category image upload failed.');
            }
            $data['image'] = $imagePath;
        }

        try {
            DB::transaction(function () use ($data): void {
                $category = Category::create($data);

                app(AdminActivityLogger::class)->log(
                    'category.created',
                    $category,
                    ['name' => $category->name]
                );
            });
        } catch (\Throwable $e) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $e;
        }

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'تم إنشاء التصنيف بنجاح.'
            );
    }

    public function edit(Category $category): View
    {
        return view(
            'admin.categories.edit',
            compact('category')
        );
    }

    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ): RedirectResponse {
        $data = $request->safe()->except('image');

        $oldImage = $category->image;
        $newImage = null;

        if ($request->hasFile('image')) {
            $newImage = $request->file('image')
                ->store('categories', 'public');
            if ($newImage === false) {
                throw new \RuntimeException('Category image upload failed.');
            }

            $data['image'] = $newImage;
        }

        try {
            DB::transaction(function () use ($category, $data): void {
                $category->update($data);

                app(AdminActivityLogger::class)->log(
                    'category.updated',
                    $category,
                    ['name' => $category->name]
                );
            });
        } catch (\Throwable $e) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $e;
        }

        if (
            $newImage
            && is_string($oldImage)
            && preg_match(
                '~\Acategories/[^/\\\\]+\.(?:jpg|jpeg|png|webp)\z~i',
                $oldImage
            )
        ) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'تم تحديث التصنيف بنجاح.'
            );
    }

    public function destroy(
        Category $category
    ): RedirectResponse {
        $imagePath = null;

        $deleted = DB::transaction(function () use (
            $category,
            &$imagePath
        ): bool {
            $lockedCategory = Category::query()
                ->whereKey($category->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedCategory->products()->exists()) {
                return false;
            }

            $imagePath = $lockedCategory->image;

            $lockedCategory->delete();

            app(AdminActivityLogger::class)->log(
                'category.deleted',
                $lockedCategory,
                ['name' => $lockedCategory->name]
            );

            return true;
        });

        if (! $deleted) {
            return back()->withErrors([
                'category' => 'لا يمكن حذف تصنيف يحتوي على منتجات.',
            ]);
        }

        if (
            is_string($imagePath)
            && preg_match(
                '~\Acategories/[^/\\\\]+\.(?:jpg|jpeg|png|webp)\z~i',
                $imagePath
            )
        ) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'تم حذف التصنيف بنجاح.'
            );
    }
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:categories,id',
            ],
        ]);

        $ids = $validated['ids'];

        $deletedCount = 0;
        $skippedCount = 0;
        $imagesToDelete = [];

        DB::transaction(function () use (
            $ids,
            &$deletedCount,
            &$skippedCount,
            &$imagesToDelete
        ) {
            $categories = Category::query()
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get();

            foreach ($categories as $category) {
                if ($category->products()->exists()) {
                    $skippedCount++;
                    continue;
                }

                if ($category->image) {
                    $imagesToDelete[] = $category->image;
                }

                $category->delete();
                $deletedCount++;
            }

            if ($deletedCount > 0) {
                app(AdminActivityLogger::class)->log(
                    'categories.bulk_deleted',
                    null,
                    [
                        'deleted_count' => $deletedCount,
                        'skipped_count' => $skippedCount,
                    ]
                );
            }
        });

        foreach ($imagesToDelete as $imagePath) {
            if (
                is_string($imagePath)
                && preg_match(
                    '~\Acategories/[^/\\\\]+\.(?:jpg|jpeg|png|webp)\z~i',
                    $imagePath
                )
            ) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                "تم حذف {$deletedCount} تصنيف. " .
                "تم تجاوز {$skippedCount} تصنيف مرتبط بمنتجات."
            );
    }
}
