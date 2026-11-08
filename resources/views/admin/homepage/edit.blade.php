
@extends('layouts.admin')

@section('title', 'إدارة الصفحة الرئيسية - DAKEN')

@section('content')

    @php
        $sectionLabels = [
            'categories' => 'التشكيلات / Collections',
            'featured' => 'المنتجات المميزة / Featured Products',
            'about' => 'من نحن / Our Story',
            'locations' => 'أماكن البيع / Locations',
            'gallery' => 'معرض الصور / Gallery',
            'contact' => 'التواصل / Contact',
        ];

        $defaultOrders = [
            'categories' => 10,
            'featured' => 20,
            'about' => 30,
            'locations' => 40,
            'gallery' => 50,
            'contact' => 60,
        ];
    @endphp

    <section class="container homepage-admin">

        <h1>إدارة الصفحة الرئيسية</h1>

        <p>
            تحكم بمحتوى الصفحة الرئيسية وترتيب أقسامها
            بالعربية والإنجليزية.
        </p>

        @if(session('success'))
            <p role="status" class="homepage-notice">
                {{ session('success') }}
            </p>
        @endif

        @if($errors->any())
            <div role="alert" class="homepage-errors">
                <strong>تعذر حفظ التعديلات:</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('admin.homepage.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="homepage-form"
        >
            @csrf
            @method('PUT')

            {{-- Hero content --}}
            <div class="homepage-admin-section">
                <h2>محتوى الواجهة الرئيسية</h2>

                @foreach([
                    'ar' => 'العربية',
                    'en' => 'English',
                ] as $locale => $label)

                    <fieldset>
                        <legend>{{ $label }}</legend>

                        <div class="form-group">
                            <label for="hero_title_{{ $locale }}">
                                عنوان القسم الرئيسي
                            </label>

                            <input
                                id="hero_title_{{ $locale }}"
                                type="text"
                                name="hero_title_{{ $locale }}"
                                value="{{ old(
                                'hero_title_'.$locale,
                                $settings['hero_title_'.$locale] ?? ''
                            ) }}"
                                maxlength="150"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="hero_subtitle_{{ $locale }}">
                                وصف القسم الرئيسي
                            </label>

                            <textarea
                                id="hero_subtitle_{{ $locale }}"
                                name="hero_subtitle_{{ $locale }}"
                                maxlength="500"
                                rows="4"
                                required
                            >{{ old(
                            'hero_subtitle_'.$locale,
                            $settings['hero_subtitle_'.$locale] ?? ''
                        ) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="hero_button_text_{{ $locale }}">
                                نص زر التسوق
                            </label>

                            <input
                                id="hero_button_text_{{ $locale }}"
                                type="text"
                                name="hero_button_text_{{ $locale }}"
                                value="{{ old(
                                'hero_button_text_'.$locale,
                                $settings['hero_button_text_'.$locale] ?? ''
                            ) }}"
                                maxlength="60"
                                required
                            >
                        </div>
                    </fieldset>

                @endforeach
            </div>

            {{-- Hero image --}}
            <div class="homepage-admin-section">
                <h2>صورة الواجهة الرئيسية</h2>

                <div class="form-group">
                    <label for="hero_image">
                        رفع صورة جديدة
                    </label>

                    <input
                        id="hero_image"
                        type="file"
                        name="hero_image"
                        accept=".jpg,.jpeg,.png,.webp"
                    >

                    <p>
                        اترك الحقل فارغًا للاحتفاظ بالصورة الحالية.
                    </p>

                    @if(!empty($settings['hero_image']))
                        <img
                            src="{{ asset('storage/'.$settings['hero_image']) }}"
                            alt="Current Hero"
                            class="homepage-admin-preview"
                        >
                    @endif
                </div>
            </div>

            {{-- Section visibility and order --}}
            <div class="homepage-admin-section">
                <h2>إدارة أقسام الصفحة الرئيسية</h2>

                <p>
                    فعّل الأقسام التي تريد عرضها،
                    واستخدم رقم الترتيب لتحديد مكان كل قسم.
                    الرقم الأصغر يظهر أولًا.
                </p>

                <div class="homepage-sections-table-wrap">
                    <table class="homepage-sections-table">
                        <thead>
                        <tr>
                            <th>القسم</th>
                            <th>إظهار</th>
                            <th>الترتيب</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($sections as $section)
                            @php
                                $showKey = 'home_show_'.$section;
                                $orderKey = 'home_order_'.$section;

                                $isVisible = (string) old(
                                    $showKey,
                                    $settings[$showKey] ?? '1'
                                ) === '1';

                                $orderValue = old(
                                    $orderKey,
                                    $settings[$orderKey]
                                        ?? $defaultOrders[$section]
                                );
                            @endphp

                            <tr>
                                <td>
                                    <strong>
                                        {{ $sectionLabels[$section] }}
                                    </strong>
                                </td>

                                <td>
                                    <input
                                        type="hidden"
                                        name="{{ $showKey }}"
                                        value="0"
                                    >

                                    <label class="homepage-section-toggle">
                                        <input
                                            type="checkbox"
                                            name="{{ $showKey }}"
                                            value="1"
                                            @checked($isVisible)
                                        >

                                        <span>ظاهر</span>
                                    </label>
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="{{ $orderKey }}"
                                        value="{{ $orderValue }}"
                                        min="1"
                                        max="99"
                                        required
                                        class="homepage-order-input"
                                    >
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="homepage-save">
                حفظ جميع تعديلات الصفحة الرئيسية
            </button>
        </form>

    </section>

@endsection
