
@php
    $isArabic = app()->getLocale() === 'ar';

    $adminLinks = [
        [
            'label' => $isArabic ? 'لوحة التحكم' : 'Dashboard',
            'route' => 'admin.dashboard',
            'active' => 'admin.dashboard',
            'icon' => '▦',
            'permission' => null,
        ],
        [
            'label' => $isArabic ? 'التحليلات' : 'Analytics',
            'route' => 'admin.analytics.index',
            'active' => 'admin.analytics.*',
            'icon' => '▥',
            'permission' => 'analytics.view',
        ],
        [
            'label' => $isArabic ? 'المنتجات' : 'Products',
            'route' => 'admin.products.index',
            'active' => 'admin.products.*',
            'icon' => '◆',
            'permission' => 'products.manage',
        ],
        [
            'label' => $isArabic ? 'التصنيفات' : 'Categories',
            'route' => 'admin.categories.index',
            'active' => 'admin.categories.*',
            'icon' => '◈',
            'permission' => 'categories.manage',
        ],
        [
            'label' => $isArabic ? 'أماكن البيع' : 'Locations',
            'route' => 'admin.locations.index',
            'active' => 'admin.locations.*',
            'icon' => '⌖',
            'permission' => 'locations.manage',
        ],
        [
            'label' => $isArabic ? 'المعرض' : 'Gallery',
            'route' => 'admin.gallery.index',
            'active' => 'admin.gallery.*',
            'icon' => '▧',
            'permission' => 'gallery.manage',
        ],
        [
            'label' => $isArabic ? 'رسائل التواصل' : 'Messages',
            'route' => 'admin.messages.index',
            'active' => 'admin.messages.*',
            'icon' => '✉',
            'permission' => 'messages.manage',
        ],
        [
            'label' => $isArabic ? 'صفحات الموقع' : 'Pages',
            'route' => 'admin.pages.index',
            'active' => 'admin.pages.*',
            'icon' => '▤',
            'permission' => 'pages.manage',
        ],
        [
            'label' => $isArabic ? 'سجل النشاط' : 'Activity Log',
            'route' => 'admin.activities.index',
            'active' => 'admin.activities.*',
            'icon' => '◷',
            'permission' => 'activities.view',
        ],
        [
            'label' => $isArabic ? 'إشعارات البريد' : 'Email Notifications',
            'route' => 'admin.email-notifications.edit',
            'active' => 'admin.email-notifications.*',
            'icon' => '✉',
            'permission' => 'email-notifications.manage',
        ],
        [
            'label' => $isArabic ? 'الصفحة الرئيسية' : 'Homepage',
            'route' => 'admin.homepage.edit',
            'active' => 'admin.homepage.*',
            'icon' => '⌂',
            'permission' => 'homepage.manage',
        ],
        [
            'label' => $isArabic ? 'القائمة والشعار' : 'Navigation & Logo',
            'route' => 'admin.navigation.edit',
            'active' => 'admin.navigation.*',
            'icon' => '☷',
            'permission' => 'navigation.manage',
        ],
        [
            'label' => $isArabic ? 'إعدادات الموقع' : 'Settings',
            'route' => 'admin.settings.edit',
            'active' => 'admin.settings.*',
            'icon' => '⚙',
            'permission' => 'settings.manage',
        ],
        [
            'label' => $isArabic ? 'حالة النظام' : 'System Health',
            'route' => 'admin.system-health.index',
            'active' => 'admin.system-health.*',
            'icon' => '◉',
            'permission' => 'system-health.view',
        ],
        [
            'label' => $isArabic ? 'إدارة الموظفين' : 'Staff Management',
            'route' => 'admin.users.index',
            'active' => 'admin.users.*',
            'icon' => '👥',
            'permission' => 'users.manage',
        ],
    ];

    $currentUser = auth()->user();

    $adminLinks = array_values(array_filter(
        $adminLinks,
        fn (array $link): bool =>
            \Illuminate\Support\Facades\Route::has($link['route'])
            && (
                $link['permission'] === null
                || $currentUser?->hasAdminPermission($link['permission'])
            )
    ));
@endphp

<aside
    id="admin-sidebar"
    class="daken-admin-sidebar"
    aria-label="{{ $isArabic ? 'قائمة الإدارة' : 'Admin navigation' }}"
>
    {{-- Brand --}}
    <a
        href="{{ route('admin.dashboard') }}"
        class="daken-admin-sidebar__brand"
    >
        <span class="daken-admin-sidebar__logo">
            D
        </span>

        <span>
            <strong>DAKEN</strong>
            <small>ADMIN PANEL</small>
        </span>
    </a>

    {{-- Section title --}}
    <div class="daken-admin-sidebar__label">
        {{ $isArabic ? 'إدارة المحتوى' : 'CONTENT MANAGEMENT' }}
    </div>

    {{-- Navigation --}}
    <nav class="daken-admin-sidebar__nav">
        @foreach ($adminLinks as $link)
            @php
                $isActive = request()->routeIs($link['active']);
            @endphp

            <a
                href="{{ route($link['route']) }}"
                class="daken-admin-sidebar__link {{ $isActive ? 'is-active' : '' }}"
                @if ($isActive)
                    aria-current="page"
                @endif
            >
                <span
                    class="daken-admin-sidebar__icon"
                    aria-hidden="true"
                >
                    {{ $link['icon'] }}
                </span>

                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="daken-admin-sidebar__bottom">
        <a
            href="{{ route('home') }}"
            target="_blank"
            rel="noopener noreferrer"
        >
            {{ $isArabic ? 'عرض الموقع' : 'View Website' }}
            ↗
        </a>
    </div>
</aside>
