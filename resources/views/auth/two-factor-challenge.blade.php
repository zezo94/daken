
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

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        {{ $t('التحقق بخطوتين', 'Two-Factor Authentication') }}
        | DAKEN Chocolate
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="daken-admin-body daken-admin-light">

<main class="container mx-auto max-w-xl px-4 py-12">

    <header class="admin-page-header">
        <div class="admin-page-header__text">

            <h1>
                {{ $t(
                    'التحقق بخطوتين',
                    'Two-Factor Authentication'
                ) }}
            </h1>

            <p>
                {{ $t(
                    'أكمل التحقق الأمني للوصول إلى لوحة إدارة DAKEN.',
                    'Complete security verification to access the DAKEN admin panel.'
                ) }}
            </p>

        </div>
    </header>

    <section class="admin-ui-card"
             aria-labelledby="two-factor-heading">

        <div class="admin-ui-card__header">

            <h2 id="two-factor-heading">
                {{ $t(
                    'رمز التحقق',
                    'Verification Code'
                ) }}
            </h2>

        </div>

        <div class="admin-ui-card__body">

            @if ($errors->any())

                <div class="admin-flash-container"
                     role="alert">

                    <div class="admin-flash admin-flash--error">

                        <div class="admin-flash__content">
                            {{ $errors->first() }}
                        </div>

                    </div>

                </div>

            @endif

            <p class="mb-6">
                {{ $t(
                    'أدخل الرمز المكوّن من 6 أرقام من تطبيق المصادقة، أو استخدم رمز استرداد.',
                    'Enter the 6-digit code from your authenticator app, or use a recovery code.'
                ) }}
            </p>

            <form
                method="POST"
                action="{{ route('two-factor.login.store') }}"
                id="two-factor-form"
            >
                @csrf

                <div
                    class="admin-form-field"
                    id="code-field"
                >

                    <label for="code">
                        {{ $t(
                            'رمز تطبيق المصادقة',
                            'Authenticator Code'
                        ) }}
                    </label>

                    <input
                        id="code"
                        name="code"
                        type="text"
                        class="admin-form-control"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        autocomplete="one-time-code"
                        dir="ltr"
                        required
                        autofocus
                        aria-invalid="{{ $errors->has('code') ? 'true' : 'false' }}"
                    >

                    @error('code')
                    <span class="admin-field-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                <div
                    class="admin-form-field hidden"
                    id="recovery-field"
                >

                    <label for="recovery_code">
                        {{ $t(
                            'رمز الاسترداد',
                            'Recovery Code'
                        ) }}
                    </label>

                    <input
                        id="recovery_code"
                        name="recovery_code"
                        type="text"
                        class="admin-form-control"
                        autocomplete="off"
                        dir="ltr"
                        disabled
                        aria-invalid="{{ $errors->has('recovery_code') ? 'true' : 'false' }}"
                    >

                    @error('recovery_code')
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
                        {{ $t(
                            'تأكيد الدخول',
                            'Verify and Sign In'
                        ) }}
                    </button>

                    <button
                        type="button"
                        id="toggle-recovery"
                        class="admin-ui-button admin-ui-button--secondary"
                        aria-controls="recovery-field"
                        aria-expanded="false"
                    >
                        {{ $t(
                            'استخدام رمز استرداد',
                            'Use a Recovery Code'
                        ) }}
                    </button>

                </div>

            </form>

            <form
                method="POST"
                action="{{ route('two-factor.cancel') }}"
                class="mt-6"
            >
                @csrf

                <button
                    type="submit"
                    class="admin-ui-button admin-ui-button--secondary"
                >
                    {{ $t(
                        'إلغاء التحقق والعودة إلى تسجيل الدخول',
                        'Cancel Verification and Return to Login'
                    ) }}
                </button>

            </form>

        </div>

    </section>

</main>

<script>
    (() => {
        const toggle = document.getElementById('toggle-recovery');
        const codeField = document.getElementById('code-field');
        const recoveryField = document.getElementById('recovery-field');
        const codeInput = document.getElementById('code');
        const recoveryInput = document.getElementById('recovery_code');

        if (
            !toggle ||
            !codeField ||
            !recoveryField ||
            !codeInput ||
            !recoveryInput
        ) {
            return;
        }

        let recoveryMode = false;

        toggle.addEventListener('click', () => {
            recoveryMode = !recoveryMode;

            codeField.classList.toggle(
                'hidden',
                recoveryMode
            );

            recoveryField.classList.toggle(
                'hidden',
                !recoveryMode
            );

            codeInput.disabled = recoveryMode;
            codeInput.required = !recoveryMode;

            recoveryInput.disabled = !recoveryMode;
            recoveryInput.required = recoveryMode;

            toggle.textContent = recoveryMode
                ? @json($t(
                'استخدام رمز التطبيق',
                'Use Authenticator Code'
            ))
                : @json($t(
                'استخدام رمز استرداد',
                'Use a Recovery Code'
            ));

            toggle.setAttribute(
                'aria-expanded',
                recoveryMode ? 'true' : 'false'
            );

            (
                recoveryMode
                    ? recoveryInput
                    : codeInput
            ).focus();
        });
    })();
</script>

</body>
</html>
