
@extends('layouts.admin')

@section('title', 'DAKEN Activity Details')

@section('content')

    @php
        $ar = app()->getLocale() === 'ar';

        $actionLabels = [
            'admin.created' => ['إنشاء حساب مدير', 'Administrator Created'],
            'admin.updated' => ['تعديل حساب مدير', 'Administrator Updated'],
            'admin.deactivated' => ['تعطيل حساب مدير', 'Administrator Deactivated'],

            'product.created' => ['إضافة منتج', 'Product Created'],
            'product.updated' => ['تعديل منتج', 'Product Updated'],
            'product.deleted' => ['حذف منتج', 'Product Deleted'],

            'products.bulk_activated' => ['تفعيل منتجات', 'Bulk Activation'],
            'products.bulk_deactivated' => ['إلغاء تفعيل منتجات', 'Bulk Deactivation'],
            'products.bulk_deleted' => ['حذف منتجات', 'Bulk Deletion'],

            'category.created' => ['إضافة تصنيف', 'Category Created'],
            'category.updated' => ['تعديل تصنيف', 'Category Updated'],
            'category.deleted' => ['حذف تصنيف', 'Category Deleted'],
            'categories.bulk_deleted' => ['حذف تصنيفات', 'Bulk Category Deletion'],

            'location.created' => ['إضافة مكان بيع', 'Location Created'],
            'location.updated' => ['تعديل مكان بيع', 'Location Updated'],
            'location.deleted' => ['حذف مكان بيع', 'Location Deleted'],
        ];

        $labels = $actionLabels[$activity->action] ?? null;

        $actionLabel = $labels
            ? $labels[$ar ? 0 : 1]
            : $activity->action;

        $details = is_array($activity->details)
            ? $activity->details
            : [];

        $isAdminAction = in_array(
            $activity->action,
            [
                'admin.created',
                'admin.updated',
                'admin.deactivated',
            ],
            true
        );

        $name = data_get(
            $details,
            $ar ? 'name.ar' : 'name.en'
        ) ?? data_get(
            $details,
            $ar ? 'name.en' : 'name.ar'
        );

        if (! is_scalar($name)) {
            $name = null;
        }

        $count = data_get($details, 'count');

        $productIds = data_get($details, 'product_ids', []);
        $productIds = is_array($productIds) ? $productIds : [];

        $categoryIds = data_get($details, 'category_ids', []);
        $categoryIds = is_array($categoryIds) ? $categoryIds : [];

        $safePermissions = function ($value): array {
            if (! is_array($value)) {
                return [];
            }

            return array_values(
                array_filter(
                    $value,
                    fn ($permission) =>
                        is_string($permission)
                        && array_key_exists(
                            $permission,
                            \App\Models\User::PERMISSIONS
                        )
                )
            );
        };

        $before = data_get($details, 'before', []);
        $before = is_array($before) ? $before : [];

        $after = data_get($details, 'after', []);
        $after = is_array($after) ? $after : [];

        $permissionLabels = \App\Models\User::PERMISSIONS;

        $formatPermissions = function (array $permissions) use ($permissionLabels): string {
            if (empty($permissions)) {
                return '—';
            }

            return implode(
                ', ',
                array_map(
                    fn ($permission) => is_scalar($permissionLabels[$permission])
                        ? (string) $permissionLabels[$permission]
                        : $permission,
                    $permissions
                )
            );
        };

        $createdPermissions = $safePermissions(
            data_get($details, 'admin_permissions', [])
        );

        $beforePermissions = $safePermissions(
            data_get($before, 'admin_permissions', [])
        );

        $afterPermissions = $safePermissions(
            data_get($after, 'admin_permissions', [])
        );

        $statusLabel = function ($value) use ($ar): string {
            if ($value === null) {
                return '—';
            }

            return (bool) $value
                ? ($ar ? 'نشط' : 'Active')
                : ($ar ? 'معطل' : 'Inactive');
        };
    @endphp

    <div class="daken-activity-detail">

        <div class="daken-activity-detail__header">
            <div>
                <h1>
                    {{ $ar ? 'تفاصيل النشاط الإداري' : 'Activity Details' }}
                </h1>

                <p>
                    {{ $ar
                        ? 'عرض معلومات العملية المسجلة في لوحة الإدارة.'
                        : 'View recorded administrative action details.' }}
                </p>
            </div>

            <a
                href="{{ route('admin.activities.index') }}"
                class="admin-ui-button"
            >
                {{ $ar ? 'العودة إلى السجل' : 'Back to Activity Log' }}
            </a>
        </div>

        <div class="daken-activity-detail__card">

            <div class="daken-activity-detail__grid">

                <div class="daken-activity-detail__field">
                    <span>{{ $ar ? 'رقم السجل' : 'Record ID' }}</span>
                    <strong>#{{ $activity->id }}</strong>
                </div>

                <div class="daken-activity-detail__field">
                    <span>{{ $ar ? 'نوع العملية' : 'Action' }}</span>
                    <strong>{{ $actionLabel }}</strong>
                </div>

                <div class="daken-activity-detail__field">
                    <span>{{ $ar ? 'المسؤول' : 'Administrator' }}</span>
                    <strong>{{ $activity->user?->name ?? '—' }}</strong>
                </div>

                <div class="daken-activity-detail__field">
                    <span>{{ $ar ? 'التاريخ والوقت' : 'Date and Time' }}</span>
                    <strong>
                        {{ $activity->created_at?->format('Y-m-d H:i:s') ?? '—' }}
                    </strong>
                </div>

                <div class="daken-activity-detail__field">
                    <span>{{ $ar ? 'نوع العنصر' : 'Subject Type' }}</span>
                    <strong>
                        {{ $isAdminAction
                            ? ($ar ? 'حساب مدير' : 'Administrator Account')
                            : ($activity->subject_type ?? '—') }}
                    </strong>
                </div>

                <div class="daken-activity-detail__field">
                    <span>{{ $ar ? 'معرّف العنصر' : 'Subject ID' }}</span>
                    <strong>#{{ $activity->subject_id ?? '—' }}</strong>
                </div>

                <div class="daken-activity-detail__field">
                    <span>{{ $ar ? 'عنوان IP' : 'IP Address' }}</span>
                    <strong>{{ $activity->ip_address ?? '—' }}</strong>
                </div>

            </div>

            @if($activity->action === 'admin.created')

                <div class="daken-activity-detail__section">
                    <h2>
                        {{ $ar ? 'بيانات المدير الجديد' : 'New Administrator' }}
                    </h2>

                    <p>
                        {{ $ar ? 'المستوى الإداري' : 'Administrative Level' }}:
                        {{ data_get($details, 'admin_level', '—') }}
                    </p>

                    <p>
                        {{ $ar ? 'الحالة' : 'Status' }}:
                        {{ $statusLabel(data_get($details, 'is_active')) }}
                    </p>

                    <p>
                        {{ $ar ? 'الصلاحيات' : 'Permissions' }}:
                        {{ $formatPermissions($createdPermissions) }}
                    </p>
                </div>

            @elseif($activity->action === 'admin.updated')

                <div class="daken-activity-detail__section">
                    <h2>
                        {{ $ar ? 'قبل التعديل' : 'Before Update' }}
                    </h2>

                    <p>
                        {{ $ar ? 'المستوى الإداري' : 'Administrative Level' }}:
                        {{ data_get($before, 'admin_level', '—') }}
                    </p>

                    <p>
                        {{ $ar ? 'الحالة' : 'Status' }}:
                        {{ $statusLabel(data_get($before, 'is_active')) }}
                    </p>

                    <p>
                        {{ $ar ? 'الصلاحيات' : 'Permissions' }}:
                        {{ $formatPermissions($beforePermissions) }}
                    </p>
                </div>

                <div class="daken-activity-detail__section">
                    <h2>
                        {{ $ar ? 'بعد التعديل' : 'After Update' }}
                    </h2>

                    <p>
                        {{ $ar ? 'المستوى الإداري' : 'Administrative Level' }}:
                        {{ data_get($after, 'admin_level', '—') }}
                    </p>

                    <p>
                        {{ $ar ? 'الحالة' : 'Status' }}:
                        {{ $statusLabel(data_get($after, 'is_active')) }}
                    </p>

                    <p>
                        {{ $ar ? 'الصلاحيات' : 'Permissions' }}:
                        {{ $formatPermissions($afterPermissions) }}
                    </p>

                    <p>
                        {{ $ar ? 'تغيير كلمة المرور' : 'Password Changed' }}:
                        {{ data_get($details, 'password_changed') === true
                            ? ($ar ? 'نعم' : 'Yes')
                            : ($ar ? 'لا' : 'No') }}
                    </p>
                </div>

            @elseif($activity->action === 'admin.deactivated')

                <div class="daken-activity-detail__section">
                    <h2>
                        {{ $ar ? 'تعطيل الحساب' : 'Account Deactivation' }}
                    </h2>

                    <p>
                        {{ $ar ? 'الحالة السابقة' : 'Previous Status' }}:
                        {{ $statusLabel(data_get($details, 'was_active')) }}
                    </p>

                    <p>
                        {{ $ar ? 'الحالة الجديدة' : 'New Status' }}:
                        {{ $statusLabel(data_get($details, 'is_active')) }}
                    </p>
                </div>

            @else

                @if($name !== null)
                    <div class="daken-activity-detail__section">
                        <h2>{{ $ar ? 'اسم العنصر' : 'Subject Name' }}</h2>
                        <p>{{ $name }}</p>
                    </div>
                @endif

                @if($count !== null)
                    <div class="daken-activity-detail__section">
                        <h2>
                            {{ $ar ? 'عدد العناصر المتأثرة' : 'Affected Items' }}
                        </h2>
                        <p>{{ $count }}</p>
                    </div>
                @endif

                @if(count($productIds) > 0)
                    <div class="daken-activity-detail__section">
                        <h2>
                            {{ $ar ? 'معرّفات المنتجات' : 'Product IDs' }}
                        </h2>

                        <div class="daken-activity-detail__ids">
                            @foreach($productIds as $id)
                                @if(is_numeric($id))
                                    <span>#{{ $id }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($categoryIds) > 0)
                    <div class="daken-activity-detail__section">
                        <h2>
                            {{ $ar ? 'معرّفات التصنيفات' : 'Category IDs' }}
                        </h2>

                        <div class="daken-activity-detail__ids">
                            @foreach($categoryIds as $id)
                                @if(is_numeric($id))
                                    <span>#{{ $id }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

            @endif

        </div>

    </div>

@endsection
