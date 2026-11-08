
@extends('layouts.admin')

@section('content')
    <div class="daken-users-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="daken-users-heading">
            <div>
                <h1>إدارة المستخدمين</h1>
                <p>إضافة الموظفين وتحديد مستوياتهم وصلاحياتهم.</p>
            </div>

            <a href="{{ route('admin.users.create') }}" class="daken-users-btn">
                + إضافة مستخدم
            </a>
        </div>

        <div class="daken-users-card">
            <div class="daken-users-table-wrap">
                <table class="daken-users-table">
                    <thead>
                    <tr>
                        <th>المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>المستوى</th>
                        <th>الحالة</th>
                        <th>الصلاحيات</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if ($user->is_super_admin)
                                    <span class="daken-users-protected">
                                        المدير الرئيسي
                                    </span>
                                @endif
                            </td>

                            <td>{{ $user->email }}</td>

                            <td>{{ $user->admin_level }}</td>

                            <td>
                                {{ $user->is_active ? 'نشط' : 'معطل' }}
                            </td>

                            <td>
                                @if ($user->is_super_admin)
                                    جميع الصلاحيات
                                @else
                                    {{ count($user->admin_permissions ?? []) }} صلاحية
                                @endif
                            </td>

                            <td>
                                @can('update', $user)
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="daken-users-action">
                                        تعديل
                                    </a>
                                @endcan

                                @can('delete', $user)
                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user) }}"
                                          class="daken-users-delete-form"
                                          onsubmit="return confirm('هل تريد حذف هذا المستخدم نهائيًا؟');">
                                        @csrf
                                        @method('DELETE')

                                        @if ($user->is_active)
                                            <button type="submit" class="daken-users-danger">
                                                تعطيل الحساب
                                            </button>
                                        @endif                                    </form>
                                @endcan

                                @if ($user->is_super_admin)
                                    <span class="daken-users-protected">
                                        حساب محمي
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">لا يوجد مستخدمون.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="daken-users-pagination">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
