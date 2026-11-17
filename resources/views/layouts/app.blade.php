
<!DOCTYPE html>
<html
    lang="{{ app()->getLocale() }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = trim(
            $__env->yieldContent(
                'title',
                config('brand.full_name')
            )
        );

        $metaDescription = trim(
            $__env->yieldContent('meta_description')
        );

        $canonicalUrl = trim(
            $__env->yieldContent('canonical')
        );

        $ogImage = trim(
            $__env->yieldContent('og_image')
        );
    @endphp

    <title>{{ $pageTitle }}</title>

    @if($metaDescription !== '')
        <meta
            name="description"
            content="{{ $metaDescription }}"
        >
    @endif

    @if($canonicalUrl !== '')
        <link
            rel="canonical"
            href="{{ $canonicalUrl }}"
        >
    @endif

    <meta
        property="og:title"
        content="{{ $pageTitle }}"
    >

    @if($metaDescription !== '')
        <meta
            property="og:description"
            content="{{ $metaDescription }}"
        >
    @endif

    @if($ogImage !== '')
        <meta
            property="og:image"
            content="{{ $ogImage }}"
        >
    @endif

    <meta
        property="og:type"
        content="product"
    >

    @php
        $faviconPath = app(\App\Services\SiteSettings::class)
            ->get('favicon');

        $faviconUrl = $faviconPath
            ? asset('storage/' . $faviconPath)
            : asset('favicon-default.png');

        $faviconVersion = $faviconPath
            ? md5($faviconPath)
            : 'default';

        $faviconUrl .= '?v=' . $faviconVersion;
    @endphp

    <link
        rel="icon"
        type="{{ $faviconPath && str_ends_with($faviconPath, '.webp') ? 'image/webp' : 'image/png' }}"
        href="{{ $faviconUrl }}"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="daken-frontend">
<x-navigation.header />

<main id="main-content">
    @yield('content')
</main>

<x-navigation.footer />
</body>
</html>
