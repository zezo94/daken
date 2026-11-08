@extends('layouts.app')

@section('title', $page->translated_title . ' | DAKEN')

@section('content')
@php($ar = app()->getLocale() === 'ar')
<main class="daken-public-page" dir="{{ $ar ? 'rtl' : 'ltr' }}">
    <div class="daken-public-page__glow" aria-hidden="true"></div>
    <div class="daken-public-page__inner">
        <header class="daken-public-page__heading">
            <p class="daken-public-page__eyebrow">DAKEN <span>CHOCOLATE</span></p>
            <span class="daken-public-page__rule" aria-hidden="true"></span>
            <h1>{{ $page->translated_title }}</h1>
            <p class="daken-public-page__subtitle">{{ $ar ? 'اكتشف المزيد عن عالم داكن' : 'Discover more about DAKEN' }}</p>
        </header>

        <div class="daken-public-page__content {{ $page->image_url ? 'daken-public-page__content--with-image' : '' }}">
            @if($page->image_url)
                <figure class="daken-public-page__visual">
                    <img class="daken-public-page__hero" src="{{ $page->image_url }}" alt="{{ $page->translated_title }}" loading="eager" decoding="async">
                </figure>
            @endif
            <article class="daken-public-page__story">
                <div class="daken-public-page__story-label">{{ $ar ? 'DAKEN | محتوى الصفحة' : 'DAKEN | OUR WORLD' }}</div>
                <div class="daken-public-page__body">{{ $page->translated_body }}</div>
                <div class="daken-public-page__story-line" aria-hidden="true"></div>
            </article>
        </div>
    </div>
</main>
@endsection
