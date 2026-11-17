
@extends('layouts.app')

@section('title', 'استعادة كلمة المرور - DAKEN')

@section('content')
    @php
        $ar = app()->getLocale() === 'ar';
    @endphp

    <section class="container"
             style="max-width: 480px; padding-block: 80px">

        <h1>
            {{ $ar ? 'استعادة كلمة المرور' : 'Reset Password' }}
        </h1>

        <p>
            {{ $ar
                ? 'أدخل بريدك الإلكتروني الوظيفي لطلب رابط إعادة تعيين كلمة المرور.'
                : 'Enter your staff email address to request a password reset link.' }}
        </p>

        @if (session('status'))
            <div role="status">
                {{ __((string) session('status')) }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">
                    {{ $ar ? 'البريد الإلكتروني' : 'Email' }}
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                >
            </div>

            <x-buttons.primary type="submit">
                {{ $ar ? 'إرسال رابط الاستعادة' : 'Send Reset Link' }}
            </x-buttons.primary>
        </form>

        <p style="margin-top: 20px;">
            <a href="{{ route('login') }}">
                {{ $ar ? 'العودة إلى دخول الموظفين' : 'Back to Staff Login' }}
            </a>
        </p>
    </section>
@endsection
