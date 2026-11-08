
@extends('layouts.admin')

@section('title', 'أماكن البيع - DAKEN')

@section('content')

    <x-admin.page-header
        title="أماكن البيع"
        description="إدارة مواقع وفروع توزيع منتجات DAKEN."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.locations.create') }}"
                class="admin-ui-button admin-ui-button--primary"
            >
                + إضافة مكان
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.card>

        <form
            action="{{ route('admin.locations.index') }}"
            method="GET"
            class="admin-list-filters"
        >
            <div class="admin-form-field">
                <label for="location-search">بحث</label>

                <input
                    id="location-search"
                    type="search"
                    name="search"
                    class="admin-form-control"
                    value="{{ $search }}"
                    placeholder="اسم المكان أو المدينة"
                >
            </div>

            <div class="admin-form-field">
                <label for="location-status">الحالة</label>

                <select
                    id="location-status"
                    name="status"
                    class="admin-form-control"
                >
                    <option value="">جميع الحالات</option>
                    <option value="1" @selected((string) $status === '1')>
                        نشط
                    </option>
                    <option value="0" @selected((string) $status === '0')>
                        غير نشط
                    </option>
                </select>
            </div>

            <button
                type="submit"
                class="admin-ui-button admin-ui-button--primary"
            >
                بحث
            </button>

            <a
                href="{{ route('admin.locations.index') }}"
                class="admin-ui-button admin-ui-button--secondary"
            >
                إعادة تعيين
            </a>
        </form>

        @if($locations->count())

            <x-admin.table
                :headers="[
                'المكان',
                'المدينة',
                'الهاتف',
                'الحالة',
                'الإجراءات'
            ]"
            >

                @foreach($locations as $location)
                    <tr>
                        <td>
                            <strong>
                                {{ $location->name['ar'] ?? '' }}
                            </strong>

                            <div style="color:#887669;font-size:12px;margin-top:4px;">
                                {{ $location->name['en'] ?? '' }}
                            </div>
                        </td>

                        <td>
                            {{ $location->city }}
                        </td>

                        <td dir="ltr">
                            {{ $location->phone ?: '—' }}
                        </td>

                        <td>
                            <x-admin.status
                                :active="$location->is_active"
                            />
                        </td>

                        <td>
                            <div class="admin-table-actions">

                                <a
                                    href="{{ route('admin.locations.edit', $location) }}"
                                    class="admin-ui-button admin-ui-button--secondary"
                                >
                                    تعديل
                                </a>

                                <form
                                    action="{{ route('admin.locations.destroy', $location) }}"
                                    method="POST"
                                    onsubmit="return confirm('هل تريد حذف هذا المكان؟');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="admin-ui-button admin-ui-button--danger"
                                    >
                                        حذف
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach

            </x-admin.table>

            <div class="admin-pagination">
                {{ $locations->links() }}
            </div>

        @else
            <div class="admin-empty-state">
                لا توجد أماكن مطابقة للبحث.
            </div>
        @endif

    </x-admin.card>

@endsection
