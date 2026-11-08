
@extends('layouts.admin')

@section('title', 'إدارة التصنيفات - DAKEN')

@section('content')

    <x-admin.page-header
        title="إدارة التصنيفات"
        description="عرض التصنيفات والبحث عنها وتعديلها."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.categories.create') }}"
                class="admin-ui-button admin-ui-button--primary"
            >
                + إضافة تصنيف
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.card>

        <form
            action="{{ route('admin.categories.index') }}"
            method="GET"
            class="admin-list-filters"
        >
            <div class="admin-form-field">
                <label for="category-search">بحث</label>

                <input
                    id="category-search"
                    name="search"
                    type="search"
                    class="admin-form-control"
                    placeholder="اسم التصنيف أو Slug"
                    value="{{ $search }}"
                >
            </div>

            <div class="admin-form-field">
                <label for="category-status">الحالة</label>

                <select
                    id="category-status"
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
                href="{{ route('admin.categories.index') }}"
                class="admin-ui-button admin-ui-button--secondary"
            >
                إعادة تعيين
            </a>
        </form>

        @if($categories->count())

            <x-admin.table
                :headers="[
                'الصورة',
                'التصنيف',
                'Slug',
                'الحالة',
                'الترتيب',
                'الإجراءات'
            ]"
            >

                @foreach($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->image) }}"
                                    alt=""
                                    class="admin-table-image"
                                >
                            @else
                                <span class="admin-table-placeholder">
                                DAKEN
                            </span>
                            @endif
                        </td>

                        <td>
                            <strong>
                                {{ $category->name['ar'] ?? '' }}
                            </strong>

                            <div style="color:#887669;font-size:12px;margin-top:4px;">
                                {{ $category->name['en'] ?? '' }}
                            </div>
                        </td>

                        <td dir="ltr">
                            {{ $category->slug }}
                        </td>

                        <td>
                            <x-admin.status
                                :active="$category->is_active"
                            />
                        </td>

                        <td>
                            {{ $category->sort_order }}
                        </td>

                        <td>
                            <div class="admin-table-actions">

                                <a
                                    href="{{ route('admin.categories.edit', $category) }}"
                                    class="admin-ui-button admin-ui-button--secondary"
                                >
                                    تعديل
                                </a>


                                <form
                                    action="{{ route('admin.categories.destroy', $category) }}"
                                    method="POST"
                                    data-confirm
                                    data-confirm-title="حذف التصنيف"
                                    data-confirm-message="سيتم حذف التصنيف نهائيًا. هل تريد المتابعة؟"
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
                {{ $categories->links() }}
            </div>

        @else
            <div class="admin-empty-state">
                لا توجد تصنيفات مطابقة للبحث.
            </div>
        @endif

    </x-admin.card>

@endsection
