
@extends('layouts.admin')

@section('title', 'DAKEN Activity Log')

@section('content')

    @php
        $ar = app()->getLocale() === 'ar';
        $actionLabels = [


'admin.created' => [
    'إنشاء حساب مدير',
    'Administrator Created',
],
'admin.updated' => [
    'تعديل حساب مدير',
    'Administrator Updated',
],
'admin.deactivated' => [
    'تعطيل حساب مدير',
    'Administrator Deactivated',
],

            'product.created' => ['إضافة منتج', 'Product Created'],
            'product.updated' => ['تعديل منتج', 'Product Updated'],
            'product.deleted' => ['حذف منتج', 'Product Deleted'],
            'products.bulk_activated' => ['تفعيل جماعي', 'Bulk Activation'],
            'products.bulk_deactivated' => ['إلغاء تفعيل جماعي', 'Bulk Deactivation'],
            'products.bulk_deleted' => ['حذف جماعي', 'Bulk Deletion'],
            'category.created' => ['إضافة تصنيف', 'Category Created'],
            'category.updated' => ['تعديل تصنيف', 'Category Updated'],
            'category.deleted' => ['حذف تصنيف', 'Category Deleted'],
            'categories.bulk_deleted' => ['حذف تصنيفات جماعي', 'Bulk Category Deletion'],
            'location.created' => ['إضافة مكان بيع', 'Location Created'],
            'location.updated' => ['تعديل مكان بيع', 'Location Updated'],
            'location.deleted' => ['حذف مكان بيع', 'Location Deleted'],
        ];
    @endphp

    <x-admin.page-header
        :title="$ar ? 'سجل النشاط' : 'Activity Log'"
        :description="$ar
        ? 'متابعة العمليات التي ينفذها مسؤولو الموقع.'
        : 'Track administrative actions on the website.'"
    />




    <div class="daken-activity-stats">

        <div class="daken-activity-stat">
        <span>
{{ $ar ? 'العمليات المطابقة للفلاتر' : 'Matching Activities' }}
        </span>
            <strong>{{ number_format($statistics['total']) }}</strong>
        </div>

        <div class="daken-activity-stat">
        <span>
{{ $ar ? 'عمليات اليوم المطابقة' : 'Matching Today' }}
        </span>
            <strong>{{ number_format($statistics['today']) }}</strong>
        </div>

        <div class="daken-activity-stat">
        <span>
{{ $ar ? 'آخر 7 أيام مطابقة' : 'Matching Last 7 Days' }}
        </span>
            <strong>{{ number_format($statistics['last_7_days']) }}</strong>
        </div>

        <div class="daken-activity-stat">
        <span>
            {{ $ar ? 'عمليات المنتجات' : 'Product Actions' }}
        </span>
            <strong>{{ number_format($statistics['product_actions']) }}</strong>
        </div>

    </div>

    <x-admin.card>

        <form
            method="GET"
            action="{{ route('admin.activities.index') }}"
            class="admin-list-filters"
        >
            <div class="admin-form-field">
                <label for="activity-action">
                    {{ $ar ? 'نوع العملية' : 'Action Type' }}
                </label>

                <select
                    id="activity-action"
                    name="action"
                    class="admin-form-control"
                >
                    <option value="">
                        {{ $ar ? 'جميع العمليات' : 'All Actions' }}
                    </option>


                    @foreach($actionLabels as $value => $labels)
                        <option value="{{ $value }}" @selected($action === $value)>
                            {{ $labels[$ar ? 0 : 1] }}
                        </option>
                    @endforeach
                </select>


            </div>

            <div class="admin-form-field">
                <label for="activity-user">
                    {{ $ar ? 'المسؤول' : 'Administrator' }}
                </label>

                <select
                    id="activity-user"
                    name="user_id"
                    class="admin-form-control"
                >
                    <option value="">
                        {{ $ar ? 'جميع المسؤولين' : 'All Administrators' }}
                    </option>

                    @foreach($users as $user)
                        <option
                            value="{{ $user->id }}"
                            @selected((string) $userId === (string) $user->id)
                        >
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-field">
                <label for="activity-date-from">
                    {{ $ar ? 'من تاريخ' : 'From Date' }}
                </label>

                <input
                    type="date"
                    id="activity-date-from"
                    name="date_from"
                    value="{{ $dateFrom }}"
                    class="admin-form-control"
                >
            </div>

            <div class="admin-form-field">
                <label for="activity-date-to">
                    {{ $ar ? 'إلى تاريخ' : 'To Date' }}
                </label>

                <input
                    type="date"
                    id="activity-date-to"
                    name="date_to"
                    value="{{ $dateTo }}"
                    class="admin-form-control"
                >
            </div>

            <button
                type="submit"
                class="admin-ui-button admin-ui-button--primary"
            >
                {{ $ar ? 'تصفية' : 'Filter' }}
            </button>
        </form>

        <div class="daken-activity-actions">
            <a
                href="{{ route('admin.activities.export', request()->only([
            'action',
            'user_id',
            'date_from',
            'date_to'
        ])) }}"
                class="admin-ui-button admin-ui-button--primary"
            >
                {{ $ar ? 'تصدير CSV' : 'Export CSV' }}
            </a>

            <a
                href="{{ route('admin.activities.index') }}"
                class="admin-ui-button"
            >
                {{ $ar ? 'إعادة ضبط الفلاتر' : 'Reset Filters' }}
            </a>
        </div>

        <div class="daken-activity-results">
    <span>
        {{ $ar ? 'عدد النتائج:' : 'Results:' }}
        <strong>{{ number_format($activities->total()) }}</strong>
    </span>

            <span>
        {{ $ar ? 'الصفحة' : 'Page' }}
                {{ $activities->currentPage() }}
                {{ $ar ? 'من' : 'of' }}
                {{ $activities->lastPage() }}
    </span>
        </div>

        <div class="admin-dashboard__table-wrap">
            <table class="admin-dashboard__table">
                <thead>
                <tr>
                    <th>{{ $ar ? 'المستخدم' : 'User' }}</th>
                    <th>{{ $ar ? 'العملية' : 'Action' }}</th>
                    <th>{{ $ar ? 'العنصر' : 'Subject' }}</th>
                    <th>{{ $ar ? 'التاريخ' : 'Date' }}</th>
                    <th>{{ $ar ? 'التفاصيل' : 'Details' }}</th>
                </tr>
                </thead>

                <tbody>
                @forelse($activities as $activity)
                    <tr>
                        <td>
                            {{ $activity->user?->name ?? '—' }}
                        </td>

                        <td>
                            {{ ($actionLabels[$activity->action] ?? null)[$ar ? 0 : 1] ?? $activity->action }}
                        </td>


                        <td>

                            @if(str_starts_with($activity->action, 'admin.'))
                                {{ $ar ? 'حساب مدير' : 'Administrator' }}
                                #{{ $activity->subject_id ?? '—' }}

                            @elseif(str_starts_with($activity->action, 'products.bulk_'))
                                {{ $ar ? 'عدد المنتجات:' : 'Products:' }}
                                {{ data_get($activity->details, 'count', 0) }}

                            @elseif($activity->action === 'categories.bulk_deleted')
                                {{ $ar ? 'التصنيفات المحذوفة:' : 'Deleted categories:' }}
                                {{ data_get($activity->details, 'deleted_count', 0) }}

                            @else
                                {{ data_get($activity->details, 'name.ar')
                                    ?? data_get($activity->details, 'name.en')
                                    ?? ('#' . ($activity->subject_id ?? '—')) }}
                            @endif

                        </td>


                        <td>
                            {{ $activity->created_at->format('Y-m-d H:i') }}
                        </td>

                        <td>
                            <a
                                href="{{ route('admin.activities.show', $activity) }}"
                                class="daken-activity-detail-link"
                            >
                                {{ $ar ? 'عرض التفاصيل' : 'View Details' }}
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            {{ $ar
                                ? 'لا توجد عمليات مسجلة.'
                                : 'No activities recorded.' }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $activities->links() }}
        </div>

    </x-admin.card>

@endsection
