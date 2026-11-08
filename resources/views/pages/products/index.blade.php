
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

        <form
            action="{{ route('products.index') }}"
            method="GET"
            class="products-filters"
            role="search"
        >

            <div class="products-filters__field">

                <label for="product-search">
                    {{ $isArabic ? 'ابحث عن منتج' : 'Search products' }}
                </label>

                <input
                    id="product-search"
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    maxlength="100"
                    placeholder="{{ $isArabic ? 'اسم المنتج أو رمزه...' : 'Product name or SKU...' }}"
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
                        {{ $isArabic ? 'جميع التصنيفات' : 'All categories' }}
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
                    <option value="default" @selected($sort === 'default')>
                        {{ $isArabic ? 'الترتيب الافتراضي' : 'Default order' }}
                    </option>

                    <option value="newest" @selected($sort === 'newest')>
                        {{ $isArabic ? 'الأحدث' : 'Newest' }}
                    </option>

                    <option value="oldest" @selected($sort === 'oldest')>
                        {{ $isArabic ? 'الأقدم' : 'Oldest' }}
                    </option>

                    <option value="name_asc" @selected($sort === 'name_asc')>
                        {{ $isArabic ? 'الاسم: أ إلى ي' : 'Name: A to Z' }}
                    </option>

                    <option value="name_desc" @selected($sort === 'name_desc')>
                        {{ $isArabic ? 'الاسم: ي إلى أ' : 'Name: Z to A' }}
                    </option>
                </select>

            </div>

            <div class="products-filters__actions">

                <button type="submit" class="products-filters__submit">
                    {{ $isArabic ? 'تطبيق' : 'Apply' }}
                </button>

                <a
                    href="{{ route('products.index') }}"
                    class="products-filters__reset"
                >
                    {{ $isArabic ? 'إعادة ضبط' : 'Reset' }}
                </a>

            </div>

        </form>

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
                    {{ $isArabic ? 'عرض جميع المنتجات' : 'View all products' }}
                </a>

            </div>

        @endif

    </section>

@endsection
