
@php
    $siteSettings = app(\App\Services\SiteSettings::class);

    $brandName = $siteSettings->get(
        'brand_name',
        config('brand.name', 'DAKEN')
    );

    $logo = $siteSettings->get('logo');

    $ar = app()->getLocale() === 'ar';

    /*
    |--------------------------------------------------------------------------
    | Main navigation
    |--------------------------------------------------------------------------
    */

    $menuItems = [
        [
            'route' => 'home',
            'ar' => 'الرئيسية',
            'en' => 'Home',
            'main' => true,
        ],
        [
            'route' => 'products.index',
            'ar' => 'المنتجات',
            'en' => 'Products',
            'main' => true,
        ],
        [
            'route' => 'gallery.index',
            'ar' => 'معرض الصور',
            'en' => 'Gallery',
            'main' => true,
        ],
        [
            'route' => 'locations.index',
            'ar' => 'أماكن البيع',
            'en' => 'Where to Buy',
            'main' => false,
        ],
        [
            'route' => 'about',
            'ar' => 'من نحن',
            'en' => 'About Us',
            'main' => false,
        ],
        [
            'route' => 'vision',
            'ar' => 'رؤيتنا',
            'en' => 'Our Vision',
            'main' => false,
        ],
        [
            'route' => 'contact.index',
            'ar' => 'تواصل معنا',
            'en' => 'Contact Us',
            'main' => false,
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Remove unavailable routes
    |--------------------------------------------------------------------------
    */

    $menuItems = array_values(array_filter(
        $menuItems,
        fn ($item) => \Illuminate\Support\Facades\Route::has(
            $item['route']
        )
    ));

    /*
    |--------------------------------------------------------------------------
    | Published dynamic pages
    |--------------------------------------------------------------------------
    */

    if (\Illuminate\Support\Facades\Route::has('pages.show')) {
        $dynamicPages = \App\Models\SitePage::query()
            ->where('is_active', true)
            ->whereNotIn('key', ['about', 'vision'])
            ->orderBy('id')
            ->get();

        foreach ($dynamicPages as $page) {
            $titles = is_array($page->title)
                ? $page->title
                : [];

            $menuItems[] = [
                'route' => 'pages.show',
                'route_parameters' => [
                    'key' => $page->key,
                ],
                'setting_key' => 'page:' . $page->key,
                'ar' => $titles['ar'] ?? $page->key,
                'en' => $titles['en'] ?? $page->key,
                'main' => false,
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize menu items
    |--------------------------------------------------------------------------
    */

    foreach ($menuItems as &$item) {
        $item['setting_key'] ??= $item['route'];
        $item['route_parameters'] ??= [];
    }

    unset($item);

    /*
    |--------------------------------------------------------------------------
    | Database settings
    |--------------------------------------------------------------------------
    */

    $navSettings = \App\Models\NavigationSetting::query()
        ->get()
        ->keyBy('key');

    /*
    |--------------------------------------------------------------------------
    | Logo sizes
    |--------------------------------------------------------------------------
    */

    $logoDesktop = max(
        70,
        min(
            250,
            (int) (
                $navSettings
                    ->get('logo_desktop_width')
                    ?->sort_order ?? 125
            )
        )
    );

    $logoMobile = max(
        60,
        min(
            180,
            (int) (
                $navSettings
                    ->get('logo_mobile_width')
                    ?->sort_order ?? 105
            )
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Apply navigation settings
    |--------------------------------------------------------------------------
    */

    foreach ($menuItems as &$item) {
        $setting = $navSettings->get(
            $item['setting_key']
        );

        $item['hidden'] = false;
        $item['sort_order'] = 999;

        if ($setting) {
            $item['main'] = $setting->placement === 'main';

            $item['hidden'] = $setting->placement === 'hidden';

            $item['sort_order'] = $setting->sort_order;
        }
    }

    unset($item);

    /*
    |--------------------------------------------------------------------------
    | Filter hidden links
    |--------------------------------------------------------------------------
    */

    $menuItems = array_values(array_filter(
        $menuItems,
        fn ($item) => !$item['hidden']
    ));

    /*
    |--------------------------------------------------------------------------
    | Sort links
    |--------------------------------------------------------------------------
    */

    usort(
        $menuItems,
        fn ($a, $b) =>
            $a['sort_order'] <=> $b['sort_order']
    );

    /*
    |--------------------------------------------------------------------------
    | Main and dropdown links
    |--------------------------------------------------------------------------
    */

    $mainItems = array_values(array_filter(
        $menuItems,
        fn ($item) => $item['main']
    ));

    $moreItems = array_values(array_filter(
        $menuItems,
        fn ($item) => !$item['main']
    ));
@endphp

<header
    class="site-header daken-nav53"
    style="--daken-logo-desktop: {{ $logoDesktop }}px; --daken-logo-mobile: {{ $logoMobile }}px;"
>
    <div class="container daken-nav53__inner">

        {{-- Logo --}}

        <a
            href="{{ route('home') }}"
            class="site-logo daken-nav53__logo"
            aria-label="{{ $brandName }}"
            style="--daken-logo-desktop: {{ $logoDesktop }}px; --daken-logo-mobile: {{ $logoMobile }}px;"
        >
            @if($logo)
                <img
                    src="{{ asset('storage/' . $logo) }}"
                    alt="{{ $brandName }}"
                    class="site-logo__image daken-logo-controlled"
                >
            @else
                <span class="site-logo__text">
            {{ $brandName }}
        </span>
            @endif
        </a>

        {{-- Desktop navigation --}}
        <nav
            class="daken-nav53__links"
            aria-label="{{ $ar ? 'القائمة الرئيسية' : 'Main navigation' }}"
        >

            @foreach($mainItems as $item)
                @php
                    $isCurrent = request()->routeIs($item['route'])
                        && (
                            $item['route'] !== 'pages.show'
                            || request()->route('key') === (
                                $item['route_parameters']['key'] ?? null
                            )
                        );
                @endphp

                <a
                    href="{{ route($item['route'], $item['route_parameters']) }}"
                    @if($isCurrent)
                        aria-current="page"
                    @endif
                >
                    {{ $ar ? $item['ar'] : $item['en'] }}
                </a>
            @endforeach

            {{-- More dropdown --}}
            @if(count($moreItems))
                <details class="daken-nav53__dropdown">

                    <summary>
                        {{ $ar ? 'المزيد' : 'More' }}
                        <span aria-hidden="true">⌄</span>
                    </summary>

                    <div class="daken-nav53__dropdown-menu">

                        @foreach($moreItems as $item)
                            @php
                                $isCurrent = request()->routeIs($item['route'])
                                    && (
                                        $item['route'] !== 'pages.show'
                                        || request()->route('key') === (
                                            $item['route_parameters']['key'] ?? null
                                        )
                                    );
                            @endphp

                            <a
                                href="{{ route($item['route'], $item['route_parameters']) }}"
                                @if($isCurrent)
                                    aria-current="page"
                                @endif
                            >
                                {{ $ar ? $item['ar'] : $item['en'] }}
                            </a>
                        @endforeach

                    </div>
                </details>
            @endif

        </nav>

        {{-- Controls --}}
        <div class="daken-nav53__controls">

            {{-- Language --}}
            <form
                action="{{ route('language.switch') }}"
                method="POST"
                class="language-switcher"
            >
                @csrf

                <label
                    class="daken-nav53__sr-only"
                    for="site-language"
                >
                    {{ $ar ? 'اختر اللغة' : 'Select language' }}
                </label>

                <select
                    id="site-language"
                    name="locale"
                    onchange="this.form.submit()"
                >
                    <option value="ar" @selected($ar)>
                        العربية
                    </option>

                    <option value="en" @selected(!$ar)>
                        English
                    </option>
                </select>
            </form>

            {{-- Account controls --}}
            <div class="daken-nav53__account">

                @auth
                    <details class="daken-nav53__dropdown daken-account-menu">
                        <summary>
                            {{ $ar ? 'حسابي' : 'My Account' }}
                            <span aria-hidden="true">⌄</span>
                        </summary>

                        <div class="daken-nav53__dropdown-menu">

                            <a href="{{ route('profile.show') }}"
                               @if(request()->routeIs('profile.show'))
                                   aria-current="page"
                                @endif>
                                {{ $ar ? 'الملف الشخصي' : 'My Profile' }}
                            </a>

                            <form method="POST"
                                  action="{{ route('logout') }}">
                                @csrf

                                <button type="submit"
                                        class="daken-account-logout">
                                    {{ $ar ? 'تسجيل الخروج' : 'Logout' }}
                                </button>
                            </form>

                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}"
                       class="daken-account-login">
                        {{ $ar ? 'تسجيل الدخول' : 'Login' }}
                    </a>
                @endauth

            </div>

            {{-- Mobile menu --}}
            <details class="daken-nav53__mobile">

                <summary
                    aria-label="{{ $ar ? 'فتح القائمة' : 'Open menu' }}"
                >
                    ☰
                </summary>

                <nav
                    aria-label="{{ $ar ? 'قائمة الهاتف' : 'Mobile navigation' }}"
                >

                    @foreach($menuItems as $item)
                        @php
                            $isCurrent = request()->routeIs($item['route'])
                                && (
                                    $item['route'] !== 'pages.show'
                                    || request()->route('key') === (
                                        $item['route_parameters']['key'] ?? null
                                    )
                                );
                        @endphp

                        <a
                            href="{{ route($item['route'], $item['route_parameters']) }}"
                            @if($isCurrent)
                                aria-current="page"
                            @endif
                        >
                            {{ $ar ? $item['ar'] : $item['en'] }}
                        </a>
                    @endforeach

                        {{-- Mobile account links --}}
                        @auth
                            <a href="{{ route('profile.show') }}"
                               @if(request()->routeIs('profile.show'))
                                   aria-current="page"
                                @endif>
                                {{ $ar ? 'الملف الشخصي' : 'My Profile' }}
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit" class="daken-account-logout">
                                    {{ $ar ? 'تسجيل الخروج' : 'Logout' }}
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}">
                                {{ $ar ? 'تسجيل الدخول' : 'Login' }}
                            </a>
                        @endauth

                </nav>
            </details>

        </div>
    </div>
</header>
