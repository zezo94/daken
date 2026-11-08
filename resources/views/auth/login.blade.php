
@extends('layouts.app')

@section('title', 'Login - DAKEN')

@section('content')
    <section class="container" style="max-width: 480px; padding-block: 80px">

        <h1>{{ __('Login') }}</h1>

        @if($errors->any())
            <div role="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <label>
                <input type="checkbox" name="remember">
                {{ __('Remember me') }}
            </label>

            <x-buttons.primary type="submit">
                {{ __('Login') }}
            </x-buttons.primary>
        </form>

        <a href="{{ route('register') }}">
            {{ __('Create account') }}
        </a>
    </section>
@endsection
