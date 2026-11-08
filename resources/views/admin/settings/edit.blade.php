
@extends('layouts.admin')

@section('title', 'DAKEN - Site Settings')

@section('content')

    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <x-admin.page-header
        :title="$isArabic ? 'إعدادات العلامة التجارية' : 'Brand Settings'"
        :description="$isArabic
        ? 'إدارة بيانات DAKEN وروابط التواصل والشعار.'
        : 'Manage DAKEN information, social links and logo.'"
    />

    <x-admin.card>

        <form
            action="{{ route('admin.settings.update') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="admin-form-grid">

                <x-admin.input
                    name="brand_name"
                    :label="$isArabic ? 'اسم العلامة التجارية' : 'Brand Name'"
                    :value="$settings['brand_name'] ?? 'DAKEN'"
                    required
                    maxlength="100"
                />

                <x-admin.input
                    name="contact_email"
                    type="email"
                    :label="$isArabic ? 'البريد الإلكتروني' : 'Email'"
                    :value="$settings['contact_email'] ?? ''"
                    dir="ltr"
                />

                <x-admin.input
                    name="contact_phone"
                    type="tel"
                    :label="$isArabic ? 'رقم الهاتف' : 'Phone'"
                    :value="$settings['contact_phone'] ?? ''"
                    dir="ltr"
                />

                <x-admin.input
                    name="instagram_url"
                    type="url"
                    label="Instagram"
                    :value="$settings['instagram_url'] ?? ''"
                    dir="ltr"
                />

                <x-admin.input
                    name="facebook_url"
                    type="url"
                    label="Facebook"
                    :value="$settings['facebook_url'] ?? ''"
                    dir="ltr"
                />

                <x-admin.input
                    name="tiktok_url"
                    type="url"
                    label="TikTok"
                    :value="$settings['tiktok_url'] ?? ''"
                    dir="ltr"
                />

            </div>

            <div class="admin-form-field">
                <label for="brand-logo">
                    {{ $isArabic ? 'شعار DAKEN' : 'DAKEN Logo' }}
                </label>

                <input
                    id="brand-logo"
                    name="logo"
                    type="file"
                    accept=".png,.jpg,.jpeg,.webp"
                    class="admin-form-control"
                    @error('logo')
                    aria-invalid="true"
                    aria-describedby="brand-logo-error"
                    @enderror
                >

                @error('logo')
                <small
                    id="brand-logo-error"
                    class="admin-field-error"
                    role="alert"
                >
                    {{ $message }}
                </small>
                @enderror

                @if(!empty($settings['logo']))
                    <div style="margin-top: 16px;">
                        <img
                            src="{{ asset('storage/'.$settings['logo']) }}"
                            alt="DAKEN Logo"
                            style="max-width: 180px; max-height: 120px; object-fit: contain;"
                        >
                    </div>
                @endif
            </div>

            <div class="admin-form-actions">
                <x-admin.button type="submit">
                    {{ $isArabic ? 'حفظ التعديلات' : 'Save Changes' }}
                </x-admin.button>

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="admin-ui-button admin-ui-button--secondary"
                >
                    {{ $isArabic ? 'العودة' : 'Back' }}
                </a>
            </div>

        </form>

    </x-admin.card>

@endsection
