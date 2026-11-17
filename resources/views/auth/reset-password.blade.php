
@extends('layouts.app')

@section('title', 'تعيين كلمة مرور جديدة - DAKEN')

@section('content')
    @php
        $ar = app()->getLocale() === 'ar';
    @endphp

    <section class="container"
             style="max-width: 480px; padding-block: 80px;">

        <h1>
            {{ $ar ? 'تعيين كلمة مرور جديدة' : 'Set New Password' }}
        </h1>

        <p>
            {{ $ar
                ? 'أدخل كلمة مرور جديدة لحسابك الوظيفي في DAKEN.'
                : 'Enter a new password for your DAKEN staff account.' }}
        </p>

        @if (session('status'))
            <div role="status" style="margin-block: 16px;">
                {{ __((string) session('status')) }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" style="margin-block: 16px;">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input
                type="hidden"
                name="token"
                value="{{ $request->route('token') }}"
            >

            <div class="form-group">
                <label for="email">
                    {{ $ar ? 'البريد الإلكتروني' : 'Email' }}
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="password">
                    {{ $ar ? 'كلمة المرور الجديدة' : 'New Password' }}
                </label>

                <div style="position: relative;">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        minlength="12"
                        required
                        autocomplete="new-password"
                        aria-describedby="password-help"
                        style="width: 100%; padding-inline-end: 110px;"
                    >

                    <button
                        type="button"
                        id="toggle-password"
                        aria-controls="password"
                        aria-pressed="false"
                        aria-label="{{ $ar ? 'إظهار أو إخفاء كلمة المرور' : 'Show or hide password' }}"
                        style="position: absolute; inset-inline-end: 8px; top: 50%; transform: translateY(-50%);"
                    >
                        {{ $ar ? 'إظهار' : 'Show' }}
                    </button>
                </div>

                <p id="password-help">
                    {{ $ar
                        ? 'يجب أن تتكون كلمة المرور من 12 حرفًا على الأقل.'
                        : 'Your password must contain at least 12 characters.' }}
                </p>
            </div>

            <div class="form-group">
                <label for="password_confirmation">
                    {{ $ar ? 'تأكيد كلمة المرور الجديدة' : 'Confirm New Password' }}
                </label>

                <div style="position: relative;">
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        minlength="12"
                        required
                        autocomplete="new-password"
                        style="width: 100%; padding-inline-end: 110px;"
                    >

                    <button
                        type="button"
                        id="toggle-password-confirmation"
                        aria-controls="password_confirmation"
                        aria-pressed="false"
                        aria-label="{{ $ar ? 'إظهار أو إخفاء تأكيد كلمة المرور' : 'Show or hide password confirmation' }}"
                        style="position: absolute; inset-inline-end: 8px; top: 50%; transform: translateY(-50%);"
                    >
                        {{ $ar ? 'إظهار' : 'Show' }}
                    </button>
                </div>
            </div>

            <x-buttons.primary type="submit">
                {{ $ar ? 'حفظ كلمة المرور الجديدة' : 'Save New Password' }}
            </x-buttons.primary>
        </form>

        <p style="margin-top: 20px;">
            <a href="{{ route('login') }}">
                {{ $ar ? 'العودة إلى دخول الموظفين' : 'Back to Staff Login' }}
            </a>
        </p>

    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const showLabel = @json($ar ? 'إظهار' : 'Show');
            const hideLabel = @json($ar ? 'إخفاء' : 'Hide');

            function setupPasswordToggle(inputId, buttonId) {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);

                if (!input || !button) {
                    return;
                }

                button.addEventListener('click', function () {
                    const isHidden = input.type === 'password';

                    input.type = isHidden ? 'text' : 'password';
                    button.textContent = isHidden ? hideLabel : showLabel;
                    button.setAttribute('aria-pressed', String(isHidden));
                });
            }

            setupPasswordToggle('password', 'toggle-password');
            setupPasswordToggle(
                'password_confirmation',
                'toggle-password-confirmation'
            );
        });
    </script>
@endsection
