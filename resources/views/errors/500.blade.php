@extends('layouts.app')

@section('title', '500 - Forbidden')

@section('content')
    <section class="container" style="text-align:center;padding-block:100px">
        <h1>500</h1>
        <p>حدث خطأ غير متوقع.</p>
        <x-buttons.primary :href="route('home')">
            العودة للرئيسية
        </x-buttons.primary>
    </section>
@endsection
