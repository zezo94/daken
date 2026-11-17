
@extends('layouts.app')

@section('title', 'دخول الموظفين - DAKEN')

@section('content')
    @php
        $ar = app()->getLocale() === 'ar';
    @endphp

    <section class="container"
             style="max-width: 480px; padding-block: 80px">

        <h1>

            {{ $ar ? 'دخول الموظفين' : 'Staff Login' }}
        </h1>

        @if (
    session()->has('login.id') &&
    \Illuminate\Support\Facades\Route::has('two-factor.login')
)
            <p>
                <a href="{{ route('two-factor.login') }}">
                    {{ $ar
                        ? 'لديك محاولة تسجيل دخول قيد التحقق. متابعة التحقق بخطوتين'
                        : 'You have a pending sign-in. Continue two-factor verification'
                    }}
                </a>
            </p>
        @endif


        @if (session('status'))
            <div role="status" style="margin-block: 16px;">
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

        <form method="POST" action="{{ route('login') }}">
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
                    autocomplete="username"
                >
            </div>


            <div class="form-group">
                <label for="password">
                    {{ $ar ? 'كلمة المرور' : 'Password' }}
                </label>

                <div style="position: relative;">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        style="padding-inline-end: 110px; width: 100%;"
                    >

                    <button
                        type="button"
                        id="toggle-password"
                        aria-controls="password"
                        aria-pressed="false"
                        style="position: absolute; inset-inline-end: 8px; top: 50%; transform: translateY(-50%);"
                    >
                        {{ $ar ? 'إظهار' : 'Show' }}
                    </button>
                </div>
            </div>



            <label for="remember">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    value="1"
                    @checked(old('remember'))
                >
                {{ $ar ? 'تذكرني على هذا الجهاز' : 'Remember me on this device' }}
            </label>


            <x-buttons.primary type="submit">
                {{ $ar ? 'دخول لوحة الإدارة' : 'Sign in to Admin Panel' }}
            </x-buttons.primary>



            @if (\Illuminate\Support\Facades\Route::has('password.request'))
                <p style="margin-top: 16px;">
                    <a href="{{ route('password.request') }}">
                        {{ $ar ? 'نسيت كلمة المرور؟' : 'Forgot your password?' }}
                    </a>
                </p>
            @endif


        </form>


        <script>
            (() => {
                const toggle = document.getElementById('toggle-password');
                const password = document.getElementById('password');

                if (!toggle || !password) {
                    return;
                }

                const showLabel = @json($ar ? 'إظهار' : 'Show');
                const hideLabel = @json($ar ? 'إخفاء' : 'Hide');

                toggle.addEventListener('click', () => {
                    const isVisible = password.type === 'text';

                    password.type = isVisible ? 'password' : 'text';

                    toggle.textContent = isVisible
                        ? showLabel
                        : hideLabel;

                    toggle.setAttribute(
                        'aria-pressed',
                        isVisible ? 'false' : 'true'
                    );
                });
            })();
        </script>
    </section>
@endsection
