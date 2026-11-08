@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'إضافة صفحة | DAKEN' : 'Create Page | DAKEN')

@section('content')
@php($ar = app()->getLocale() === 'ar')

<section class="daken-cms daken-cms--editor" aria-labelledby="cms-page-title">
    <header class="daken-cms__heading">
        <div>
            <p class="daken-cms__eyebrow">DAKEN CONTENT MANAGEMENT</p>
            <h1 id="cms-page-title">{{ $ar ? 'إضافة صفحة جديدة' : 'Create a New Page' }}</h1>
            <p>{{ $ar ? 'أضف عنوانًا ومحتوى بالعربية والإنجليزية، وحدد صورة الصفحة وحالة النشر.' : 'Add Arabic and English content, a cover image, and publishing settings.' }}</p>
        </div>
        <a class="daken-cms__back" href="{{ route('admin.pages.index') }}">{{ $ar ? 'العودة إلى الصفحات' : 'Back to Pages' }}</a>
    </header>

    <div class="daken-cms__editor-shell">
        <div class="daken-cms__editor-intro">
            <span class="daken-cms__editor-icon" aria-hidden="true">✦</span>
            <div>
                <h2>{{ $ar ? 'معلومات الصفحة' : 'Page Information' }}</h2>
                <p>{{ $ar ? 'الحقول المطلوبة وعناصر النشر موجودة في النموذج أدناه.' : 'Complete the required fields and publishing options below.' }}</p>
            </div>
        </div>
        @if($errors->any())
            <div class="daken-cms__errors" role="alert">
                <strong>{{ $ar ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="daken-cms__form-area">
            @include('admin.pages._form')
        </div>
    </div>
</section>
@endsection
