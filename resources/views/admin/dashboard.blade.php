
@extends('layouts.admin')

@section('title', 'DAKEN Admin Dashboard')

@section('content')
    @php
        $ar = app()->getLocale() === 'ar';
        $user = auth()->user();

        $canProducts = $user->hasAdminPermission('products.manage');
        $canCategories = $user->hasAdminPermission('categories.manage');
        $canLocations = $user->hasAdminPermission('locations.manage');
        $canMessages = $user->hasAdminPermission('messages.manage');
        $canAnalytics = $user->hasAdminPermission('analytics.view');
        $canActivities = $user->hasAdminPermission('activities.view');

        /*
        |--------------------------------------------------------------------------
        | Dashboard KPI Cards
        |--------------------------------------------------------------------------
        */

        $cards = [
            [
                'label' => $ar ? 'إجمالي المنتجات' : 'Total Products',
                'value' => $stats['products'] ?? 0,
                'icon' => '🍫',
                'permission' => 'products.manage',
            ],
            [
                'label' => $ar ? 'المنتجات النشطة' : 'Active Products',
                'value' => $stats['active_products'] ?? 0,
                'icon' => '✓',
                'permission' => 'products.manage',
            ],
            [
                'label' => $ar ? 'المنتجات المميزة' : 'Featured Products',
                'value' => $stats['featured_products'] ?? 0,
                'icon' => '★',
                'permission' => 'products.manage',
            ],
            [
                'label' => $ar ? 'التصنيفات' : 'Categories',
                'value' => $stats['categories'] ?? 0,
                'icon' => '▦',
                'permission' => 'categories.manage',
            ],
            [
                'label' => $ar ? 'أماكن البيع' : 'Locations',
                'value' => $stats['locations'] ?? 0,
                'icon' => '⌖',
                'permission' => 'locations.manage',
            ],
            [
                'label' => $ar ? 'المواقع النشطة' : 'Active Locations',
                'value' => $stats['active_locations'] ?? 0,
                'icon' => '◉',
                'permission' => 'locations.manage',
            ],
            [
                'label' => $ar ? 'رسائل العملاء' : 'Messages',
                'value' => $stats['messages'] ?? 0,
                'icon' => '✉',
                'permission' => 'messages.manage',
            ],
            [
                'label' => $ar ? 'رسائل جديدة' : 'Unread Messages',
                'value' => $stats['unread_messages'] ?? 0,
                'icon' => '●',
                'permission' => 'messages.manage',
            ],
            [
                'label' => $ar ? 'إجمالي الزيارات' : 'Total Visits',
                'value' => $stats['total_visits'] ?? 0,
                'icon' => '◉',
                'permission' => 'analytics.view',
            ],
            [
                'label' => $ar ? 'الزوار الفريدون' : 'Unique Visitors',
                'value' => $stats['unique_visitors'] ?? 0,
                'icon' => '♙',
                'permission' => 'analytics.view',
            ],
            [
                'label' => $ar ? 'زيارات اليوم' : "Today's Visits",
                'value' => $stats['today_visits'] ?? 0,
                'icon' => '◷',
                'permission' => 'analytics.view',
            ],
            [
                'label' => $ar ? 'آخر 7 أيام' : 'Last 7 Days',
                'value' => $stats['week_visits'] ?? 0,
                'icon' => '▥',
                'permission' => 'analytics.view',
            ],
        ];

        $cards = array_values(array_filter(
            $cards,
            fn (array $card): bool =>
                $user->hasAdminPermission($card['permission'])
        ));

        /*
        |--------------------------------------------------------------------------
        | Quick Access Links
        |--------------------------------------------------------------------------
        */

        $quickLinks = [
            [
                'label' => $ar ? 'التحليلات' : 'Analytics',
                'route' => 'admin.analytics.index',
                'icon' => '◫',
                'permission' => 'analytics.view',
            ],
            [
                'label' => $ar ? 'المنتجات' : 'Products',
                'route' => 'admin.products.index',
                'icon' => '🍫',
                'permission' => 'products.manage',
            ],
            [
                'label' => $ar ? 'التصنيفات' : 'Categories',
                'route' => 'admin.categories.index',
                'icon' => '▦',
                'permission' => 'categories.manage',
            ],
            [
                'label' => $ar ? 'أماكن البيع' : 'Locations',
                'route' => 'admin.locations.index',
                'icon' => '⌖',
                'permission' => 'locations.manage',
            ],
            [
                'label' => $ar ? 'الرسائل' : 'Messages',
                'route' => 'admin.messages.index',
                'icon' => '✉',
                'permission' => 'messages.manage',
            ],
            [
                'label' => $ar ? 'الصفحات' : 'Pages',
                'route' => 'admin.pages.index',
                'icon' => '▤',
                'permission' => 'pages.manage',
            ],
            [
                'label' => $ar ? 'الصفحة الرئيسية' : 'Homepage',
                'route' => 'admin.homepage.edit',
                'icon' => '⌂',
                'permission' => 'homepage.manage',
            ],
            [
                'label' => $ar ? 'الإعدادات' : 'Settings',
                'route' => 'admin.settings.edit',
                'icon' => '⚙',
                'permission' => 'settings.manage',
            ],
            [
                'label' => $ar ? 'سجل النشاط' : 'Activity Log',
                'route' => 'admin.activities.index',
                'icon' => '◷',
                'permission' => 'activities.view',
            ],
            [
                'label' => $ar ? 'إدارة الموظفين' : 'Staff Management',
                'route' => 'admin.users.index',
                'icon' => '👥',
                'permission' => 'users.manage',
            ],
        ];

        $quickLinks = array_values(array_filter(
            $quickLinks,
            fn (array $link): bool =>
                $user->hasAdminPermission($link['permission'])
                && \Illuminate\Support\Facades\Route::has($link['route'])
        ));

        /*
        |--------------------------------------------------------------------------
        | Visitor Chart
        |--------------------------------------------------------------------------
        */

        $chartDays = collect($canAnalytics ? ($visitChart ?? []) : []);

        $maxVisits = max(
            1,
            (int) ($chartDays->max('visits') ?? 0)
        );

        $hasRecentSections = $canProducts || $canMessages;
    @endphp

    <div class="daken-dash" dir="{{ $ar ? 'rtl' : 'ltr' }}">

        {{-- Dashboard Header --}}
        <header class="daken-dash__hero">
            <div>
                <span class="daken-dash__eyebrow">
                    DAKEN CHOCOLATE / ADMIN
                </span>

                <h1>{{ $ar ? 'لوحة التحكم' : 'Dashboard' }}</h1>

                <p>
                    {{ $ar
                        ? 'ملخص أداء الموقع وإدارة المحتوى في مكان واحد.'
                        : 'Website performance and content management in one place.'
                    }}
                </p>
            </div>

            <a
                class="daken-dash__button"
                href="{{ route('home') }}"
                target="_blank"
                rel="noopener noreferrer"
            >
                {{ $ar ? 'عرض الموقع' : 'View Website' }}
                <span aria-hidden="true">↗</span>
            </a>
        </header>

        {{-- KPI Cards --}}
        @if (count($cards) > 0)
            <section
                class="daken-dash__section"
                aria-labelledby="daken-kpis"
            >
                <div class="daken-dash__section-title">
                    <h2 id="daken-kpis">
                        {{ $ar ? 'المؤشرات الرئيسية' : 'Key Metrics' }}
                    </h2>
                </div>

                <div class="daken-dash__kpis">
                    @foreach ($cards as $card)
                        <article class="daken-dash__kpi">
                            <div class="daken-dash__kpi-head">
                                <span>{{ $card['label'] }}</span>

                                <span
                                    class="daken-dash__kpi-icon"
                                    aria-hidden="true"
                                >
                                    {{ $card['icon'] }}
                                </span>
                            </div>

                            <strong>
                                {{ number_format($card['value']) }}
                            </strong>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Analytics --}}
        @if ($canAnalytics)
            <div class="daken-dash__analytics-grid">

                {{-- Traffic Chart --}}
                <section
                    class="daken-dash__panel"
                    aria-labelledby="daken-chart-title"
                >
                    <div class="daken-dash__panel-head">
                        <div>
                            <span class="daken-dash__overline">
                                TRAFFIC
                            </span>

                            <h2 id="daken-chart-title">
                                {{ $ar
                                    ? 'الزيارات خلال آخر 7 أيام'
                                    : 'Visits in the Last 7 Days'
                                }}
                            </h2>
                        </div>
                    </div>

                    <div
                        class="daken-dash__chart"
                        role="img"
                        aria-label="{{ $ar
                            ? 'رسم بياني لزيارات الأيام السبعة'
                            : 'Seven-day visits chart'
                        }}"
                    >
                        @forelse ($chartDays as $day)
                            @php
                                $visits = (int) ($day['visits'] ?? 0);

                                $barHeight = $visits
                                    ? max(5, ($visits / $maxVisits) * 100)
                                    : 2;
                            @endphp

                            <div
                                class="daken-dash__chart-day"
                                title="{{ $day['date'] }}: {{ $visits }}"
                            >
                                <strong>{{ $visits }}</strong>

                                <div class="daken-dash__chart-track">
                                    <div
                                        class="daken-dash__chart-bar"
                                        style="height: {{ $barHeight }}%"
                                    ></div>
                                </div>

                                <small dir="ltr">
                                    {{ substr((string) $day['date'], 5) }}
                                </small>
                            </div>
                        @empty
                            <p class="daken-dash__empty">
                                {{ $ar
                                    ? 'لا توجد بيانات زيارات بعد.'
                                    : 'No traffic data yet.'
                                }}
                            </p>
                        @endforelse
                    </div>
                </section>

                {{-- Traffic Insights --}}
                <section
                    class="daken-dash__panel"
                    aria-labelledby="daken-insights-title"
                >
                    <div class="daken-dash__panel-head">
                        <div>
                            <span class="daken-dash__overline">
                                INSIGHTS
                            </span>

                            <h2 id="daken-insights-title">
                                {{ $ar
                                    ? 'ملخص حركة الزوار'
                                    : 'Traffic Insights'
                                }}
                            </h2>
                        </div>
                    </div>

                    <div class="daken-dash__insights">
                        <div>
                            <span>
                                {{ $ar
                                    ? 'زوار آخر 7 أيام'
                                    : 'Unique Visitors · 7 Days'
                                }}
                            </span>

                            <strong>
                                {{ number_format($uniqueVisitorsThisWeek ?? 0) }}
                            </strong>
                        </div>

                        <div>
                            <span>
                                {{ $ar
                                    ? 'زيارات الأمس'
                                    : 'Yesterday’s Visits'
                                }}
                            </span>

                            <strong>
                                {{ number_format($yesterdayVisits ?? 0) }}
                            </strong>
                        </div>
                    </div>

                    <h3 class="daken-dash__subheading">
                        {{ $ar
                            ? 'الصفحات الأكثر زيارة'
                            : 'Most Visited Pages'
                        }}
                    </h3>

                    <div class="daken-dash__table-scroll">
                        <table class="daken-dash__table">
                            <thead>
                            <tr>
                                <th>
                                    {{ $ar ? 'الصفحة' : 'Page' }}
                                </th>
                                <th>
                                    {{ $ar ? 'الزيارات' : 'Visits' }}
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse ($topPages ?? [] as $page)
                                <tr>
                                    <td>
                                            <span
                                                dir="ltr"
                                                class="daken-dash__path"
                                            >
                                                {{ $page->path }}
                                            </span>
                                    </td>

                                    <td>
                                        <strong>
                                            {{ number_format($page->visits_count) }}
                                        </strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="2"
                                        class="daken-dash__empty"
                                    >
                                        {{ $ar
                                            ? 'لا توجد بيانات.'
                                            : 'No data available.'
                                        }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endif

        {{-- Most Viewed Products --}}
        @if ($canProducts && $canAnalytics)
            <section
                class="daken-dash__panel daken-dash__section"
                aria-labelledby="daken-popular-title"
            >
                <div class="daken-dash__panel-head">
                    <div>
                        <span class="daken-dash__overline">
                            PRODUCT PERFORMANCE
                        </span>

                        <h2 id="daken-popular-title">
                            {{ $ar
                                ? 'المنتجات الأكثر مشاهدة'
                                : 'Most Viewed Products'
                            }}
                        </h2>
                    </div>

                    <a href="{{ route('admin.products.index') }}">
                        {{ $ar ? 'جميع المنتجات' : 'All Products' }}
                    </a>
                </div>

                <div class="daken-dash__table-scroll">
                    <table class="daken-dash__table">
                        <thead>
                        <tr>
                            <th>{{ $ar ? 'المنتج' : 'Product' }}</th>
                            <th>{{ $ar ? 'المشاهدات' : 'Views' }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse ($topProducts ?? [] as $product)
                            <tr>
                                <td>
                                    <a
                                        href="{{ route('admin.products.edit', $product) }}"
                                    >
                                        {{ $product->translated_name }}
                                    </a>
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format($product->views_count) }}
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="2"
                                    class="daken-dash__empty"
                                >
                                    {{ $ar
                                        ? 'لا توجد منتجات.'
                                        : 'No products available.'
                                    }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- Quick Access --}}
        @if (count($quickLinks) > 0)
            <section
                class="daken-dash__panel daken-dash__section"
                aria-labelledby="daken-links-title"
            >
                <div class="daken-dash__panel-head">
                    <div>
                        <span class="daken-dash__overline">
                            QUICK ACCESS
                        </span>

                        <h2 id="daken-links-title">
                            {{ $ar
                                ? 'إدارة الموقع'
                                : 'Website Management'
                            }}
                        </h2>
                    </div>
                </div>

                <div class="daken-dash__links">
                    @foreach ($quickLinks as $link)
                        <a href="{{ route($link['route']) }}">
                            <span
                                class="daken-dash__link-icon"
                                aria-hidden="true"
                            >
                                {{ $link['icon'] }}
                            </span>

                            <span>{{ $link['label'] }}</span>

                            <span
                                class="daken-dash__link-arrow"
                                aria-hidden="true"
                            >
                                ↗
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Recent Products and Messages --}}
        @if ($hasRecentSections)
            <div class="daken-dash__recent-grid daken-dash__section">

                {{-- Recent Products --}}
                @if ($canProducts)
                    <section
                        class="daken-dash__panel"
                        aria-labelledby="daken-recent-products"
                    >
                        <div class="daken-dash__panel-head">
                            <h2 id="daken-recent-products">
                                {{ $ar
                                    ? 'أحدث المنتجات'
                                    : 'Recent Products'
                                }}
                            </h2>

                            <a href="{{ route('admin.products.index') }}">
                                {{ $ar ? 'عرض الكل' : 'View All' }}
                            </a>
                        </div>

                        <div class="daken-dash__table-scroll">
                            <table class="daken-dash__table">
                                <thead>
                                <tr>
                                    <th>{{ $ar ? 'المنتج' : 'Product' }}</th>
                                    <th>{{ $ar ? 'التصنيف' : 'Category' }}</th>
                                    <th>{{ $ar ? 'الحالة' : 'Status' }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @forelse ($recentProducts ?? [] as $product)
                                    <tr>
                                        <td>
                                            <a
                                                href="{{ route('admin.products.edit', $product) }}"
                                            >
                                                {{ $product->translated_name }}
                                            </a>
                                        </td>

                                        <td>
                                            {{ $product->category?->translated_name ?? '—' }}
                                        </td>

                                        <td>
                                                <span
                                                    class="daken-dash__status {{ $product->is_active ? 'is-active' : 'is-inactive' }}"
                                                >
                                                    {{ $product->is_active
                                                        ? ($ar ? 'نشط' : 'Active')
                                                        : ($ar ? 'مخفي' : 'Hidden')
                                                    }}
                                                </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="3"
                                            class="daken-dash__empty"
                                        >
                                            {{ $ar
                                                ? 'لا توجد منتجات.'
                                                : 'No products yet.'
                                            }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif

                {{-- Recent Messages --}}
                @if ($canMessages)
                    <section
                        class="daken-dash__panel"
                        aria-labelledby="daken-recent-messages"
                    >
                        <div class="daken-dash__panel-head">
                            <h2 id="daken-recent-messages">
                                {{ $ar
                                    ? 'أحدث الرسائل'
                                    : 'Recent Messages'
                                }}
                            </h2>

                            <a href="{{ route('admin.messages.index') }}">
                                {{ $ar ? 'عرض الكل' : 'View All' }}
                            </a>
                        </div>

                        <div class="daken-dash__table-scroll">
                            <table class="daken-dash__table">
                                <thead>
                                <tr>
                                    <th>{{ $ar ? 'المرسل' : 'Sender' }}</th>
                                    <th>{{ $ar ? 'الموضوع' : 'Subject' }}</th>
                                    <th>{{ $ar ? 'الحالة' : 'Status' }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @forelse ($recentMessages ?? [] as $message)
                                    <tr>
                                        <td>{{ $message->name }}</td>

                                        <td>
                                            <a
                                                href="{{ route('admin.messages.show', $message) }}"
                                            >
                                                {{ \Illuminate\Support\Str::limit($message->subject, 30) }}
                                            </a>
                                        </td>

                                        <td>
                                                <span
                                                    class="daken-dash__status {{ $message->is_read ? 'is-active' : 'is-inactive' }}"
                                                >
                                                    {{ $message->is_read
                                                        ? ($ar ? 'مقروءة' : 'Read')
                                                        : ($ar ? 'جديدة' : 'New')
                                                    }}
                                                </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="3"
                                            class="daken-dash__empty"
                                        >
                                            {{ $ar
                                                ? 'لا توجد رسائل.'
                                                : 'No messages yet.'
                                            }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            </div>
        @endif

        {{-- Recent Admin Activities --}}
        @if ($canActivities)
            <div class="daken-dash__activity daken-dash__section">
                @include('admin.partials.recent-activities')
            </div>
        @endif

        {{-- No Permissions Assigned --}}
        @if (
            count($cards) === 0
            && count($quickLinks) === 0
            && ! $canActivities
        )
            <section class="daken-dash__panel daken-dash__section">
                <p class="daken-dash__empty">
                    {{ $ar
                        ? 'مرحبًا بك في لوحة إدارة DAKEN. لم تُعيّن صلاحيات لهذا الحساب بعد. يرجى التواصل مع المدير الرئيسي.'
                        : 'Welcome to DAKEN Admin. No management permissions have been assigned to this account yet. Please contact the principal administrator.'
                    }}
                </p>
            </section>
        @endif

    </div>
@endsection
