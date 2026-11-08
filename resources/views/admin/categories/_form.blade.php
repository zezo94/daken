
@php
    $category = $category ?? null;

    $categoryName = $category?->name ?? [];
    $categoryDescription = $category?->description ?? [];
@endphp

<div class="admin-form-grid">

    <x-admin.input
        name="name[ar]"
        id="category-name-ar"
        label="اسم التصنيف بالعربية"
        :value="$categoryName['ar'] ?? ''"
        required
    />

    <x-admin.input
        name="name[en]"
        id="category-name-en"
        label="Category Name (English)"
        :value="$categoryName['en'] ?? ''"
        dir="ltr"
        required
    />

    <x-admin.input
        name="slug"
        label="Slug"
        :value="$category?->slug"
        dir="ltr"
        required
    />

    <x-admin.input
        name="sort_order"
        label="ترتيب العرض"
        type="number"
        min="0"
        :value="$category?->sort_order ?? 0"
    />

</div>

<div class="admin-form-grid">

    <x-admin.textarea
        name="description[ar]"
        id="category-description-ar"
        label="الوصف بالعربية"
        :value="$categoryDescription['ar'] ?? ''"
    />

    <x-admin.textarea
        name="description[en]"
        id="category-description-en"
        label="Description (English)"
        :value="$categoryDescription['en'] ?? ''"
        dir="ltr"
    />

</div>

<x-admin.image-upload
    name="image"
    label="صورة التصنيف"
    :current-image="$category?->image"
/>

<x-admin.checkbox
    name="is_active"
    label="تصنيف نشط"
    :checked="$category?->is_active ?? true"
/>

<div class="admin-form-actions">

    <x-admin.button type="submit">
        {{ $category ? 'حفظ التعديلات' : 'إضافة التصنيف' }}
    </x-admin.button>

    <a
        href="{{ route('admin.categories.index') }}"
        class="admin-ui-button admin-ui-button--secondary"
    >
        إلغاء
    </a>

</div>
