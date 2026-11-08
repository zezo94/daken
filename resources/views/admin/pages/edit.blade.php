@extends('layouts.admin')
@section('title', 'تعديل الصفحة - DAKEN')
@section('content')
<section class="daken-cms"><div class="daken-cms__heading"><div><h1>تعديل: {{ $page->translated_title }}</h1><p>تعديل المحتوى وحالة النشر والصورة الرئيسية.</p></div></div>@include('admin.pages._form')</section>
@endsection
