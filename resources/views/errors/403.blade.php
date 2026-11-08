@extends('layouts.app')

@section('title', '403 - Forbidden')

@section('content')
    <section class="container" style="text-align:center;padding-block:100px">
        <h1>403</h1>
        <p>ليس لديك صلاحية الوصول إلى هذه الصفحة.</p>
        <x-buttons.primary :href="route('home')">
            العودة للرئيسية
        </x-buttons.primary>
    </section>
@endsection
