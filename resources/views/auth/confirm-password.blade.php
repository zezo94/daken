
@php
    $isArabic = app()->getLocale() === 'ar';

    $t = static fn (string $ar, string $en): string =>
        $isArabic ? $ar : $en;
@endphp

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ $t('تأكيد كلمة المرور', 'Confirm Password') }}
        | DAKEN Chocolate
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="daken-admin-body daken-admin-light">

<main class="container mx-auto max-w-xl px-4 py-12">

    <header class="admin-page-header">
        <div class="admin-page-header__text">
            <h1>
                {{ $t('تأكيد كلمة المرور', 'Confirm Password') }}
            </h1>

            <p>
                {{ $t(
                    'للمتابعة، يرجى تأكيد كلمة مرور حسابك الوظيفي.',
                    'Please confirm your staff account password to continue.'
                ) }}
            </p>
        </div>
    </header>

    <section class="admin-ui-card">

        <div class="admin-ui-card__header">
            <h2>
                {{ $t('التحقق الأمني', 'Security Verification') }}
            </h2>
        </div>

        <div class="admin-ui-card__body">

            @if ($errors->any())
                <div class="admin-flash-container" role="alert">
                    <div class="admin-flash admin-flash--error">
                        <div class="admin-flash__content">
                            {{ $errors->first() }}
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('password.confirm.store') }}">

                @csrf

                <div class="admin-form-field">
                    <label for="password">
                        {{ $t('كلمة المرور الحالية', 'Current Password') }}
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="admin-form-control"
                        required
                        autofocus
                        autocomplete="current-password"
                    >

                    @error('password')
                    <span class="admin-field-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="admin-form-actions">
                    <button
                        type="submit"
                        class="admin-ui-button admin-ui-button--primary"
                    >
                        {{ $t('تأكيد والمتابعة', 'Confirm and Continue') }}
                    </button>

                    <a
                        href="{{ route('profile.show') }}"
                        class="admin-ui-button admin-ui-button--secondary"
                    >
                        {{ $t('العودة إلى الملف الشخصي', 'Back to Profile') }}
                    </a>
                </div>

            </form>

        </div>
    </section>

</main>

</body>
</html>
