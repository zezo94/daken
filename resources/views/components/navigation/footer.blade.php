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
            @if($contactEmail)
                <a class="daken-footer-luxury__contact-link" href="mailto:{{ $contactEmail }}">
                    <svg class="daken-footer-luxury__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="m3 7 9 6 9-6"/>
                    </svg>
                    <span>{{ $contactEmail }}</span>
                </a>
            @endif
            @if($contactPhone)
                <a class="daken-footer-luxury__contact-link" href="tel:{{ $contactPhone }}">
                    <svg class="daken-footer-luxury__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7l.5 3a2 2 0 0 1-.6 1.7L7.1 10a16 16 0 0 0 6.9 6.9l1.6-1.9a2 2 0 0 1 1.7-.6l3 .5a2 2 0 0 1 1.7 2Z"/>
                    </svg>
                    <span dir="ltr">{{ $contactPhone }}</span>
                </a>
            @endif
            @if(!$contactEmail && !$contactPhone)<p>{{ $locale === 'ar' ? 'تابعنا عبر حساباتنا الرسمية.' : 'Follow our official social accounts.' }}</p>@endif
        </div>
        <div class="footer-social">
            <h3>{{ $locale === 'ar' ? 'تابعنا' : 'Follow Us' }}</h3>
            <div class="footer-social__links">
                @foreach($socialLinks as $key => $label)
                    @if($url = $siteSettings->get($key))
                        <a class="daken-footer-luxury__social-link"
                           href="{{ $url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="{{ $label }}"
                           title="{{ $label }}">
                            @switch($key)
                                @case('instagram_url')
                                    <svg class="daken-footer-luxury__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="2" y="2" width="20" height="20" rx="5"/>
                                        <circle cx="12" cy="12" r="4"/>
                                        <circle cx="18" cy="6" r="1" fill="currentColor" stroke="none"/>
                                    </svg>
                                    @break
                                @case('facebook_url')
                                    <svg class="daken-footer-luxury__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.7-1.6 1.6V12h2.8l-.5 2.9h-2.3v7A10 10 0 0 0 22 12Z"/>
                                    </svg>
                                    @break
                                @case('tiktok_url')
                                    <svg class="daken-footer-luxury__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M16.7 2h-3.1v13.1a3 3 0 1 1-2.6-3v-3.2a6.2 6.2 0 1 0 5.7 6.2V8.4a8 8 0 0 0 4.8 1.5V6.7A4.9 4.9 0 0 1 16.7 2Z"/>
                                    </svg>
                                    @break
                            @endswitch
                            <span class="daken-footer-luxury__social-label">{{ $label }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="footer-bottom"><div class="container"><small>&copy; {{ date('Y') }} {{ $brandName }}. {{ $locale === 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</small></div></div>
</footer>
