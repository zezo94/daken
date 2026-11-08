
@props([
    'active' => false,
])

<span @class([
    'admin-status',
    'admin-status--active' => $active,
    'admin-status--inactive' => !$active,
])>
    {{ $active
        ? (app()->getLocale() === 'ar' ? 'نشط' : 'Active')
        : (app()->getLocale() === 'ar' ? 'غير نشط' : 'Inactive') }}
</span>
