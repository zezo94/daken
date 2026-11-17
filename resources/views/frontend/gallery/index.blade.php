@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'معرض الصور | DAKEN' : 'Gallery | DAKEN')
@section('content')
@php
    $ar = app()->getLocale() === 'ar';
    $categories = ['' => [$ar ? 'الكل' : 'All', ''], 'products' => [$ar ? 'المنتجات' : 'Products', 'products'], 'packaging' => [$ar ? 'التغليف' : 'Packaging', 'packaging'], 'campaigns' => [$ar ? 'الحملات' : 'Campaigns', 'campaigns'], 'events' => [$ar ? 'الفعاليات' : 'Events', 'events'], 'general' => [$ar ? 'عام' : 'General', 'general']];
@endphp
<div class="dg-gallery" dir="{{ $ar ? 'rtl' : 'ltr' }}">
    <div class="dg-gallery__container">
        <header class="dg-gallery__hero"><span>DAKEN CHOCOLATE</span><h1>{{ $ar ? 'معرض الصور' : 'Photo Gallery' }}</h1><p>{{ $ar ? 'لحظات وصور من عالم DAKEN' : 'Explore the world of DAKEN through images' }}</p></header>
        <nav class="dg-gallery__filters" aria-label="{{ $ar ? 'تصنيفات المعرض' : 'Gallery categories' }}">
            @foreach($categories as $value => $data)
                <a href="{{ route('gallery.index', $value === '' ? [] : ['category' => $value]) }}" class="{{ $category === $value ? 'is-active' : '' }}" @if($category === $value) aria-current="page" @endif>{{ $data[0] }}</a>
            @endforeach
        </nav>
        <div class="dg-gallery__grid">
            @forelse($images as $image)
                <figure class="dg-gallery__card">
                    <a href="{{ $image->image_url }}" target="_blank" rel="noopener" aria-label="{{ $ar ? 'تكبير الصورة' : 'View full image' }}: {{ $image->title[$ar ? 'ar' : 'en'] ?? '' }}">
                        <img src="{{ $image->image_url }}" alt="{{ $image->title[$ar ? 'ar' : 'en'] ?? $image->title['ar'] ?? '' }}" loading="lazy">
                    </a>
                    <figcaption><h2>{{ $image->title[$ar ? 'ar' : 'en'] ?? $image->title['ar'] ?? '' }}</h2>@if(!empty($image->description[$ar ? 'ar' : 'en']))<p>{{ $image->description[$ar ? 'ar' : 'en'] }}</p>@endif</figcaption>
                </figure>
            @empty
                <p class="dg-gallery__empty">{{ $ar ? 'لا توجد صور منشورة في هذا القسم حاليًا.' : 'No published images in this category yet.' }}</p>
            @endforelse
        </div>
        <div class="dg-gallery__pagination">{{ $images->links() }}</div>
    </div>
</div>
@endsection
