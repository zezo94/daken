@php
    $isArabic = app()->getLocale() === 'ar';
    $user = auth()->user();
    $t = static fn (string $ar, string $en): string => $isArabic ? $ar : $en;

    $profileErrors = $errors->getBag('updateProfileInformation');
    $passwordErrors = $errors->getBag('updatePassword');
    $defaultErrors = $errors->getBag('default');

    $twoFactorPending = filled($user->two_factor_secret) && blank($user->two_factor_confirmed_at);
    $twoFactorConfirmed = filled($user->two_factor_secret) && filled($user->two_factor_confirmed_at);

    $statusMessages = [
        'profile-information-updated' => $t('تم تحديث معلومات الحساب بنجاح.', 'Account information updated successfully.'),
        'password-updated' => $t('تم تحديث كلمة المرور بنجاح.', 'Password updated successfully.'),
        'two-factor-authentication-enabled' => $t('بدأ إعداد المصادقة الثنائية. امسح رمز QR ثم أكد الرمز.', 'Two-factor setup started. Scan the QR code and confirm your code.'),
        'two-factor-authentication-confirmed' => $t('تم تأكيد المصادقة الثنائية بنجاح.', 'Two-factor authentication confirmed successfully.'),
        'two-factor-authentication-disabled' => $t('تم تعطيل المصادقة الثنائية.', 'Two-factor authentication disabled.'),
        'recovery-codes-generated' => $t('تم إنشاء رموز استرداد جديدة. اعرضها واحفظها الآن.', 'New recovery codes generated. Display and save them now.'),
    ];
    $status = session('status');
@endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="same-origin">
    <title>{{ $t('الملف الشخصي', 'My Profile') }} | DAKEN Chocolate</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="daken-admin-body daken-admin-light">
