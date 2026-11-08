@extends('layouts.admin')
@section('title', 'إدارة الصفحات - DAKEN')
@section('content')
<section class="daken-cms">
    <div class="daken-cms__heading">
        <div><h1>إدارة صفحات الموقع</h1><p>أنشئ الصفحات وعدّل محتواها بالعربية والإنجليزية.</p></div>
        <a class="daken-cms__button" href="{{ route('admin.pages.create') }}">+ إضافة صفحة</a>
    </div>
    @if(session('success'))<div class="daken-cms__notice" role="status">{{ session('success') }}</div>@endif
    <div class="daken-cms__table-wrap"><table class="daken-cms__table">
        <thead><tr><th>الصورة</th><th>الصفحة</th><th>الرابط</th><th>الحالة</th><th>الإجراء</th></tr></thead>
        <tbody>
        @forelse($pages as $page)
            <tr>
                <td>@if($page->image_url)<img class="daken-cms__thumb" src="{{ $page->image_url }}" alt="">@else <span>—</span> @endif</td>
                <td><strong>{{ $page->translated_title }}</strong></td>
                <td><code>{{ $page->key }}</code></td>
                <td>{{ $page->is_active ? 'منشورة' : 'مخفية' }}</td>
                <td><a class="daken-cms__button daken-cms__button--small" href="{{ route('admin.pages.edit', $page) }}">تعديل</a></td>
            </tr>
        @empty
            <tr><td colspan="5">لا توجد صفحات بعد.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</section>
@endsection
