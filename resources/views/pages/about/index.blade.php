
@extends('layouts.app')

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

@section(
    'title',
    $isArabic
        ? 'من نحن - DAKEN Chocolate'
        : 'About Us - DAKEN Chocolate'
)

@section('content')

    <main class="about-page">

        {{-- Hero Section --}}
        <section class="about-hero">

            <div class="container about-hero__inner">

            <span class="about-eyebrow">
                DAKEN CHOCOLATE
            </span>

                <h1>
                    {{ $isArabic ? 'من نحن' : 'About Us' }}
                </h1>

                <p>
                    {{ $isArabic
                        ? 'تعرّف على عالم DAKEN Chocolate.'
                        : 'Discover the world of DAKEN Chocolate.' }}
                </p>

            </div>

        </section>

        {{-- Our Story --}}
        <section class="container about-story">

            <div class="about-story__content">

            <span class="about-eyebrow">
                {{ $isArabic ? 'قصتنا' : 'OUR STORY' }}
            </span>

                <h2>
                    {{ $isArabic
                        ? 'شغف بالشوكولاتة'
                        : 'A Passion for Chocolate' }}
                </h2>

                <p>
                    {{ $isArabic
                        ? 'في DAKEN Chocolate، نؤمن بأن الشوكولاتة أكثر من مجرد مذاق؛ إنها تجربة تجمع بين المتعة واللحظات الجميلة.'
                        : 'At DAKEN Chocolate, we believe chocolate is more than a taste. It is an experience that brings together enjoyment and memorable moments.' }}
                </p>

                <p>
                    {{ $isArabic
                        ? 'نسعى إلى تقديم تجربة مميزة لمحبي الشوكولاتة، مع الاهتمام بالتفاصيل التي تجعل كل لحظة أكثر خصوصية.'
                        : 'We aim to create a distinctive experience for chocolate lovers, with attention to the details that make every moment special.' }}
                </p>

            </div>

            <div class="about-story__visual">

                <img
                    src="{{ asset('images/about/daken-story.jpg') }}"
                    alt="DAKEN Chocolate"
                    loading="lazy"
                    onerror="this.style.display='none';this.parentElement.classList.add('about-story__visual--empty');"
                >

            </div>

        </section>

        {{-- Vision and Mission --}}
        <section class="about-values">

            <div class="container">

                <div class="about-section-heading">

                <span class="about-eyebrow">
                    DAKEN CHOCOLATE
                </span>

                    <h2>
                        {{ $isArabic
                            ? 'ما الذي يميزنا؟'
                            : 'What Matters to Us' }}
                    </h2>

                </div>

                <div class="about-values__grid">

                    <article class="about-value-card">

                    <span class="about-value-card__number">
                        01
                    </span>

                        <h3>
                            {{ $isArabic ? 'رؤيتنا' : 'Our Vision' }}
                        </h3>

                        <p>
                            {{ $isArabic
                                ? 'أن نصنع تجارب شوكولاتة تترك أثرًا جميلًا في كل مناسبة.'
                                : 'To create chocolate experiences that leave a lasting impression on every occasion.' }}
                        </p>

                    </article>

                    <article class="about-value-card">

                    <span class="about-value-card__number">
                        02
                    </span>

                        <h3>
                            {{ $isArabic ? 'رسالتنا' : 'Our Mission' }}
                        </h3>

                        <p>
                            {{ $isArabic
                                ? 'تقديم تجربة شوكولاتة ممتعة مع الاهتمام بالجودة والتفاصيل.'
                                : 'To offer an enjoyable chocolate experience with attention to quality and detail.' }}
                        </p>

                    </article>

                    <article class="about-value-card">

                    <span class="about-value-card__number">
                        03
                    </span>

                        <h3>
                            {{ $isArabic ? 'قيمنا' : 'Our Values' }}
                        </h3>

                        <p>
                            {{ $isArabic
                                ? 'الشغف والإبداع والاهتمام بالتفاصيل في كل ما نقدمه.'
                                : 'Passion, creativity, and attention to detail in everything we offer.' }}
                        </p>

                    </article>

                </div>

            </div>

        </section>

        {{-- Closing Section --}}
        <section class="container about-closing">

        <span class="about-eyebrow">
            DAKEN CHOCOLATE
        </span>

            <h2>
                {{ $isArabic
                    ? 'اكتشف منتجاتنا'
                    : 'Discover Our Products' }}
            </h2>

            <p>
                {{ $isArabic
                    ? 'تعرّف على مجموعة منتجات DAKEN Chocolate.'
                    : 'Explore the DAKEN Chocolate product collection.' }}
            </p>

            <a
                href="{{ route('products.index') }}"
                class="about-closing__button"
            >
                {{ $isArabic
                    ? 'تصفح المنتجات'
                    : 'Explore Products' }}
            </a>

        </section>

    </main>

@endsection
