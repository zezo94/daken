@extends('layouts.app')

@section('title', '419 - Forbidden')

@section('content')
    <section class="container" style="text-align:center;padding-block:100px">
        <h1>419</h1>
        <p>انتهت صلاحية الجلسة.</p>
        <x-buttons.primary :href="route('home')">
            العودة للرئيسية
        </x-buttons.primary>
    </section>
@endsection
