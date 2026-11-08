
@extends('layouts.app')

@section(
    'title',
    app()->getLocale() === 'ar'
        ? 'أماكن البيع - DAKEN Chocolate'
        : 'Where to Find Us - DAKEN Chocolate'
)

@section('content')

    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <section class="container locations-page">

        <header class="locations-page__header">

        <span class="locations-page__eyebrow">
            DAKEN Chocolate
        </span>

            <h1>
                {{ $isArabic ? 'أماكن البيع' : 'Where to Find Us' }}
            </h1>

            <p>
                {{ $isArabic
                    ? 'اكتشف الأماكن التي يمكنك العثور فيها على منتجات DAKEN.'
                    : 'Discover where you can find DAKEN products.' }}
            </p>

        </header>

        <form
            action="{{ route('locations.index') }}"
            method="GET"
            class="locations-filters"
            role="search"
        >

            <div class="locations-filters__field">

                <label for="location-search">
                    {{ $isArabic ? 'البحث' : 'Search' }}
                </label>

                <input
                    id="location-search"
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    maxlength="100"
                    placeholder="{{ $isArabic ? 'اسم المكان أو المدينة...' : 'Location name or city...' }}"
                >

            </div>

            <div class="locations-filters__field">

                <label for="location-city">
                    {{ $isArabic ? 'المدينة' : 'City' }}
                </label>

                <select
                    id="location-city"
                    name="city"
                >

                    <option value="">
                        {{ $isArabic ? 'جميع المدن' : 'All cities' }}
                    </option>

                    @foreach($cities as $cityName)
                        <option
                            value="{{ $cityName }}"
                            @selected($city === $cityName)
                        >
                            {{ $cityName }}
                        </option>
                    @endforeach

                </select>

            </div>

            <div class="locations-filters__actions">

                <button type="submit">
                    {{ $isArabic ? 'بحث' : 'Search' }}
                </button>

                <a href="{{ route('locations.index') }}">
                    {{ $isArabic ? 'إعادة ضبط' : 'Reset' }}
                </a>

            </div>

        </form>

        <div class="locations-page__results">
            {{ $isArabic ? 'عدد الأماكن:' : 'Locations:' }}

            <strong>{{ $locations->total() }}</strong>
        </div>

        @if($locations->isNotEmpty())

            <div class="locations-grid">

                @foreach($locations as $location)

                    <article class="location-card">

                        <div class="location-card__image">

                            <img
                                src="{{ $location->image_url }}"
                                alt="{{ $location->translated_name }}"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('images/location-placeholder.svg') }}';"
                            >

                        </div>

                        <div class="location-card__content">

                        <span class="location-card__city">
                            {{ $location->city }}
                        </span>

                            <h2>
                                {{ $location->translated_name }}
                            </h2>

                            @if($location->translated_address)
                                <p>
                                    {{ $location->translated_address }}
                                </p>
                            @endif

                            @if($location->phone)
                                <p class="location-card__phone">
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $location->phone) }}">
                                        {{ $location->phone }}
                                    </a>
                                </p>
                            @endif

                        </div>

                        <a
                            href="{{ $location->maps_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="location-card__map-link"
                        >
                            {{ $isArabic ? 'عرض على الخريطة' : 'View on Map' }}
                            <span aria-hidden="true">↗</span>
                        </a>

                    </article>

                @endforeach

            </div>

            <div class="locations-pagination">
                {{ $locations->links() }}
            </div>

        @else

            <div class="locations-empty">

                <h2>
                    {{ $isArabic
                        ? 'لا توجد أماكن مطابقة'
                        : 'No matching locations' }}
                </h2>

                <p>
                    {{ $isArabic
                        ? 'جرّب البحث باسم آخر أو اختيار مدينة مختلفة.'
                        : 'Try another search or select a different city.' }}
                </p>

                <a href="{{ route('locations.index') }}">
                    {{ $isArabic ? 'عرض جميع الأماكن' : 'View all locations' }}
                </a>

            </div>

        @endif

    </section>

@endsection
