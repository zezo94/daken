@extends('layouts.app')

@section('title', '429 - Forbidden')

@section('content')
    <section class="container" style="text-align:center;padding-block:100px">
        <h1>429</h1>
        <p>عدد محاولات كبير، حاول لاحقًا.</p>
        <x-buttons.primary :href="route('home')">
            العودة للرئيسية
        </x-buttons.primary>
    </section>
@endsection
