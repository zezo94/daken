@extends('layouts.admin')
@section('title', 'إدارة معرض الصور')
@section('content')
<div class="dg-admin-wrap">
    <header class="dg-heading dg-heading--row"><div><h1>إدارة معرض الصور</h1><p>إضافة وتعديل وترتيب الصور المنشورة.</p></div><a class="dg-button" href="{{ route('admin.gallery.create') }}">+ إضافة صورة</a></header>
    @if(session('success'))<div class="dg-success" role="status">{{ session('success') }}</div>@endif
    <div class="dg-admin-list">
        @forelse($images as $image)
            <article class="dg-admin-item">
                <img src="{{ $image->image_url }}" alt="{{ $image->title['ar'] ?? '' }}" loading="lazy">
                <div class="dg-admin-item__body"><h2>{{ $image->title[app()->getLocale()] ?? $image->title['ar'] ?? '' }}</h2><p>{{ $image->category }} · الترتيب: {{ $image->sort_order }}</p><span class="dg-state">{{ $image->is_active ? 'منشورة' : 'مسودة' }}</span></div>
                <a class="dg-outline" href="{{ route('admin.gallery.edit', $image) }}">تعديل</a>
            </article>
        @empty
            <div class="dg-empty">لا توجد صور في المعرض بعد.</div>
        @endforelse
    </div>
    <div class="dg-pagination">{{ $images->links() }}</div>
</div>
@endsection
