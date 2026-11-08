
@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar'
    ? 'إدارة المنتجات - DAKEN'
    : 'Product Management - DAKEN')

@section('content')

    @php
        $ar = app()->getLocale() === 'ar';

        $featured = (string) ($featured ?? '');
        $sort = $sort ?? 'default';
        $viewMode = $viewMode ?? 'table';
    @endphp

    <x-admin.page-header
        :title="$ar ? 'إدارة المنتجات' : 'Product Management'"
        :description="$ar
        ? 'عرض منتجات DAKEN والبحث والتصفية وتحليل المشاهدات.'
        : 'Manage, filter and analyze DAKEN products.'"
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.products.create') }}"
                class="admin-ui-button admin-ui-button--primary"
            >
                {{ $ar ? '+ إضافة منتج' : '+ Add Product' }}
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.card>

        {{-- Filters --}}
        <form
            action="{{ route('admin.products.index') }}"
            method="GET"
            class="admin-list-filters"
        >
            <div class="admin-form-field">
                <label for="product-search">
                    {{ $ar ? 'بحث' : 'Search' }}
                </label>

                <input
                    id="product-search"
                    type="search"
                    name="search"
                    class="admin-form-control"
                    value="{{ $search }}"
                    placeholder="{{ $ar
                    ? 'اسم المنتج أو Slug'
                    : 'Product name or slug' }}"
                >
            </div>

            <div class="admin-form-field">
                <label for="product-category">
                    {{ $ar ? 'التصنيف' : 'Category' }}
                </label>

                <select
                    id="product-category"
                    name="category_id"
                    class="admin-form-control"
                >
                    <option value="">
                        {{ $ar ? 'جميع التصنيفات' : 'All Categories' }}
                    </option>

                    @foreach($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected((string) $categoryId === (string) $category->id)
                        >
                            {{ $category->name[$ar ? 'ar' : 'en']
                                ?? $category->name['ar']
                                ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-field">
                <label for="product-status">
                    {{ $ar ? 'الحالة' : 'Status' }}
                </label>

                <select
                    id="product-status"
                    name="status"
                    class="admin-form-control"
                >
                    <option value="">
                        {{ $ar ? 'جميع الحالات' : 'All Statuses' }}
                    </option>

                    <option value="1" @selected((string) $status === '1')>
                        {{ $ar ? 'نشط' : 'Active' }}
                    </option>

                    <option value="0" @selected((string) $status === '0')>
                        {{ $ar ? 'غير نشط' : 'Inactive' }}
                    </option>
                </select>
            </div>

            <div class="admin-form-field">
                <label for="product-featured">
                    {{ $ar ? 'المنتجات المميزة' : 'Featured' }}
                </label>

                <select
                    id="product-featured"
                    name="featured"
                    class="admin-form-control"
                >
                    <option value="">
                        {{ $ar ? 'الكل' : 'All' }}
                    </option>

                    <option value="1" @selected($featured === '1')>
                        {{ $ar ? 'مميز' : 'Featured' }}
                    </option>

                    <option value="0" @selected($featured === '0')>
                        {{ $ar ? 'غير مميز' : 'Not Featured' }}
                    </option>
                </select>
            </div>

            <div class="admin-form-field">
                <label for="product-sort">
                    {{ $ar ? 'الترتيب' : 'Sort By' }}
                </label>

                <select
                    id="product-sort"
                    name="sort"
                    class="admin-form-control"
                >
                    <option value="default" @selected($sort === 'default')>
                        {{ $ar ? 'الترتيب الافتراضي' : 'Default' }}
                    </option>

                    <option value="most_viewed" @selected($sort === 'most_viewed')>
                        {{ $ar ? 'الأكثر مشاهدة' : 'Most Viewed' }}
                    </option>

                    <option value="newest" @selected($sort === 'newest')>
                        {{ $ar ? 'الأحدث' : 'Newest' }}
                    </option>

                    <option value="oldest" @selected($sort === 'oldest')>
                        {{ $ar ? 'الأقدم' : 'Oldest' }}
                    </option>
                </select>
            </div>

            <div class="admin-form-field">
                <label for="product-view">
                    {{ $ar ? 'طريقة العرض' : 'View Mode' }}
                </label>

                <select
                    id="product-view"
                    name="view"
                    class="admin-form-control"
                >
                    <option value="table" @selected($viewMode === 'table')>
                        {{ $ar ? 'جدول' : 'Table' }}
                    </option>

                    <option value="grid" @selected($viewMode === 'grid')>
                        {{ $ar ? 'شبكة' : 'Grid' }}
                    </option>
                </select>
            </div>

            <div class="daken-products-filter-actions">
                <button
                    type="submit"
                    class="admin-ui-button admin-ui-button--primary"
                >
                    {{ $ar ? 'تطبيق الفلاتر' : 'Apply Filters' }}
                </button>

                <a
                    href="{{ route('admin.products.index') }}"
                    class="admin-ui-button admin-ui-button--secondary"
                >
                    {{ $ar ? 'إعادة تعيين' : 'Reset' }}
                </a>
            </div>
        </form>


        <form
            id="bulk-products-form"
            action="{{ route('admin.products.bulk-action') }}"
            method="POST"
        >
            @csrf

            <div class="daken-products-select-all">
                <label for="select-all-products">
                    <input
                        type="checkbox"
                        id="select-all-products"
                    >

                    <span>
            {{ $ar
                ? 'تحديد جميع المنتجات في الصفحة الحالية'
                : 'Select all products on this page' }}
        </span>
                </label>
            </div>

            <div class="daken-bulk-toolbar">
                <div class="daken-bulk-toolbar__info">
                    <strong id="selected-products-count">0</strong>

                    <span>
                {{ $ar ? 'منتجات محددة' : 'Selected Products' }}
            </span>
                </div>

                <label for="bulk-action">
                    {{ $ar ? 'الإجراء' : 'Action' }}
                </label>

                <select
                    name="action"
                    id="bulk-action"
                    class="admin-form-control"
                    required
                >
                    <option value="">
                        {{ $ar ? 'اختر الإجراء' : 'Choose Action' }}
                    </option>

                    <option value="activate">
                        {{ $ar ? 'تفعيل المحدد' : 'Activate Selected' }}
                    </option>

                    <option value="deactivate">
                        {{ $ar ? 'إلغاء تفعيل المحدد' : 'Deactivate Selected' }}
                    </option>

                    <option value="delete">
                        {{ $ar ? 'حذف المحدد' : 'Delete Selected' }}
                    </option>
                </select>

                <button
                    type="submit"
                    class="admin-ui-button admin-ui-button--primary"
                >
                    {{ $ar ? 'تنفيذ' : 'Apply' }}
                </button>
            </div>

            <div id="bulk-products-hidden-inputs"></div>
        </form>


        {{-- Results --}}
        <div class="daken-products-results">
            {{ $ar ? 'النتائج:' : 'Results:' }}
            <strong>{{ number_format($products->total()) }}</strong>
        </div>

        @if($products->count())

            @if($viewMode === 'grid')

                {{-- Grid View --}}
                <div class="daken-products-grid">

                    @foreach($products as $product)
                        @php
                            $productName = $product->name[$ar ? 'ar' : 'en']
                                ?? $product->name['ar']
                                ?? '';

                            $categoryName = $product->category?->name[
                                $ar ? 'ar' : 'en'
                            ] ?? $product->category?->name['ar'] ?? '—';
                        @endphp

                        <article class="daken-product-admin-card">
                            <div class="daken-product-card-selection">
                                <label>
                                    <input
                                        type="checkbox"
                                        class="daken-product-checkbox"
                                        value="{{ $product->id }}"
                                    >

                                    {{ $ar ? 'تحديد المنتج' : 'Select Product' }}
                                </label>
                            </div>

                            <div class="daken-product-admin-card__image">
                                @if($product->image)
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image) }}"
                                        alt="{{ $productName }}"
                                        loading="lazy"
                                    >
                                @else
                                    <span>DAKEN</span>
                                @endif
                            </div>

                            <div class="daken-product-admin-card__body">

                                <h3>{{ $productName }}</h3>

                                <p class="daken-product-admin-card__category">
                                    {{ $categoryName }}
                                </p>

                                <div class="daken-product-admin-card__badges">
                                    <x-admin.status
                                        :active="$product->is_active"
                                    />

                                    @if($product->is_featured)
                                        <span class="daken-product-featured">
                                        ★ {{ $ar ? 'مميز' : 'Featured' }}
                                    </span>
                                    @endif
                                </div>

                                <p class="daken-product-admin-card__views">
                                    {{ $ar ? 'المشاهدات' : 'Views' }}
                                    <strong>
                                        {{ number_format($product->views_count ?? 0) }}
                                    </strong>
                                </p>

                                <div class="admin-table-actions">
                                    <a
                                        href="{{ route('admin.products.images.index', $product) }}"
                                        class="admin-ui-button admin-ui-button--secondary"
                                    >
                                        {{ $ar ? 'الصور' : 'Images' }}
                                    </a>

                                    <a
                                        href="{{ route('admin.products.edit', $product) }}"
                                        class="admin-ui-button admin-ui-button--secondary"
                                    >
                                        {{ $ar ? 'تعديل' : 'Edit' }}
                                    </a>

                                    <form
                                        action="{{ route('admin.products.destroy', $product) }}"
                                        method="POST"
                                        onsubmit="return confirm(@js($ar ? 'هل تريد حذف هذا المنتج؟' : 'Delete this product?'));"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="admin-ui-button admin-ui-button--danger"
                                        >
                                            {{ $ar ? 'حذف' : 'Delete' }}
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </article>
                    @endforeach

                </div>

            @else

                {{-- Table View --}}
                <x-admin.table
                    :headers="[

                    $ar ? 'تحديد' : 'Select',
                    $ar ? 'الصورة' : 'Image',
                    $ar ? 'المنتج' : 'Product',
                    $ar ? 'التصنيف' : 'Category',
                    $ar ? 'الحالة' : 'Status',
                    $ar ? 'مميز' : 'Featured',
                    $ar ? 'المشاهدات' : 'Views',
                    $ar ? 'الإجراءات' : 'Actions',

                ]"
                >
                    @foreach($products as $product)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    class="daken-product-checkbox"
                                    value="{{ $product->id }}"
                                    aria-label="{{ $ar ? 'تحديد المنتج' : 'Select product' }} #{{ $product->id }}"
                                >
                            </td>
                            <td>
                                @if($product->image)
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image) }}"
                                        alt=""
                                        class="admin-table-image"
                                        loading="lazy"
                                    >
                                @else
                                    <span class="admin-table-placeholder">
                                    DAKEN
                                </span>
                                @endif
                            </td>

                            <td>
                                <strong>
                                    {{ $product->name[$ar ? 'ar' : 'en']
                                        ?? $product->name['ar']
                                        ?? '' }}
                                </strong>

                                <div class="daken-product-secondary-name">
                                    {{ $product->name[$ar ? 'en' : 'ar'] ?? '' }}
                                </div>
                            </td>

                            <td>
                                {{ $product->category?->name[
                                    $ar ? 'ar' : 'en'
                                ] ?? $product->category?->name['ar'] ?? '—' }}
                            </td>

                            <td>
                                <x-admin.status
                                    :active="$product->is_active"
                                />
                            </td>

                            <td>
                                {{ $product->is_featured
                                    ? ($ar ? 'نعم' : 'Yes')
                                    : ($ar ? 'لا' : 'No') }}
                            </td>

                            <td>
                                <strong>
                                    {{ number_format($product->views_count ?? 0) }}
                                </strong>
                            </td>

                            <td>
                                <div class="admin-table-actions">
                                    <a
                                        href="{{ route('admin.products.images.index', $product) }}"
                                        class="admin-ui-button admin-ui-button--secondary"
                                    >
                                        {{ $ar ? 'الصور' : 'Images' }}
                                    </a>

                                    <a
                                        href="{{ route('admin.products.edit', $product) }}"
                                        class="admin-ui-button admin-ui-button--secondary"
                                    >
                                        {{ $ar ? 'تعديل' : 'Edit' }}
                                    </a>

                                    <form
                                        action="{{ route('admin.products.destroy', $product) }}"
                                        method="POST"
                                        onsubmit="return confirm(@js($ar ? 'هل تريد حذف هذا المنتج؟' : 'Delete this product?'));"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="admin-ui-button admin-ui-button--danger"
                                        >
                                            {{ $ar ? 'حذف' : 'Delete' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.table>

            @endif

            <div class="admin-pagination">
                {{ $products->links() }}
            </div>

        @else

            <div class="admin-empty-state">
                {{ $ar
                    ? 'لا توجد منتجات مطابقة للفلاتر المحددة.'
                    : 'No products match the selected filters.' }}
            </div>

        @endif

    </x-admin.card>





    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('bulk-products-form');

                if (!form) {
                    return;
                }

                const checkboxes = Array.from(
                    document.querySelectorAll('.daken-product-checkbox')
                );

                const selectAll = document.getElementById(
                    'select-all-products'
                );

                const counter = document.getElementById(
                    'selected-products-count'
                );

                const container = document.getElementById(
                    'bulk-products-hidden-inputs'
                );

                const actionSelect = document.getElementById('bulk-action');

                function updateSelection() {
                    const selected = checkboxes.filter(
                        checkbox => checkbox.checked
                    );

                    counter.textContent = selected.length;

                    container.replaceChildren();

                    selected.forEach(checkbox => {
                        const input = document.createElement('input');

                        input.type = 'hidden';
                        input.name = 'product_ids[]';
                        input.value = checkbox.value;

                        container.appendChild(input);
                    });

                    if (selectAll) {
                        selectAll.checked =
                            checkboxes.length > 0 &&
                            selected.length === checkboxes.length;

                        selectAll.indeterminate =
                            selected.length > 0 &&
                            selected.length < checkboxes.length;
                    }
                }

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelection);
                });

                if (selectAll) {
                    selectAll.addEventListener('change', () => {
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = selectAll.checked;
                        });

                        updateSelection();
                    });
                }

                form.addEventListener('submit', event => {
                    // Disable the inline handler if it exists.
                    // Confirmation is handled here.
                    event.preventDefault();

                    const selectedCount = checkboxes.filter(
                        checkbox => checkbox.checked
                    ).length;

                    const action = actionSelect.value;

                    if (selectedCount === 0) {
                        alert(@js($ar
                ? 'يرجى تحديد منتج واحد على الأقل.'
                : 'Please select at least one product.'
            ));
                        return;
                    }

                    if (!action) {
                        alert(@js($ar
                ? 'يرجى اختيار الإجراء المطلوب.'
                : 'Please choose an action.'
            ));
                        return;
                    }

                    let message;

                    if (action === 'delete') {
                        message = @js($ar
                ? 'تحذير: سيتم حذف المنتجات المحددة. لا يمكن التراجع عن هذه العملية. هل تريد المتابعة؟'
                : 'Warning: Selected products will be deleted. This cannot be undone. Continue?'
            );
                    } else {
                        message = @js($ar
                ? 'هل تريد تطبيق الإجراء على المنتجات المحددة؟'
                : 'Apply this action to selected products?'
            );
                    }

                    if (confirm(message)) {
                        form.submit();
                    }
                });

                updateSelection();
            });
        </script>
    @endpush






@endsection
