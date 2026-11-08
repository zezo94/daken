
<!DOCTYPE html>
<html
    lang="{{ app()->getLocale() }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
        >
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
@yield('title', config('brand.full_name'))
</title>


    @if(trim($__env->yieldContent('meta_description')) !== '')
        <meta
            name="description"
            content="@yield('meta_description')"
        >
    @endif

    @if(trim($__env->yieldContent('canonical')) !== '')
        <link
            rel="canonical"
            href="@yield('canonical')"
        >
    @endif

    <meta
        property="og:title"
        content="@yield('title', 'DAKEN Chocolate')"
    >

    @if(trim($__env->yieldContent('meta_description')) !== '')
        <meta
            property="og:description"
            content="@yield('meta_description')"
        >
    @endif

    @if(trim($__env->yieldContent('og_image')) !== '')
        <meta
            property="og:image"
            content="@yield('og_image')"
        >
    @endif

    <meta
        property="og:type"
        content="product"
    >


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
<x-navigation.header />

<main id="main-content">
    @yield('content')
</main>

<x-navigation.footer />
</body>
</html>
