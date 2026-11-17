
@extends('layouts.app')

@section('title', 'DAKEN Chocolate')

@section('content')

    @php
        $locale = app()->getLocale();
        $ar = $locale === 'ar';

        /*
        |--------------------------------------------------------------------------
        | Hero settings
        |--------------------------------------------------------------------------
        */

        $heroTitle = $settings['hero_title_'.$locale]
            ?? ($ar
                ? 'شوكولاتة تصنع اللحظات'
                : 'Chocolate Made for Moments');

        $heroSubtitle = $settings['hero_subtitle_'.$locale]
            ?? ($ar
                ? 'اكتشف تشكيلتنا من الشوكولاتة.'
                : 'Discover our chocolate collection.');

        $heroButton = $settings['hero_button_text_'.$locale]
            ?? ($ar
                ? 'اكتشف المنتجات'
                : 'Explore Products');

        $heroImage = $settings['hero_image'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Homepage sections
        |--------------------------------------------------------------------------
        */

        $sectionDefaults = [
            'categories' => 10,
            'featured' => 20,
            'about' => 30,
            'locations' => 40,
            'gallery' => 50,
            'contact' => 60,
        ];

        $homepageSections = [];

        foreach ($sectionDefaults as $key => $defaultOrder) {
            $isVisible = (string) (
                $settings['home_show_'.$key] ?? '1'
            ) === '1';

            if (!$isVisible) {
                continue;
            }

            $homepageSections[] = [
                'key' => $key,
                'order' => (int) (
                    $settings['home_order_'.$key]
                    ?? $defaultOrder
                ),
                'default_order' => $defaultOrder,
            ];
        }

        usort(
            $homepageSections,
            fn ($a, $b) =>
                ($a['order'] <=> $b['order'])
                ?: ($a['default_order'] <=> $b['default_order'])
        );
    @endphp

    {{-- ==================================================
         HERO
         ================================================== --}}

    <section
        class="daken-hero"
        @if($heroImage)
            style="--hero-image: url('{{ asset('storage/'.$heroImage) }}')"
        @endif
    >
        <div class="container daken-hero__inner">
            <div class="daken-hero__content">

                <span class="daken-eyebrow">
                    DAKEN CHOCOLATE
                </span>

                <h1>{{ $heroTitle }}</h1>

                <p>{{ $heroSubtitle }}</p>

                <a
                    href="{{ route('products.index') }}"
                    class="daken-button"
                >
                    {{ $heroButton }}
                </a>

            </div>
        </div>
    </section>

    {{-- ==================================================
         DYNAMIC SECTION ORDER
         ================================================== --}}

    @foreach($homepageSections as $homeSection)

        @switch($homeSection['key'])

            {{-- ==========================================
                 CATEGORIES
                 ========================================== --}}

            @case('categories')
                <section class="daken-section">
                    <div class="container">

                        <div class="daken-section__heading">

                            <span class="daken-eyebrow">
                                DAKEN COLLECTION
                            </span>

                            <h2>
                                {{ $ar
                                    ? 'تشكيلاتنا'
                                    : 'Our Collections' }}
                            </h2>

                        </div>


                        <div
                            class="daken-collections-carousel"
                            data-daken-collections-carousel
                        >
                            <div
                                class="daken-category-grid"
                                data-daken-collections-track
                                tabindex="0"
                                aria-label="{{ $ar ? 'تصفح التشكيلات' : 'Browse collections' }}"
                            >
                                @forelse($categories as $category)

                                    <div class="daken-layered-frame">

                                        <a
                                            href="{{ route('products.index', ['category' => $category->slug]) }}"
                                            class="daken-category-card"
                                        >
                                            <h3>
                                                {{ $category->translated_name }}
                                            </h3>

                                            <div class="daken-category-card__visual">
                                                @if($category->image)
                                                    <img
                                                        src="{{ asset('storage/'.$category->image) }}"
                                                        alt="{{ $category->translated_name }}"
                                                        loading="lazy"
                                                        decoding="async"
                                                    >
                                                @else
                                                    <div
                                                        class="daken-category-card__placeholder"
                                                        aria-hidden="true"
                                                    ></div>
                                                @endif
                                            </div>
                                        </a>

                                    </div>

                                @empty

                                    <p>
                                        {{ $ar
                                            ? 'ستتوفر التشكيلات قريبًا.'
                                            : 'Collections are coming soon.' }}
                                    </p>

                                @endforelse
                            </div>

                            @if($categories->count() > 1)
                                <div class="daken-featured-carousel__controls">

                                    <button
                                        type="button"
                                        class="daken-featured-carousel__arrow"
                                        data-daken-collections-prev
                                        aria-label="{{ $ar ? 'التشكيلة السابقة' : 'Previous collection' }}"
                                    >
                                        &#8249;
                                    </button>

                                    <button
                                        type="button"
                                        class="daken-featured-carousel__arrow"
                                        data-daken-collections-next
                                        aria-label="{{ $ar ? 'التشكيلة التالية' : 'Next collection' }}"
                                    >
                                        &#8250;
                                    </button>

                                </div>
                            @endif
                        </div>

                    </div>
                </section>
                @break

                {{-- ==========================================
                     FEATURED PRODUCTS
                     ========================================== --}}


            @case('featured')
                <section class="daken-section daken-section--cream">
                    <div class="container">

                        <div class="daken-section__heading">
                <span class="daken-eyebrow">
                    SELECTED FOR YOU
                </span>

                            <h2>
                                {{ $ar
                                    ? 'منتجات مميزة'
                                    : 'Featured Products' }}
                            </h2>
                        </div>

                        @if($featuredProducts->isNotEmpty())

                            <div
                                class="daken-featured-carousel"
                                data-daken-featured-carousel
                                dir="{{ $ar ? 'rtl' : 'ltr' }}"
                            >
                                <div
                                    class="daken-product-grid"
                                    id="daken-featured-track"
                                    data-daken-featured-track
                                    tabindex="0"
                                    role="region"
                                    aria-roledescription="carousel"
                                    aria-label="{{ $ar ? 'المنتجات المميزة' : 'Featured products' }}"
                                >
                                    @foreach($featuredProducts as $product)
                                        <x-cards.product :product="$product" />
                                    @endforeach
                                </div>

                                @if($featuredProducts->count() > 1)
                                    <div class="daken-featured-carousel__controls">

                                        <button
                                            type="button"
                                            class="daken-featured-carousel__arrow"
                                            data-daken-featured-prev
                                            aria-controls="daken-featured-track"
                                            aria-label="{{ $ar ? 'المنتج السابق' : 'Previous product' }}"
                                        >
                                            <span aria-hidden="true">‹</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="daken-featured-carousel__arrow"
                                            data-daken-featured-next
                                            aria-controls="daken-featured-track"
                                            aria-label="{{ $ar ? 'المنتج التالي' : 'Next product' }}"
                                        >
                                            <span aria-hidden="true">›</span>
                                        </button>

                                    </div>
                                @endif
                            </div>

                        @else

                            <p>
                                {{ $ar
                                    ? 'لا توجد منتجات مميزة حاليًا.'
                                    : 'No featured products available yet.' }}
                            </p>

                        @endif

                        <div class="daken-section__action">
                            <a
                                href="{{ route('products.index') }}"
                                class="daken-button daken-button--outline"
                            >
                                {{ $ar
                                    ? 'عرض جميع المنتجات'
                                    : 'View All Products' }}
                            </a>
                        </div>

                    </div>
                </section>
                @break

                {{-- ==========================================
                     ABOUT DAKEN
                     ========================================== --}}

            @case('about')
                @if($aboutPage)

                    <section class="daken-section daken-about">
                        <div class="container daken-about__inner">

                            <div>

                                <span class="daken-eyebrow">
                                    OUR STORY
                                </span>

                                <h2>
                                    {{ $aboutPage->translated_title }}
                                </h2>

                                <p class="daken-about__text">
                                    {{ \Illuminate\Support\Str::limit(
                                        $aboutPage->translated_body,
                                        350
                                    ) }}
                                </p>

                                <a
                                    href="{{ route('about') }}"
                                    class="daken-button"
                                >
                                    {{ $ar
                                        ? 'اقرأ قصتنا'
                                        : 'Read Our Story' }}
                                </a>

                            </div>

                            <div class="daken-about__visual">

                                @if($aboutPage->image_url)
                                    <img
                                        src="{{ $aboutPage->image_url }}"
                                        alt="{{ $aboutPage->translated_title }}"
                                        class="daken-about__image"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                @else
                                    <span>DAKEN</span>
                                    <small>CHOCOLATE</small>
                                @endif

                            </div>

                        </div>
                    </section>

                @endif
                @break

                {{-- ==========================================
                     LOCATIONS
                     ========================================== --}}

            @case('locations')
                <section class="daken-section daken-section--cream">
                    <div class="container">

                        <div class="daken-section__heading">

                            <span class="daken-eyebrow">
                                FIND DAKEN
                            </span>

                            <h2>
                                {{ $ar
                                    ? 'أماكن تواجدنا'
                                    : 'Find Us' }}
                            </h2>

                        </div>

                        <div class="daken-location-grid">

                            @forelse($locations as $location)


                                <article class="daken-location-card">

    <span class="daken-location-card__icon" aria-hidden="true">
        <svg
            viewBox="0 0 24 24"
            width="24"
            height="24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
            <circle cx="12" cy="10" r="2.5"/>
        </svg>
    </span>

                                    <h3>
                                        {{ $location->translated_name }}
                                    </h3>


                                    <p>
                                        {{ $location->city }},
                                        {{ $location->country }}
                                    </p>

                                    @if($location->google_maps_url)

                                        <a
                                            href="{{ $location->google_maps_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            {{ $ar
                                                ? 'عرض الموقع'
                                                : 'View Location' }}
                                        </a>

                                    @endif

                                </article>

                            @empty

                                <p>
                                    {{ $ar
                                        ? 'ستُضاف أماكن البيع قريبًا.'
                                        : 'Locations will be added soon.' }}
                                </p>

                            @endforelse

                        </div>

                        <div class="daken-section__action">

                            <a
                                href="{{ route('locations.index') }}"
                                class="daken-button daken-button--outline"
                            >
                                {{ $ar
                                    ? 'جميع المواقع'
                                    : 'All Locations' }}
                            </a>

                        </div>

                    </div>
                </section>
                @break

                {{-- ==========================================
                     GALLERY
                     ========================================== --}}


            @case('gallery')
                <section
                    class="daken-gallery-home"
                    aria-labelledby="home-gallery-title"
                >
                    <div class="container">

                        <div class="daken-gallery-home__heading">
                            <div>
                                <span class="daken-eyebrow">
                                    DAKEN GALLERY
                                </span>

                                <h2 id="home-gallery-title">
                                    {{ $ar
                                        ? 'معرض صور DAKEN'
                                        : 'DAKEN Gallery' }}
                                </h2>

                                <p>
                                    {{ $ar
                                        ? 'اكتشف لحظات وتفاصيل من عالم DAKEN.'
                                        : 'Discover moments and details from the world of DAKEN.' }}
                                </p>
                            </div>

                            <a
                                href="{{ route('gallery.index') }}"
                                class="daken-gallery-home__all"
                            >
                                {{ $ar
                                    ? 'عرض جميع الصور'
                                    : 'View All Photos' }}
                            </a>
                        </div>

                        @if($galleryImages->isNotEmpty())

                            <div
                                class="daken-gallery-carousel"
                                data-daken-gallery-carousel
                                dir="{{ $ar ? 'rtl' : 'ltr' }}"
                            >

                                <div
                                    class="daken-gallery-home__grid"
                                    id="daken-gallery-track"
                                    data-daken-gallery-track
                                    tabindex="0"
                                    role="region"
                                    aria-roledescription="carousel"
                                    aria-label="{{ $ar
                                        ? 'معرض صور داكن'
                                        : 'DAKEN Gallery' }}"
                                >

                                    @foreach($galleryImages as $image)
                                        @php
                                            $imageTitle =
                                                $image->title[$locale]
                                                ?? $image->title['ar']
                                                ?? $image->title['en']
                                                ?? 'DAKEN';
                                        @endphp

                                        <div class="daken-layered-frame">
                                            <a
                                                href="{{ $image->image_url }}"
                                                class="daken-gallery-home__item"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                aria-label="{{ $ar
                                                    ? 'عرض الصورة كاملة'
                                                    : 'View full image' }}: {{ $imageTitle }}"
                                            >
                                                <img
                                                    src="{{ $image->image_url }}"
                                                    alt="{{ $imageTitle }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                >

                                                <span class="daken-gallery-home__caption">
                                                    {{ $imageTitle }}
                                                </span>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>

                                @if($galleryImages->count() > 1)
                                    <div class="daken-gallery-carousel__controls">

                                        <button
                                            type="button"
                                            class="daken-gallery-carousel__arrow"
                                            data-daken-gallery-prev
                                            aria-controls="daken-gallery-track"
                                            aria-label="{{ $ar
                                                ? 'الصورة السابقة'
                                                : 'Previous image' }}"
                                        >
                                            <span aria-hidden="true">‹</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="daken-gallery-carousel__arrow"
                                            data-daken-gallery-next
                                            aria-controls="daken-gallery-track"
                                            aria-label="{{ $ar
                                                ? 'الصورة التالية'
                                                : 'Next image' }}"
                                        >
                                            <span aria-hidden="true">›</span>
                                        </button>

                                    </div>
                                @endif

                            </div>

                        @else

                            <div class="daken-gallery-home__empty">
                                {{ $ar
                                    ? 'لا توجد صور منشورة في المعرض حاليًا.'
                                    : 'No published gallery images yet.' }}
                            </div>

                        @endif

                    </div>
                </section>
                @break


                {{-- ==========================================
                     CONTACT CTA
                     ========================================== --}}

            @case('contact')
                <section class="daken-contact-cta">
                    <div class="container">

                        <h2>
                            {{ $ar
                                ? 'هل لديك استفسار؟'
                                : 'Have a Question?' }}
                        </h2>

                        <p>
                            {{ $ar
                                ? 'يسعد فريق DAKEN بالتواصل معك.'
                                : 'The DAKEN team would love to hear from you.' }}
                        </p>

                        <a
                            href="{{ route('contact.index') }}"
                            class="daken-button"
                        >
                            {{ $ar
                                ? 'تواصل معنا'
                                : 'Contact Us' }}
                        </a>

                    </div>
                </section>
                @break

        @endswitch

    @endforeach

@endsection
