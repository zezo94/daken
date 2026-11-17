
@extends('layouts.app')

@section(
    'title',
    app()->getLocale() === 'ar'
        ? 'منتجاتنا - DAKEN Chocolate'
        : 'Our Products - DAKEN Chocolate'
)

@section('content')

    @php
        $isArabic = app()->getLocale() === 'ar';

        $hasActiveFilters = $search !== ''
            || !empty($category)
            || $sort !== 'default';
    @endphp

    <section class="container products-page">

        <header class="products-page__header">
            <span class="products-page__eyebrow">
                DAKEN Chocolate
            </span>

            <h1>
                {{ $isArabic ? 'منتجاتنا' : 'Our Products' }}
            </h1>

            <p>
                {{ $isArabic
                    ? 'اكتشف مجموعتنا من منتجات الشوكولاتة.'
                    : 'Explore our collection of chocolate products.' }}
            </p>
        </header>

        {{-- Collapsible filters and page-size selector --}}
        <div class="daken-listing-toolbar">

            <details
                class="daken-filter-panel"
                @if($hasActiveFilters) open @endif
            >
                <summary class="daken-filter-toggle">
                    <span class="daken-filter-toggle__label">
                        <span aria-hidden="true">☷</span>

                        {{ $isArabic
                            ? 'البحث والتصفية'
                            : 'Search & Filters' }}

                        @if($hasActiveFilters)
                            <span class="daken-filter-active">
                                {{ $isArabic ? 'نشط' : 'Active' }}
                            </span>
                        @endif
                    </span>

                    <span
                        class="daken-filter-toggle__arrow"
                        aria-hidden="true"
                    >⌄</span>
                </summary>

                <form
                    action="{{ route('products.index') }}"
                    method="GET"
                    class="products-filters"
                    role="search"
                >
                    <input
                        type="hidden"
                        name="per_page"
                        value="{{ $perPage }}"
                    >

                    <div class="products-filters__field">
                        <label for="product-search">
                            {{ $isArabic
                                ? 'ابحث عن منتج'
                                : 'Search products' }}
                        </label>

                        <input
                            id="product-search"
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            maxlength="100"
                            placeholder="{{ $isArabic
                                ? 'اسم المنتج أو رمزه...'
                                : 'Product name or SKU...' }}"
                        >
                    </div>

                    <div class="products-filters__field">
                        <label for="product-category">
                            {{ $isArabic ? 'التصنيف' : 'Category' }}
                        </label>

                        <select
                            id="product-category"
                            name="category"
                        >
                            <option value="">
                                {{ $isArabic
                                    ? 'جميع التصنيفات'
                                    : 'All categories' }}
                            </option>

                            @foreach($categories as $item)
                                <option
                                    value="{{ $item->slug }}"
                                    @selected($category === $item->slug)
                                >
                                    {{ $item->translated_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="products-filters__field">
                        <label for="product-sort">
                            {{ $isArabic ? 'ترتيب حسب' : 'Sort by' }}
                        </label>

                        <select
                            id="product-sort"
                            name="sort"
                        >
                            <option
                                value="default"
                                @selected($sort === 'default')
                            >
                                {{ $isArabic
                                    ? 'الترتيب الافتراضي'
                                    : 'Default order' }}
                            </option>

                            <option
                                value="newest"
                                @selected($sort === 'newest')
                            >
                                {{ $isArabic ? 'الأحدث' : 'Newest' }}
                            </option>

                            <option
                                value="oldest"
                                @selected($sort === 'oldest')
                            >
                                {{ $isArabic ? 'الأقدم' : 'Oldest' }}
                            </option>

                            <option
                                value="name_asc"
                                @selected($sort === 'name_asc')
                            >
                                {{ $isArabic
                                    ? 'الاسم: أ إلى ي'
                                    : 'Name: A to Z' }}
                            </option>

                            <option
                                value="name_desc"
                                @selected($sort === 'name_desc')
                            >
                                {{ $isArabic
                                    ? 'الاسم: ي إلى أ'
                                    : 'Name: Z to A' }}
                            </option>
                        </select>
                    </div>

                    <div class="products-filters__actions">
                        <button
                            type="submit"
                            class="products-filters__submit"
                        >
                            {{ $isArabic ? 'تطبيق' : 'Apply' }}
                        </button>

                        <a
                            href="{{ route('products.index', ['per_page' => $perPage]) }}"
                            class="products-filters__reset"
                        >
                            {{ $isArabic ? 'إعادة ضبط' : 'Reset' }}
                        </a>
                    </div>
                </form>
            </details>

            {{-- Independent form: never nested inside filters --}}
            <form
                method="GET"
                action="{{ route('products.index') }}"
                class="daken-page-size-form"
            >
                @if($search !== '')
                    <input
                        type="hidden"
                        name="search"
                        value="{{ $search }}"
                    >
                @endif

                @if(!empty($category))
                    <input
                        type="hidden"
                        name="category"
                        value="{{ $category }}"
                    >
                @endif

                @if($sort !== 'default')
                    <input
                        type="hidden"
                        name="sort"
                        value="{{ $sort }}"
                    >
                @endif

                <label for="products-per-page">
                    {{ $isArabic
                        ? 'عرض لكل صفحة'
                        : 'Items per page' }}
                </label>

                <select
                    id="products-per-page"
                    name="per_page"
                    onchange="this.form.requestSubmit()"
                >
                    @foreach([6, 12, 24, 48] as $count)
                        <option
                            value="{{ $count }}"
                            @selected($perPage === $count)
                        >
                            {{ $count }}
                        </option>
                    @endforeach
                </select>

                <noscript>
                    <button type="submit">
                        {{ $isArabic ? 'تطبيق' : 'Apply' }}
                    </button>
                </noscript>
            </form>

        </div>

        {{-- Results summary --}}
        <div class="products-results">

            <p aria-live="polite">
                {{ $isArabic ? 'عدد النتائج:' : 'Results:' }}

                <strong>{{ $products->total() }}</strong>
            </p>

            @if($products->total() > 0)
                <p>
                    {{ $isArabic ? 'عرض' : 'Showing' }}

                    {{ $products->firstItem() }}

                    -

                    {{ $products->lastItem() }}

                    {{ $isArabic ? 'من' : 'of' }}

                    {{ $products->total() }}
                </p>
            @endif

        </div>

        {{-- Product cards --}}
        @if($products->isNotEmpty())

            <div class="products-grid">
                @foreach($products as $product)
                    <x-cards.product :product="$product" />
                @endforeach
            </div>

            <div class="products-pagination">
                {{ $products->links() }}
            </div>

        @else

            <div class="products-empty">

                <h2>
                    {{ $isArabic
                        ? 'لم نجد منتجات مطابقة'
                        : 'No matching products found' }}
                </h2>

                <p>
                    {{ $isArabic
                        ? 'جرّب تغيير كلمات البحث أو اختيار تصنيف آخر.'
                        : 'Try another search term or category.' }}
                </p>

                <a href="{{ route('products.index') }}">
                    {{ $isArabic
                        ? 'عرض جميع المنتجات'
                        : 'View all products' }}
                </a>

            </div>

        @endif

    </section>

@endsection
