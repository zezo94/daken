
@extends('layouts.admin')

@section('title', 'DAKEN Analytics')

@section('content')

    @php
        $ar = app()->getLocale() === 'ar';


$cards = [
    [
        'label' => $ar ? 'زيارات الصفحات' : 'Page Views',
        'value' => $totalVisits,
        'change' => $changes['visits'],
    ],
    [
        'label' => $ar ? 'الزوار الفريدون' : 'Unique Visitors',
        'value' => $uniqueVisitors,
        'change' => $changes['visitors'],
    ],
    [
        'label' => $ar ? 'مشاهدات المنتجات' : 'Product Views',
        'value' => $totalProductViews,
        'change' => $changes['products'],
    ],
    [
        'label' => $ar
            ? 'متوسط الصفحات لكل زائر'
            : 'Pages per Visitor',
        'value' => $averageViews,
        'change' => $changes['average'],
    ],
];

        $maxVisits = max(
            1,
            collect($chartData)->max('visits') ?? 0,
            collect($chartData)->max('product_views') ?? 0
        );
    @endphp

    <div class="container" style="padding-block: 55px 90px;">

        <header style="margin-bottom: 30px;">
        <span style="color: #9b7049; font-weight: 700;">
            DAKEN CHOCOLATE
        </span>

            <h1 style="margin: 12px 0;">
                {{ $ar ? 'تحليلات الموقع' : 'Website Analytics' }}
            </h1>

            <p style="color: #76685d;">
                {{ $ar
                    ? 'تحليل زيارات الموقع ومشاهدات المنتجات.'
                    : 'Website traffic and product performance.' }}
            </p>
        </header>

        <form method="GET" style="margin-bottom: 30px;">
            <label for="analytics-period">
                {{ $ar ? 'الفترة الزمنية' : 'Date Range' }}
            </label>

            <select
                id="analytics-period"
                name="period"
                onchange="this.form.submit()"
                style="
                margin-inline-start: 12px;
                padding: 10px 16px;
                border: 1px solid #dfd2c5;
                border-radius: 10px;
                background: white;
            "
            >
                <option value="7" @selected($period === 7)>
                    {{ $ar ? 'آخر 7 أيام' : 'Last 7 Days' }}
                </option>

                <option value="30" @selected($period === 30)>
                    {{ $ar ? 'آخر 30 يومًا' : 'Last 30 Days' }}
                </option>

                <option value="90" @selected($period === 90)>
                    {{ $ar ? 'آخر 90 يومًا' : 'Last 90 Days' }}
                </option>
            </select>

            <noscript>
                <button type="submit">
                    {{ $ar ? 'تطبيق' : 'Apply' }}
                </button>
            </noscript>
        </form>


        {{-- Analytics CSV Exports --}}
        <div
            style="
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 30px;
    "
        >
            <a
                href="{{ route('admin.analytics.export', [
            'period' => $period,
            'report' => 'daily',
        ]) }}"
                class="btn btn-primary"
            >
                {{ $ar
                    ? 'تصدير الزيارات اليومية CSV'
                    : 'Export Daily Traffic CSV' }}
            </a>

            <a
                href="{{ route('admin.analytics.export', [
            'period' => $period,
            'report' => 'pages',
        ]) }}"
                class="btn btn-outline"
            >
                {{ $ar
                    ? 'تصدير الصفحات CSV'
                    : 'Export Pages CSV' }}
            </a>

            <a
                href="{{ route('admin.analytics.export', [
            'period' => $period,
            'report' => 'products',
        ]) }}"
                class="btn btn-outline"
            >
                {{ $ar
                    ? 'تصدير المنتجات CSV'
                    : 'Export Products CSV' }}
            </a>
        </div>


        <div class="admin-dashboard__stats">

            @foreach($cards as $card)
                <article class="admin-stat-card">

                    <div class="admin-stat-card__top">
                        <span>{{ $card['label'] }}</span>
                    </div>

                    <strong>
                        {{ number_format(
                            $card['value'],
                            is_float($card['value']) ? 1 : 0
                        ) }}
                    </strong>

                    <div style="margin-top: 14px;">

                        @if($card['change'] === null)
                            <span style="color: #9b7049;">
                        {{ $ar ? 'جديد' : 'New' }}
                    </span>

                        @elseif($card['change'] > 0)
                            <span style="color: #16803c;">
                        ↑ +{{ $card['change'] }}%
                    </span>

                        @elseif($card['change'] < 0)
                            <span style="color: #b42318;">
                        ↓ {{ $card['change'] }}%
                    </span>

                        @else
                            <span style="color: #777;">
                        0%
                    </span>
                        @endif

                        <div
                            style="
                        margin-top: 5px;
                        font-size: 12px;
                        color: #87796d;
                    "
                        >
                            {{ $ar
                                ? 'مقارنة بالفترة السابقة'
                                : 'Compared with previous period' }}
                        </div>

                    </div>

                </article>
            @endforeach

        </div>


        <section class="admin-panel" style="margin-top: 30px;">

            <div class="admin-panel__heading">
                <h2>
                    {{ $ar ? 'الزيارات اليومية' : 'Daily Traffic' }}
                </h2>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                <span>■ {{ $ar ? 'الزيارات' : 'Visits' }}</span>
                <span style="color: #a77b51;">
                ■ {{ $ar ? 'مشاهدات المنتجات' : 'Product Views' }}
            </span>
            </div>

            <div style="overflow-x: auto;">
                <div
                    style="
                    display: flex;
                    align-items: end;
                    gap: 10px;
                    min-width: {{ $period * 26 }}px;
                    height: 240px;
                    padding: 20px 0;
                "
                >
                    @foreach($chartData as $day)
                        @php
                            $visitHeight = $day['visits'] > 0
                                ? max(3, $day['visits'] / $maxVisits * 170)
                                : 2;

                            $productHeight = $day['product_views'] > 0
                                ? max(3, $day['product_views'] / $maxVisits * 170)
                                : 2;
                        @endphp

                        <div
                            style="
                            display: flex;
                            flex: 1;
                            align-items: end;
                            justify-content: center;
                            gap: 3px;
                            height: 190px;
                        "
                            title="{{ $day['date'] }} | Visits: {{ $day['visits'] }} | Products: {{ $day['product_views'] }}"
                        >
                            <div
                                style="
                                width: 9px;
                                height: {{ $visitHeight }}px;
                                background: #4b3122;
                                border-radius: 4px 4px 0 0;
                            "
                            ></div>

                            <div
                                style="
                                width: 9px;
                                height: {{ $productHeight }}px;
                                background: #a77b51;
                                border-radius: 4px 4px 0 0;
                            "
                            ></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="admin-dashboard__columns" style="margin-top: 30px;">

            <section class="admin-panel">
                <div class="admin-panel__heading">
                    <h2>
                        {{ $ar ? 'الصفحات الأكثر زيارة' : 'Top Pages' }}
                    </h2>
                </div>

                <div class="admin-dashboard__table-wrap">
                    <table class="admin-dashboard__table">
                        <thead>
                        <tr>
                            <th>{{ $ar ? 'الصفحة' : 'Page' }}</th>
                            <th>{{ $ar ? 'الزيارات' : 'Views' }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($topPages as $page)
                            <tr>
                                <td>
                                    <span dir="ltr">{{ $page->path }}</span>
                                </td>

                                <td>
                                    {{ number_format($page->visits_count) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">
                                    {{ $ar ? 'لا توجد بيانات.' : 'No data.' }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="admin-panel">
                <div class="admin-panel__heading">
                    <h2>
                        {{ $ar ? 'المنتجات الأكثر مشاهدة' : 'Top Products' }}
                    </h2>
                </div>

                <div class="admin-dashboard__table-wrap">
                    <table class="admin-dashboard__table">
                        <thead>
                        <tr>
                            <th>{{ $ar ? 'المنتج' : 'Product' }}</th>
                            <th>{{ $ar ? 'المشاهدات' : 'Views' }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($topProducts as $product)
                            <tr>
                                <td>
                                    {{ $product->translated_name }}
                                </td>

                                <td>
                                    {{ number_format($product->period_views_count) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">
                                    {{ $ar ? 'لا توجد منتجات.' : 'No products.' }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

    </div>

@endsection
