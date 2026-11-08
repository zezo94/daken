
@props(['product'])

@php
    $imagePath = $product->display_image_path;
@endphp

<article class="product-card">

    <a
        href="{{ route('products.show', $product->slug) }}"
        class="product-card__image"
    >
        @if($imagePath)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath) }}"
                alt="{{ $product->translated_name }}"
                loading="lazy"
            >
        @else
            <div class="product-card__placeholder">
                DAKEN
            </div>
        @endif
    </a>

    <div class="product-card__body">

        <span class="product-card__category">
            {{ $product->category?->translated_name }}
        </span>

        <h3>
            <a href="{{ route('products.show', $product->slug) }}">
                {{ $product->translated_name }}
            </a>
        </h3>

        @if($product->weight)
            <p>
                {{ $product->weight }}
                {{ app()->getLocale() === 'ar' ? 'غرام' : 'g' }}
            </p>
        @endif

        <a
            class="product-card__link"
            href="{{ route('products.show', $product->slug) }}"
        >
            {{ __('products.details') }}
            <span aria-hidden="true">←</span>
        </a>

    </div>

</article>
