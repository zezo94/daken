
@extends('layouts.admin')

@section('title', 'إدارة القائمة والشعار - DAKEN')

@section('content')

    <div class="daken-navigation-admin">

        {{-- Page heading --}}
        <div class="daken-navigation-admin__heading">
            <div>
                <h1>إدارة القائمة والشعار</h1>

                <p>
                    تحكم في ظهور الروابط وترتيبها وحجم شعار DAKEN.
                </p>
            </div>
        </div>

        {{-- Success message --}}
        @if(session('success'))
            <div
                class="daken-navigation-admin__success"
                role="status"
            >
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div
                class="daken-navigation-admin__errors"
                role="alert"
            >
                <strong>تعذر حفظ الإعدادات:</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.navigation.update') }}"
        >
            @csrf
            @method('PUT')

            {{-- Navigation settings --}}
            <div class="daken-navigation-admin__table-wrap">

                <table>
                    <thead>
                    <tr>
                        <th>الرابط</th>
                        <th>مكان الظهور</th>
                        <th>الترتيب</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($items as $index => $item)

                        @php
                            $setting = $settings->get(
                                $item['key']
                            );

                            $defaultPlacement = in_array(
                                $item['key'],
                                [
                                    'home',
                                    'products.index',
                                    'gallery.index',
                                ],
                                true
                            ) ? 'main' : 'more';

                            $placement = old(
                                'items.'.$item['key'].'.placement',
                                $setting?->placement
                                    ?? $defaultPlacement
                            );

                            $order = old(
                                'items.'.$item['key'].'.sort_order',
                                $setting?->sort_order
                                    ?? ($index * 10)
                            );
                        @endphp

                        <tr>

                            <td>
                                <strong>
                                    {{ $item['title'] }}
                                </strong>
                            </td>

                            <td>
                                <select
                                    name="items[{{ $item['key'] }}][placement]"
                                    required
                                >
                                    <option
                                        value="main"
                                        @selected($placement === 'main')
                                    >
                                        الشريط الرئيسي
                                    </option>

                                    <option
                                        value="more"
                                        @selected($placement === 'more')
                                    >
                                        قائمة المزيد
                                    </option>

                                    <option
                                        value="hidden"
                                        @selected($placement === 'hidden')
                                    >
                                        مخفي
                                    </option>
                                </select>
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="items[{{ $item['key'] }}][sort_order]"
                                    value="{{ $order }}"
                                    min="0"
                                    max="9999"
                                    required
                                >
                            </td>

                        </tr>

                    @endforeach

                    </tbody>
                </table>

            </div>

            {{-- Logo settings --}}
            <div class="daken-navigation-admin__logo-settings">

                <h2>حجم شعار DAKEN</h2>

                <p>
                    حدد عرض الشعار بالبكسل على الكمبيوتر والهاتف.
                </p>

                <div class="daken-navigation-admin__logo-grid">

                    <div>
                        <label for="logo-desktop-width">
                            عرض الشعار على الكمبيوتر
                        </label>

                        <input
                            id="logo-desktop-width"
                            type="number"
                            name="logo_desktop_width"
                            min="70"
                            max="250"
                            required
                            value="{{ old('logo_desktop_width', $logoDesktop) }}"
                        >

                        <small>
                            من 70 إلى 250 بكسل
                        </small>
                    </div>

                    <div>
                        <label for="logo-mobile-width">
                            عرض الشعار على الهاتف
                        </label>

                        <input
                            id="logo-mobile-width"
                            type="number"
                            name="logo_mobile_width"
                            min="60"
                            max="180"
                            required
                            value="{{ old('logo_mobile_width', $logoMobile) }}"
                        >

                        <small>
                            من 60 إلى 180 بكسل
                        </small>
                    </div>

                </div>
            </div>

            {{-- Save --}}
            <div class="daken-navigation-admin__actions">
                <button
                    type="submit"
                    class="daken-navigation-admin__save"
                >
                    حفظ جميع الإعدادات
                </button>
            </div>

        </form>

    </div>

@endsection
