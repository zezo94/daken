
@extends('layouts.app')

@php
    $isArabic = app()->getLocale() === 'ar';

    $isAbout = $page->key === 'about';
    $isVision = $page->key === 'vision';

    $pageLabel = match ($page->key) {
        'about' => $isArabic ? 'قصتنا' : 'OUR STORY',
        'vision' => $isArabic ? 'رؤيتنا' : 'OUR VISION',
        default => 'DAKEN CHOCOLATE',
    };
@endphp

@section('title', $page->translated_title . ' - DAKEN')

@section('content')

    <main class="site-content-page">

        {{-- Page Header --}}
        <section class="site-content-hero">

            <div class="container site-content-hero__inner">

            <span class="site-content-hero__eyebrow">
                DAKEN CHOCOLATE
            </span>

                <h1>
                    {{ $page->translated_title }}
                </h1>

                <div class="site-content-hero__line"></div>

            </div>

        </section>

        {{-- Main Content --}}
        <section class="container site-content-section">

            <div class="site-content-section__intro">

            <span class="site-content-section__eyebrow">
                {{ $pageLabel }}
            </span>

                <h2>
                    {{ $page->translated_title }}
                </h2>

            </div>

            <div class="site-content-layout">

                <article class="site-content-card">

                    <div class="site-content-card__body">
                        {!! nl2br(e($page->translated_body ?? '')) !!}
                    </div>

                </article>

                <aside class="site-content-aside">

                    @if($page->image_url)

                        <img
                            src="{{ $page->image_url }}"
                            alt="{{ $page->translated_title }}"
                            class="site-content-aside__image"
                            loading="lazy"
                        >

                    @else

                        <div class="site-content-aside__brand">
                            <span>DAKEN</span>
                            <small>CHOCOLATE</small>
                        </div>

                        <div class="site-content-aside__decoration">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>

                    @endif

                </aside>

            </div>

        </section>

        {{-- Related Page --}}
        @if($isAbout || $isVision)

            <section class="site-content-related">

                <div class="container site-content-related__inner">

                    <div>

                    <span class="site-content-related__eyebrow">
                        DAKEN CHOCOLATE
                    </span>

                        <h2>
                            {{ $isAbout
                                ? ($isArabic ? 'اكتشف رؤيتنا' : 'Discover Our Vision')
                                : ($isArabic ? 'تعرّف على قصتنا' : 'Discover Our Story') }}
                        </h2>

                    </div>

                    <a
                        href="{{ $isAbout ? route('vision') : route('about') }}"
                        class="site-content-related__link"
                    >
                        {{ $isArabic ? 'اكتشف المزيد' : 'Learn More' }}

                        <span aria-hidden="true">↗</span>
                    </a>

                </div>

            </section>

        @endif

    </main>

@endsection
