
@extends('layouts.app')

@php
    $locale = app()->getLocale();

    $description = $product->description ?? [];

    $productDescription = is_array($description)
        ? ($description[$locale] ?? $description['en'] ?? '')
        : (string) $description;

    $productName = $product->translated_name;

    $pageDescription = \Illuminate\Support\Str::limit(
        trim(preg_replace('/\s+/u', ' ', strip_tags($productDescription))),
        155,
        ''
    );

    $canonicalUrl = route('products.show', $product->slug);

    $imagePath = $product->display_image_path;

    $imageUrl = $imagePath
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath)
        : null;
@endphp

@section('title', $productName . ' - DAKEN Chocolate')

@section('meta_description', $pageDescription)

@section('canonical', $canonicalUrl)

@section('og_image', $imageUrl ?? '')

@section('content')

    <div class="container">

        <nav
            class="product-details__breadcrumb"
            aria-label="{{ $locale === 'ar' ? 'مسار التنقل' : 'Breadcrumb' }}"
        >
            <a href="{{ route('home') }}">
                {{ $locale === 'ar' ? 'الرئيسية' : 'Home' }}
            </a>

            <span aria-hidden="true">/</span>

            <a href="{{ route('products.index') }}">
                {{ $locale === 'ar' ? 'المنتجات' : 'Products' }}
            </a>

            <span aria-hidden="true">/</span>

            <span aria-current="page">
                {{ $productName }}
            </span>
        </nav>

    </div>

    <section class="container product-details">

        <div class="product-details__image">

            <x-products.gallery :product="$product" />

        </div>

        <div class="product-details__info">

            @if($product->category)
                <span class="eyebrow">
                    {{ $product->category->translated_name }}
                </span>
            @endif

            <h1>{{ $productName }}</h1>

            @if($productDescription !== '')
                <div class="product-details__description">
                    {{ $productDescription }}
                </div>
            @endif

            <div class="product-details__meta">

                @if($product->weight)
                    <div class="product-details__meta-item">
                        <strong>
                            {{ __('products.weight') }}:
                        </strong>

                        {{ $product->weight }}

                        {{ $locale === 'ar' ? 'غرام' : 'g' }}
                    </div>
                @endif

                @if($product->sku)
                    <div class="product-details__meta-item">
                        <strong>SKU:</strong>
                        {{ $product->sku }}
                    </div>
                @endif

            </div>

            <div class="product-details__actions">

                <x-buttons.primary :href="route('products.index')">
                    {{ __('products.back') }}
                </x-buttons.primary>

            </div>

        </div>

    </section>

    @if($relatedProducts->isNotEmpty())

        <section class="container related-products">

            <h2>
                {{ __('products.related') }}
            </h2>

            <div class="products-grid">

                @foreach($relatedProducts as $related)
                    <x-cards.product :product="$related" />
                @endforeach

            </div>

        </section>

    @endif

    @php
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $productName,
            'description' => $productDescription,
            'url' => $canonicalUrl,
            'brand' => [
                '@type' => 'Brand',
                'name' => 'DAKEN Chocolate',
            ],
        ];

        if ($imageUrl) {
            $structuredData['image'] = [$imageUrl];
        }

        if ($product->sku) {
            $structuredData['sku'] = $product->sku;
        }
    @endphp

    <script type="application/ld+json">
        {!! json_encode(
            $structuredData,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        ) !!}
    </script>

@endsection
