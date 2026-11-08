@php
    $siteSettings = app(\App\Services\SiteSettings::class);
    $brandName = $siteSettings->get('brand_name', config('brand.name', 'DAKEN'));
    $logo = $siteSettings->get('logo');
    $contactEmail = $siteSettings->get('contact_email');
    $contactPhone = $siteSettings->get('contact_phone');
    $socialLinks = ['instagram_url' => 'Instagram', 'facebook_url' => 'Facebook', 'tiktok_url' => 'TikTok'];
    $navigationItems = collect(config('navigation.main', []))
        ->filter(fn ($item) => isset($item['route']) && \Illuminate\Support\Facades\Route::has($item['route']))
        ->values();
    $existingRoutes = $navigationItems->pluck('route')->all();
    $extraRoutes = collect([
        ['route' => 'about', 'ar' => 'من نحن', 'en' => 'About Us'],
        ['route' => 'vision', 'ar' => 'رؤيتنا', 'en' => 'Our Vision'],
        ['route' => 'gallery.index', 'ar' => 'معرض الصور', 'en' => 'Gallery'],
    ])->filter(fn ($item) => !in_array($item['route'], $existingRoutes, true)
        && \Illuminate\Support\Facades\Route::has($item['route']));
    $dynamicPages = \App\Models\SitePage::query()
        ->where('is_active', true)
        ->whereNotIn('key', ['about', 'vision'])
        ->orderBy('id')
        ->get();
    $locale = app()->getLocale();
@endphp
<footer class="site-footer daken-nav52-footer">
    <div class="container footer-inner daken-nav52-footer__inner">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="footer-logo" aria-label="{{ $brandName }}">
                @if($logo)<img src="{{ asset('storage/' . $logo) }}" alt="{{ $brandName }}">@else<strong>{{ $brandName }}</strong>@endif
            </a>
            <p class="footer-description">{{ $locale === 'ar' ? 'DAKEN — تجربة مميزة لعشاق الشوكولاتة.' : 'DAKEN — A distinctive experience for chocolate lovers.' }}</p>
        </div>
        <div class="footer-navigation">
            <h3>{{ $locale === 'ar' ? 'روابط سريعة' : 'Quick Links' }}</h3>
            <nav aria-label="{{ $locale === 'ar' ? 'روابط التذييل' : 'Footer navigation' }}">
                @foreach($navigationItems as $item)<a href="{{ route($item['route']) }}">{{ __($item['label']) }}</a>@endforeach
                @foreach($extraRoutes as $item)<a href="{{ route($item['route']) }}">{{ $item[$locale] ?? $item['en'] }}</a>@endforeach
                @foreach($dynamicPages as $page)<a href="{{ route('pages.show', ['key' => $page->key]) }}">{{ $page->translated_title }}</a>@endforeach
            </nav>
        </div>
        <div class="footer-contact">
            <h3>{{ $locale === 'ar' ? 'تواصل معنا' : 'Contact Us' }}</h3>
            @if($contactEmail)<a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif
            @if($contactPhone)<a href="tel:{{ $contactPhone }}">{{ $contactPhone }}</a>@endif
            @if(!$contactEmail && !$contactPhone)<p>{{ $locale === 'ar' ? 'تابعنا عبر حساباتنا الرسمية.' : 'Follow our official social accounts.' }}</p>@endif
        </div>
        <div class="footer-social">
            <h3>{{ $locale === 'ar' ? 'تابعنا' : 'Follow Us' }}</h3>
            <div class="footer-social__links">
                @foreach($socialLinks as $key => $label)
                    @if($url = $siteSettings->get($key))<a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>@endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="footer-bottom"><div class="container"><small>&copy; {{ date('Y') }} {{ $brandName }}. {{ $locale === 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</small></div></div>
</footer>
