
@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

<header class="daken-admin-topbar">

    <div class="daken-admin-topbar__intro">

        <button
            type="button"
            id="admin-menu-toggle"
            class="daken-admin-menu-toggle"
            aria-controls="admin-sidebar"
            aria-expanded="false"
            aria-label="{{ $isArabic ? 'فتح القائمة' : 'Open menu' }}"
        >
            <span aria-hidden="true">☰</span>
        </button>

        <div>
            <span class="daken-admin-topbar__eyebrow">
                DAKEN CHOCOLATE
            </span>

            <strong>
                {{ $isArabic
                    ? 'إدارة الموقع'
                    : 'Website Management' }}
            </strong>
        </div>
    </div>

    <div class="daken-admin-topbar__actions">

        <form
            action="{{ route('language.switch') }}"
            method="POST"
        >
            @csrf

            <label for="admin-language" class="sr-only">
                {{ $isArabic ? 'اللغة' : 'Language' }}
            </label>

            <select
                id="admin-language"
                name="locale"
                onchange="this.form.submit()"
            >
                <option value="ar" @selected($isArabic)>
                    العربية
                </option>

                <option value="en" @selected(!$isArabic)>
                    English
                </option>
            </select>
        </form>

        @auth
            <div class="daken-admin-topbar__user">

                <span class="daken-admin-topbar__avatar">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </span>

                <span class="daken-admin-topbar__username">
                    {{ auth()->user()->name }}
                </span>

            </div>
        @endauth

    </div>
</header>
