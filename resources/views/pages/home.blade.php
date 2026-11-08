
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

                        <div class="daken-category-grid">
                            @forelse($categories as $category)
                                <a
                                    href="{{ route('products.index', ['category' => $category->slug]) }}"
                                    class="daken-category-card"
                                >
                                    @if($category->image)
                                        <img
                                            src="{{ asset('storage/'.$category->image) }}"
                                            alt="{{ $category->translated_name }}"
                                            loading="lazy"
                                        >
                                    @endif

                                    <h3>
                                        {{ $category->translated_name }}
                                    </h3>
                                </a>
                            @empty
                                <p>
                                    {{ $ar
                                        ? 'ستتوفر التشكيلات قريبًا.'
                                        : 'Collections are coming soon.' }}
                                </p>
                            @endforelse
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

                        <div class="daken-product-grid">
                            @forelse($featuredProducts as $product)
                                <x-cards.product :product="$product" />
                            @empty
                                <p>
                                    {{ $ar
                                        ? 'لا توجد منتجات مميزة حاليًا.'
                                        : 'No featured products available yet.' }}
                                </p>
                            @endforelse
                        </div>

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
                                <span>DAKEN</span>
                                <small>CHOCOLATE</small>
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

                            <div class="daken-gallery-home__grid">

                                @foreach($galleryImages as $image)
                                    @php
                                        $imageTitle = $image->title[$locale]
                                            ?? $image->title['ar']
                                            ?? $image->title['en']
                                            ?? 'DAKEN';
                                    @endphp

                                    <a
                                        href="{{ route('gallery.index') }}"
                                        class="daken-gallery-home__item"
                                    >
                                        <img
                                            src="{{ $image->image_url }}"
                                            alt="{{ $imageTitle }}"
                                            loading="lazy"
                                        >

                                        <span class="daken-gallery-home__caption">
                                        {{ $imageTitle }}
                                    </span>
                                    </a>
                                @endforeach

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
