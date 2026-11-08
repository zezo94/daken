
<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>@yield('title', 'DAKEN Admin')</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')
</head>

<body class="daken-admin-body daken-admin-light">

<a
    href="#admin-main-content"
    class="daken-admin-skip-link"
>
    {{ app()->getLocale() === 'ar'
        ? 'الانتقال إلى المحتوى الرئيسي'
        : 'Skip to main content' }}
</a>

<div class="daken-admin-shell">

    {{-- Mobile sidebar backdrop --}}
    <button
        type="button"
        id="admin-sidebar-backdrop"
        class="daken-admin-sidebar-backdrop"
        aria-label="{{ app()->getLocale() === 'ar'
                ? 'إغلاق القائمة'
                : 'Close menu' }}"
        hidden
    ></button>

    {{-- Sidebar --}}
    @include('admin.partials.sidebar')

    <div class="daken-admin-main">

        {{-- Topbar --}}
        @include('admin.partials.topbar')

        {{-- Main content --}}
        <main
            id="admin-main-content"
            class="daken-admin-content"
            tabindex="-1"
        >

            {{-- Flash messages --}}
            <x-admin.flash />

            {{-- Validation errors --}}
            @if ($errors->any())
                <div
                    class="daken-admin-alert daken-admin-alert--error"
                    role="alert"
                >
                    <strong>
                        {{ app()->getLocale() === 'ar'
                            ? 'يرجى مراجعة البيانات المدخلة:'
                            : 'Please review the submitted information:' }}
                    </strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </main>

        {{-- Footer --}}
        <footer class="daken-admin-footer">
            &copy; {{ date('Y') }} DAKEN.

            {{ app()->getLocale() === 'ar'
                ? 'لوحة إدارة الموقع'
                : 'Website Administration' }}
        </footer>

    </div>
</div>

{{-- Confirmation dialog --}}
<x-admin.confirm-dialog />

@stack('scripts')

</body>
</html>
