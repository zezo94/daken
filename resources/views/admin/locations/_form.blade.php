
@php
    $location = $location ?? null;

    $locationName = $location?->name ?? [];
    $locationAddress = $location?->address ?? [];
@endphp

<div class="admin-form-grid">

    <x-admin.input
        name="name[ar]"
        id="location-name-ar"
        label="اسم الموقع بالعربية"
        :value="old('name.ar', $locationName['ar'] ?? '')"
        required
    />

    <x-admin.input
        name="name[en]"
        id="location-name-en"
        label="Location Name (English)"
        :value="old('name.en', $locationName['en'] ?? '')"
        dir="ltr"
        required
    />

    <x-admin.input
        name="city"
        id="location-city"
        label="المدينة"
        :value="old('city', $location?->city)"
        required
    />

    <x-admin.input
        name="country"
        id="location-country"
        label="الدولة"
        :value="old('country', $location?->country)"
        required
    />

    <x-admin.input
        name="phone"
        id="location-phone"
        type="tel"
        label="رقم الهاتف"
        :value="old('phone', $location?->phone)"
        dir="ltr"
    />

    <x-admin.input
        name="google_maps_url"
        id="location-google-maps-url"
        type="url"
        label="Google Maps URL"
        :value="old('google_maps_url', $location?->google_maps_url)"
        dir="ltr"
    />

    <x-admin.input
        name="latitude"
        id="location-latitude"
        type="number"
        step="any"
        label="Latitude"
        :value="old('latitude', $location?->latitude)"
        dir="ltr"
    />

    <x-admin.input
        name="longitude"
        id="location-longitude"
        type="number"
        step="any"
        label="Longitude"
        :value="old('longitude', $location?->longitude)"
        dir="ltr"
    />

    <x-admin.input
        name="sort_order"
        id="location-sort-order"
        type="number"
        min="0"
        label="ترتيب العرض"
        :value="old('sort_order', $location?->sort_order ?? 0)"
    />

</div>

<div class="admin-form-grid">

    <x-admin.textarea
        name="address[ar]"
        id="location-address-ar"
        label="العنوان بالعربية"
        :value="old('address.ar', $locationAddress['ar'] ?? '')"
        required
    />

    <x-admin.textarea
        name="address[en]"
        id="location-address-en"
        label="Address (English)"
        :value="old('address.en', $locationAddress['en'] ?? '')"
        dir="ltr"
        required
    />

</div>

<div class="admin-form-field">

    <label for="location-image">
        شعار أو صورة المكان
    </label>

    <input
        id="location-image"
        type="file"
        name="image"
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        class="admin-form-control"
    >

    <small>
        الصيغ المسموحة: JPG وPNG وWebP.
        الحد الأقصى: 2 ميجابايت.
    </small>

    @error('image')
    <div class="admin-field-error">
        {{ $message }}
    </div>
    @enderror

    <div class="admin-location-image-preview">
        <img
            src="{{ $location?->image_url ?? asset('images/location-placeholder.svg') }}"
            alt="معاينة صورة المكان"
            id="location-image-preview"
        >
    </div>

    @if($location?->image)
        <label class="admin-location-remove-image">
            <input
                type="checkbox"
                name="remove_image"
                value="1"
                @checked(old('remove_image', false))
            >
            حذف الصورة الحالية واستخدام الصورة الرمزية
        </label>
    @endif

</div>


<x-admin.checkbox
    name="is_active"
    label="موقع نشط"
    :checked="(bool) old('is_active', $location?->is_active ?? true)"
/>

<div class="admin-form-actions">

    <x-admin.button type="submit">
        {{ $location ? 'حفظ التعديلات' : 'إضافة الموقع' }}
    </x-admin.button>

    <a
        href="{{ route('admin.locations.index') }}"
        class="admin-ui-button admin-ui-button--secondary"
    >
        إلغاء
    </a>

</div>
