<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductImageController extends Controller
{
    public function index(Product $product): View
    {
        $product->load('images');

        return view(
            'admin.products.images',
            compact('product')
        );
    }

    public function store(
        Request $request,
        Product $product
    ): RedirectResponse {
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ]);

        $uploadedPaths = [];

        try {
            DB::transaction(function () use (
                $request,
                $product,
                &$uploadedPaths
            ) {
                $nextOrder = (
                    (int) $product->images()
                        ->max('sort_order')
                    ) + 1;

                $hasPrimary = $product->images()
                    ->where('is_primary', true)
                    ->exists();

                foreach ($request->file('images') as $file) {
                    $path = $file->store(
                        'products/gallery',
                        'public'
                    );

                    if ($path === false) {
                        throw new \RuntimeException('Product image upload failed.');
                    }

                    $uploadedPaths[] = $path;

                    $product->images()->create([
                        'path' => $path,
                        'sort_order' => $nextOrder++,
                        'is_primary' => !$hasPrimary,
                    ]);

                    $hasPrimary = true;
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);

            throw $exception;
        }

        return back()->with(
            'success',
            'تم رفع صور المنتج بنجاح.'
        );
    }

    public function setPrimary(
        Product $product,
        ProductImage $image
    ): RedirectResponse {
        abort_unless(
            $image->product_id === $product->id,
            404
        );

        DB::transaction(function () use ($product, $image) {
            $product->images()
                ->update(['is_primary' => false]);

            $image->update(['is_primary' => true]);
        });

        return back()->with(
            'success',
            'تم تحديد الصورة الرئيسية بنجاح.'
        );
    }

    public function updateOrder(
        Request $request,
        Product $product
    ): RedirectResponse {
        $validated = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*' => [
                'required',
                'integer',
                'distinct',
            ],
        ]);

        $currentIds = $product->images()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        $submittedIds = collect($validated['order'])
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        if ($currentIds !== $submittedIds) {
            return back()->withErrors([
                'order' => 'قائمة الصور غير صحيحة.',
            ]);
        }

        DB::transaction(function () use ($product, $validated) {
            foreach ($validated['order'] as $index => $id) {
                $product->images()
                    ->whereKey($id)
                    ->update([
                        'sort_order' => $index,
                    ]);
            }
        });

        return back()->with(
            'success',
            'تم حفظ ترتيب الصور.'
        );
    }

    public function destroy(
        Product $product,
        ProductImage $image
    ): RedirectResponse {
        abort_unless(
            $image->product_id === $product->id,
            404
        );

        $path = $image->path;
        $wasPrimary = $image->is_primary;

        DB::transaction(function () use (
            $product,
            $image,
            $wasPrimary
        ) {
            $image->delete();

            if ($wasPrimary) {
                $nextImage = $product->images()
                    ->first();

                $nextImage?->update([
                    'is_primary' => true,
                ]);
            }
        });

        if (
            is_string($path)
            && preg_match(
                '~\Aproducts/gallery/[^/\\\\]+\.(?:jpg|jpeg|png|webp)\z~i',
                $path
            )
        ) {
            Storage::disk('public')->delete($path);
        }
        return back()->with(
            'success',
            'تم حذف الصورة بنجاح.'
        );
    }


    public function move(
        Request $request,
        Product $product,
        ProductImage $image
    ): RedirectResponse {
        abort_unless(
            $image->product_id === $product->id,
            404
        );

        $validated = $request->validate([
            'direction' => [
                'required',
                'in:up,down',
            ],
        ]);

        $images = $product->images()->get();

        $currentIndex = $images->search(
            fn ($item) => $item->id === $image->id
        );

        $targetIndex = $validated['direction'] === 'up'
            ? $currentIndex - 1
            : $currentIndex + 1;

        if (
            $currentIndex === false ||
            $targetIndex < 0 ||
            $targetIndex >= $images->count()
        ) {
            return back();
        }

        $target = $images[$targetIndex];

        DB::transaction(function () use (
            $image,
            $target
        ) {
            $currentOrder = $image->sort_order;
            $targetOrder = $target->sort_order;

            $image->update([
                'sort_order' => $targetOrder,
            ]);

            $target->update([
                'sort_order' => $currentOrder,
            ]);
        });

        return back()->with(
            'success',
            'تم تحديث ترتيب الصور.'
        );
    }

}
