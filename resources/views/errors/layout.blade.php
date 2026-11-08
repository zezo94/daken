
@props([
    'code',
    'message',
])

@extends('layouts.app')

@section('content')
    <section class="container"
             style="text-align:center;padding-block:100px">

        <h1 style="font-size:72px">
            {{ $code }}
        </h1>

        <p>{{ $message }}</p>

        <x-buttons.primary :href="route('home')">
            {{ __('nav.home') }}
        </x-buttons.primary>

    </section>
@endsection
