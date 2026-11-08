
@php
    $ar = app()->getLocale() === 'ar';

    $actionLabels = [
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
@endphp

<section class="daken-recent-activities">

    <div class="daken-recent-activities__header">
        <div>
            <h2>
                {{ $ar ? 'آخر النشاطات الإدارية' : 'Recent Admin Activity' }}
            </h2>

            <p>
                {{ $ar
                    ? 'آخر خمس عمليات مسجلة في لوحة الإدارة.'
                    : 'The latest five recorded admin actions.' }}
            </p>
        </div>

        <a href="{{ route('admin.activities.index') }}">
            {{ $ar ? 'عرض السجل كاملًا' : 'View All Activity' }}
        </a>
    </div>

    <div class="daken-recent-activities__list">

        @forelse($recentActivities as $activity)
            @php
                $labels = $actionLabels[$activity->action] ?? null;

                $actionLabel = $labels
                    ? $labels[$ar ? 0 : 1]
                    : $activity->action;

                $name = data_get($activity->details, 'name.ar')
                    ?? data_get($activity->details, 'name.en');

                $count = data_get($activity->details, 'count');

                $subject = $name
                    ?? ($count !== null
                        ? ($ar ? 'عدد العناصر: ' : 'Items: ') . $count
                        : ($activity->subject_id
                            ? '#' . $activity->subject_id
                            : '—'));
            @endphp

            <div class="daken-recent-activities__item">

                <div class="daken-recent-activities__icon">
                    <span aria-hidden="true">◷</span>
                </div>

                <div class="daken-recent-activities__details">
                    <strong>{{ $actionLabel }}</strong>

                    <span>
                        {{ $subject }}
                    </span>

                    <small>
                        {{ $ar ? 'بواسطة' : 'By' }}
                        {{ $activity->user?->name ?? '—' }}
                    </small>
                </div>

                <time
                    datetime="{{ $activity->created_at->toIso8601String() }}"
                >
                    {{ $activity->created_at->diffForHumans() }}
                </time>

            </div>

        @empty

            <div class="daken-recent-activities__empty">
                {{ $ar
                    ? 'لا توجد نشاطات إدارية مسجلة حتى الآن.'
                    : 'No administrative activity recorded yet.' }}
            </div>

        @endforelse

    </div>
</section>