<main class="container mx-auto max-w-3xl px-4 py-10">
    <header class="admin-page-header">
        <div class="admin-page-header__text">
            <h1>{{ $t('الملف الشخصي', 'My Profile') }}</h1>
            <p>{{ $t('إدارة بيانات حسابك الوظيفي بأمان.', 'Manage your staff account securely.') }}</p>
        </div>
        <div class="admin-page-header__actions">
            <a href="{{ route('admin.dashboard') }}" class="admin-ui-button admin-ui-button--secondary">
                {{ $t('العودة إلى لوحة الإدارة', 'Back to Dashboard') }}
            </a>
        </div>
    </header>

    @if ($status)
        <div class="admin-flash-container" role="status">
            <div class="admin-flash admin-flash--success">
                <div class="admin-flash__content">{{ $statusMessages[$status] ?? $status }}</div>
            </div>
        </div>
    @endif

    @if ($defaultErrors->any())
        <div class="admin-flash-container" role="alert">
            <div class="admin-flash admin-flash--error">
                <div class="admin-flash__content">
                    <ul class="list-disc ps-5">
                        @foreach ($defaultErrors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <section class="admin-ui-card mb-6" aria-labelledby="account-heading">
        <div class="admin-ui-card__header">
            <h2 id="account-heading">{{ $t('معلومات الحساب', 'Account Information') }}</h2>
        </div>
        <div class="admin-ui-card__body">
            @if ($profileErrors->any())
                <div class="admin-flash-container" role="alert">
                    <div class="admin-flash admin-flash--error">
                        <div class="admin-flash__content">{{ $t('يرجى تصحيح أخطاء معلومات الحساب.', 'Please correct the account information errors.') }}</div>
                    </div>
                </div>
            @endif
            <form method="POST" action="{{ route('user-profile-information.update') }}">
                @csrf
                @method('PUT')
                <div class="admin-form-field">
                    <label for="name">{{ $t('الاسم الكامل', 'Full Name') }}</label>
                    <input id="name" type="text" class="admin-form-control" value="{{ $user->name }}" disabled>
                    <small>{{ $t('لا يمكن تعديل الاسم من هذه الصفحة.', 'Your name cannot be changed here.') }}</small>
                </div>
                <div class="admin-form-field">
                    <label for="email">{{ $t('البريد الإلكتروني', 'Email Address') }}</label>
                    <input id="email" name="email" type="email" class="admin-form-control {{ $profileErrors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email', $user->email) }}" autocomplete="email" required
                           aria-invalid="{{ $profileErrors->has('email') ? 'true' : 'false' }}">
                    @error('email', 'updateProfileInformation')
                    <span class="admin-field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="admin-form-field">
                    <label for="email_current_password">{{ $t('كلمة المرور الحالية', 'Current Password') }}</label>
                    <input id="email_current_password" name="current_password" type="password"
                           class="admin-form-control {{ $profileErrors->has('current_password') ? 'is-invalid' : '' }}"
                           autocomplete="current-password" required
                           aria-invalid="{{ $profileErrors->has('current_password') ? 'true' : 'false' }}">
                    <div>
                        <button type="button" class="admin-ui-button admin-ui-button--secondary" data-password-toggle="email_current_password" aria-controls="email_current_password" aria-pressed="false">
                            {{ $t('إظهار كلمة المرور', 'Show Password') }}
                        </button>
                    </div>
                    @error('current_password', 'updateProfileInformation')
                    <span class="admin-field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="admin-form-actions">
                    <button type="submit" class="admin-ui-button admin-ui-button--primary">{{ $t('حفظ البريد الإلكتروني', 'Save Email') }}</button>
                </div>
            </form>
        </div>
    </section>

    <section class="admin-ui-card mb-6" aria-labelledby="password-heading">
        <div class="admin-ui-card__header">
            <h2 id="password-heading">{{ $t('تغيير كلمة المرور', 'Change Password') }}</h2>
        </div>
        <div class="admin-ui-card__body">
            @if ($passwordErrors->any())
                <div class="admin-flash-container" role="alert">
                    <div class="admin-flash admin-flash--error">
                        <div class="admin-flash__content">{{ $t('يرجى تصحيح أخطاء كلمة المرور.', 'Please correct the password errors.') }}</div>
                    </div>
                </div>
            @endif
            <form method="POST" action="{{ route('user-password.update') }}">
                @csrf
                @method('PUT')
                <div class="admin-form-field">
                    <label for="password_current">{{ $t('كلمة المرور الحالية', 'Current Password') }}</label>
                    <input id="password_current" name="current_password" type="password"
                           class="admin-form-control {{ $passwordErrors->has('current_password') ? 'is-invalid' : '' }}"
                           autocomplete="current-password" required>
                    <div><button type="button" class="admin-ui-button admin-ui-button--secondary" data-password-toggle="password_current" aria-controls="password_current" aria-pressed="false">{{ $t('إظهار كلمة المرور', 'Show Password') }}</button></div>
                    @error('current_password', 'updatePassword')
                    <span class="admin-field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="admin-form-field">
                    <label for="password">{{ $t('كلمة المرور الجديدة', 'New Password') }}</label>
                    <input id="password" name="password" type="password"
                           class="admin-form-control {{ $passwordErrors->has('password') ? 'is-invalid' : '' }}"
                           autocomplete="new-password" minlength="12" required>
                    <small>{{ $t('يجب أن تتكون كلمة المرور من 12 حرفًا على الأقل.', 'Use at least 12 characters.') }}</small>
                    <div><button type="button" class="admin-ui-button admin-ui-button--secondary" data-password-toggle="password" aria-controls="password" aria-pressed="false">{{ $t('إظهار كلمة المرور', 'Show Password') }}</button></div>
                    @error('password', 'updatePassword')
                    <span class="admin-field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="admin-form-field">
                    <label for="password_confirmation">{{ $t('تأكيد كلمة المرور الجديدة', 'Confirm New Password') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="admin-form-control"
                           autocomplete="new-password" minlength="12" required>
                    <div><button type="button" class="admin-ui-button admin-ui-button--secondary" data-password-toggle="password_confirmation" aria-controls="password_confirmation" aria-pressed="false">{{ $t('إظهار كلمة المرور', 'Show Password') }}</button></div>
                    @error('password_confirmation', 'updatePassword')
                    <span class="admin-field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="admin-form-actions">
                    <button type="submit" class="admin-ui-button admin-ui-button--primary">{{ $t('تحديث كلمة المرور', 'Update Password') }}</button>
                </div>
            </form>
        </div>
    </section>

    <section class="admin-ui-card mb-6" aria-labelledby="two-factor-heading">
        <div class="admin-ui-card__header">
            <h2 id="two-factor-heading">{{ $t('المصادقة الثنائية', 'Two-Factor Authentication') }}</h2>
        </div>
        <div class="admin-ui-card__body">
            <p class="mb-5">{{ $t('استخدم تطبيق مصادقة لحماية حسابك الوظيفي.', 'Use an authenticator app to protect your staff account.') }}</p>

            @if (! $twoFactorPending && ! $twoFactorConfirmed)
                <p class="mb-4">{{ $t('المصادقة الثنائية غير مفعّلة.', 'Two-factor authentication is disabled.') }}</p>
                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <button type="submit" class="admin-ui-button admin-ui-button--primary">{{ $t('تفعيل المصادقة الثنائية', 'Enable Two-Factor Authentication') }}</button>
                </form>
            @elseif ($twoFactorPending)
                <p class="mb-4">{{ $t('الإعداد غير مكتمل. امسح رمز QR باستخدام تطبيق المصادقة ثم أدخل رمزًا من ستة أرقام.', 'Setup is pending. Scan the QR code using your authenticator app, then enter a six-digit code.') }}</p>
                <div id="two-factor-qr" class="mb-5" data-url="{{ route('two-factor.qr-code') }}" role="status" aria-live="polite">
                    {{ $t('جارٍ تحميل رمز QR...', 'Loading QR code...') }}
                </div>
                <form method="POST" action="{{ route('two-factor.confirm') }}">
                    @csrf
                    <div class="admin-form-field">
                        <label for="two_factor_code">{{ $t('رمز تطبيق المصادقة', 'Authenticator Code') }}</label>
                        <input id="two_factor_code" name="code" type="text" class="admin-form-control {{ $defaultErrors->has('code') ? 'is-invalid' : '' }}"
                               inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" dir="ltr" required>
                        @error('code')
                        <span class="admin-field-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="admin-form-actions">
                        <button type="submit" class="admin-ui-button admin-ui-button--primary">{{ $t('تأكيد التفعيل', 'Confirm Activation') }}</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-5">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-ui-button admin-ui-button--secondary">{{ $t('إلغاء الإعداد', 'Cancel Setup') }}</button>
                </form>
            @else
                <p class="mb-4">{{ $t('المصادقة الثنائية مفعّلة ومؤكدة.', 'Two-factor authentication is enabled and confirmed.') }}</p>
                <button type="button" id="show-recovery-codes" class="admin-ui-button admin-ui-button--secondary"
                        data-url="{{ route('two-factor.recovery-codes') }}" aria-controls="recovery-codes-container" aria-expanded="false">
                    {{ $t('عرض رموز الاسترداد', 'Show Recovery Codes') }}
                </button>
                <div id="recovery-codes-container" class="mt-5 hidden" aria-live="polite"></div>
                <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}" class="mt-5" data-confirm="{{ $t('سيتم إلغاء رموز الاسترداد القديمة. هل تريد المتابعة؟', 'Existing recovery codes will become invalid. Continue?') }}">
                    @csrf
                    <button type="submit" class="admin-ui-button admin-ui-button--secondary">{{ $t('تجديد رموز الاسترداد', 'Regenerate Recovery Codes') }}</button>
                </form>
                <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-5" data-confirm="{{ $t('هل تريد تعطيل المصادقة الثنائية لحسابك؟', 'Disable two-factor authentication for your account?') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-ui-button admin-ui-button--danger">{{ $t('تعطيل المصادقة الثنائية', 'Disable Two-Factor Authentication') }}</button>
                </form>
            @endif
            <p class="mt-5 text-sm">{{ $t('قد يُطلب منك تأكيد كلمة المرور قبل تنفيذ عمليات الأمان.', 'You may be asked to confirm your password before security operations.') }}</p>
        </div>
    </section>

    <section class="admin-ui-card mb-6" aria-labelledby="logout-heading">
        <div class="admin-ui-card__header">
            <h2 id="logout-heading">{{ $t('تسجيل الخروج', 'Sign Out') }}</h2>
        </div>
        <div class="admin-ui-card__body">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="admin-ui-button admin-ui-button--danger">{{ $t('تسجيل الخروج', 'Logout') }}</button>
            </form>
        </div>
    </section>
</main>

<script>
    (() => {
        const showPassword = @json($t('إظهار كلمة المرور', 'Show Password'));
        const hidePassword = @json($t('إخفاء كلمة المرور', 'Hide Password'));
        const qrError = @json($t('تعذر تحميل رمز QR. أكد كلمة المرور إذا طُلب منك ذلك، ثم أعد فتح الصفحة.', 'Unable to load the QR code. Confirm your password if prompted, then reopen this page.'));
        const recoveryError = @json($t('تعذر عرض رموز الاسترداد. قد تحتاج إلى تأكيد كلمة المرور.', 'Unable to display recovery codes. You may need to confirm your password.'));
        const hideCodes = @json($t('إخفاء رموز الاسترداد', 'Hide Recovery Codes'));
        const showCodes = @json($t('عرض رموز الاسترداد', 'Show Recovery Codes'));

        document.querySelectorAll('[data-password-toggle]').forEach(button => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                if (!input) return;
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                button.textContent = visible ? showPassword : hidePassword;
                button.setAttribute('aria-pressed', visible ? 'false' : 'true');
            });
        });

        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', event => {
                if (!window.confirm(form.dataset.confirm)) event.preventDefault();
            });
        });

        const qrContainer = document.getElementById('two-factor-qr');
        if (qrContainer) {
            fetch(qrContainer.dataset.url, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            })
                .then(response => {
                    if (!response.ok || response.redirected) throw new Error('QR request failed');
                    return response.json();
                })
                .then(data => {
                    if (typeof data.svg !== 'string' || !data.svg.trim().startsWith('<svg')) {
                        throw new Error('QR SVG unavailable');
                    }
                    const img = document.createElement('img');
                    img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(data.svg);
                    img.alt = @json($t('رمز QR لإعداد تطبيق المصادقة', 'QR code for authenticator setup'));
                    img.className = 'max-w-full h-auto';
                    qrContainer.replaceChildren(img);
                })
                .catch(() => { qrContainer.textContent = qrError; });
        }

        const recoveryButton = document.getElementById('show-recovery-codes');
        const recoveryContainer = document.getElementById('recovery-codes-container');
        if (recoveryButton && recoveryContainer) {
            recoveryButton.addEventListener('click', async () => {
                if (!recoveryContainer.classList.contains('hidden')) {
                    recoveryContainer.replaceChildren();
                    recoveryContainer.classList.add('hidden');
                    recoveryButton.textContent = showCodes;
                    recoveryButton.setAttribute('aria-expanded', 'false');
                    return;
                }
                recoveryButton.disabled = true;
                try {
                    const response = await fetch(recoveryButton.dataset.url, {
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!response.ok || response.redirected) throw new Error('Recovery request failed');
                    const codes = await response.json();
                    if (!Array.isArray(codes)) throw new Error('Invalid recovery codes');
                    const list = document.createElement('ul');
                    list.className = 'list-disc ps-5';
                    for (const code of codes) {
                        const item = document.createElement('li');
                        item.textContent = String(code);
                        item.dir = 'ltr';
                        list.appendChild(item);
                    }
                    recoveryContainer.replaceChildren(list);
                    recoveryButton.textContent = hideCodes;
                } catch {
                    recoveryContainer.textContent = recoveryError;
                    recoveryButton.textContent = hideCodes;
                } finally {
                    recoveryContainer.classList.remove('hidden');
                    recoveryButton.setAttribute('aria-expanded', 'true');
                    recoveryButton.disabled = false;
                }
            });
        }
    })();
</script>
</body>
</html>
