
@props([
    'product',
])

@php
    $galleryImages = $product->images
        ->map(fn ($image) => [
            'path' => $image->path,
            'primary' => (bool) $image->is_primary,
        ])
        ->values();

    if ($galleryImages->isEmpty() && $product->image) {
        $galleryImages = collect([
            [
                'path' => $product->image,
                'primary' => true,
            ],
        ]);
    }

    $galleryImages = $galleryImages
        ->sortByDesc('primary')
        ->values();

    $productName = $product->name[app()->getLocale()]
        ?? $product->name['ar']
        ?? 'DAKEN Chocolate';
@endphp

<div class="daken-product-gallery" data-product-gallery>

    @if($galleryImages->isNotEmpty())

        <div class="daken-product-gallery__main">
            <img
                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($galleryImages->first()['path']) }}"
                alt="{{ $productName }}"
                data-gallery-main
            >
        </div>

        @if($galleryImages->count() > 1)
            <div class="daken-product-gallery__thumbs">

                @foreach($galleryImages as $image)
                    <button
                        type="button"
                        class="daken-product-gallery__thumb"
                        data-gallery-thumb
                        data-image-url="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image['path']) }}"
                        aria-label="{{ app()->getLocale() === 'ar' ? 'عرض الصورة' : 'View image' }} {{ $loop->iteration }}"
                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                    >
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image['path']) }}"
                            alt=""
                            loading="lazy"
                        >
                    </button>
                @endforeach

            </div>
        @endif

    @else

        <div class="daken-product-gallery__empty">
            DAKEN Chocolate
        </div>

    @endif

</div>
