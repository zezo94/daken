@extends('layouts.app')

@section('title', '404 - Forbidden')

@section('content')
    <section class="container" style="text-align:center;padding-block:100px">
        <h1>404</h1>
        <p>الصفحة المطلوبة غير موجودة</p>
        <x-buttons.primary :href="route('home')">
            العودة للرئيسية
        </x-buttons.primary>
    </section>
@endsection
