
@extends('layouts.app')

@section('title', 'Register - DAKEN')

@section('content')
    <section class="container" style="max-width: 480px; padding-block: 80px">

        <h1>{{ __('Create account') }}</h1>

        @if($errors->any())
            <div role="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('Name') }}</label>
                <input id="name" name="name"
                       value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input id="password" type="password"
                       name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">
                    {{ __('Confirm Password') }}
                </label>
                <input id="password_confirmation"
                       type="password"
                       name="password_confirmation" required>
            </div>

            <x-buttons.primary type="submit">
                {{ __('Register') }}
            </x-buttons.primary>
        </form>
    </section>
@endsection
