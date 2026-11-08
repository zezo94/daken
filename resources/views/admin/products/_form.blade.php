
@php
    $product = $product ?? null;

    $categoryOptions = $categories->mapWithKeys(
        fn ($category) => [
            $category->id => $category->translated_name
        ]
    )->all();

    $productName = $product?->name ?? [];
    $productDescription = $product?->description ?? [];
@endphp

<div class="admin-form-grid">

    <x-admin.input
        name="name[ar]"
        id="product-name-ar"
        label="اسم المنتج بالعربية"
        :value="$productName['ar'] ?? ''"
        required
    />

    <x-admin.input
        name="name[en]"
        id="product-name-en"
        label="Product Name (English)"
        :value="$productName['en'] ?? ''"
        dir="ltr"
        required
    />

    <x-admin.input
        name="slug"
        label="Slug"
        :value="$product?->slug"
        dir="ltr"
        required
    />

    <x-admin.input
        name="sku"
        label="SKU"
        :value="$product?->sku"
        dir="ltr"
    />

    <x-admin.select
        name="category_id"
        label="التصنيف"
        :options="$categoryOptions"
        :value="$product?->category_id"
        placeholder="اختر التصنيف"
        required
    />

    <x-admin.input
        name="weight"
        label="الوزن"
        type="number"
        min="0"
        step="0.01"
        :value="$product?->weight"
    />

    <x-admin.input
        name="sort_order"
        label="ترتيب العرض"
        type="number"
        min="0"
        :value="$product?->sort_order ?? 0"
    />

</div>

<div class="admin-form-grid">

    <x-admin.textarea
        name="description[ar]"
        id="product-description-ar"
        label="وصف المنتج بالعربية"
        :value="$productDescription['ar'] ?? ''"
        rows="5"
    />

    <x-admin.textarea
        name="description[en]"
        id="product-description-en"
        label="Product Description (English)"
        :value="$productDescription['en'] ?? ''"
        rows="5"
        dir="ltr"
    />

</div>

<x-admin.image-upload
    name="image"
    label="صورة المنتج"
    :current-image="$product?->image"
/>

<div class="admin-form-options">

    <x-admin.checkbox
        name="is_active"
        label="منتج نشط"
        :checked="$product?->is_active ?? true"
    />

    <x-admin.checkbox
        name="is_featured"
        label="منتج مميز"
        :checked="$product?->is_featured ?? false"
    />

</div>

<div class="admin-form-actions">

    <x-admin.button type="submit">
        {{ $product
            ? 'حفظ التعديلات'
            : 'إضافة المنتج' }}
    </x-admin.button>

    <a
        href="{{ route('admin.products.index') }}"
        class="admin-ui-button admin-ui-button--secondary"
    >
        إلغاء
    </a>

</div>
