
@extends('layouts.admin')

@section('content')
    <div class="daken-users-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="daken-users-heading">
            <div>
                <h1>إدارة الموظفين</h1>
                <p>إنشاء حسابات الموظفين الإداريين وإدارة مستوياتهم وصلاحياتهم وحالة حساباتهم.</p>
            </div>

            @can('create', \App\Models\User::class)
                <a href="{{ route('admin.users.create') }}" class="daken-users-btn">
                    + إضافة موظف
                </a>
            @endcan
        </div>

        <div class="daken-users-card">
            <div class="daken-users-table-wrap">
                <table class="daken-users-table">
                    <thead>
                    <tr>
                        <th>الموظف</th>
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
                                @if ($user->is_active)
                                    <span class="daken-users-status">نشط</span>
                                @else
                                    <span class="daken-users-protected">معطل</span>
                                @endif
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
                                        @if ($user->is_active)
                                            <form method="POST"
                                                  action="{{ route('admin.users.destroy', $user) }}"
                                                  class="daken-users-delete-form"
                                                  onsubmit="return confirm('هل تريد تعطيل حساب هذا الموظف؟ سيتم منعه من الدخول مع الاحتفاظ ببياناته وسجل نشاطه.');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="daken-users-danger">
                                                    تعطيل الحساب
                                                </button>
                                            </form>
                                        @endif
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
                            <td colspan="6">لا توجد حسابات موظفين لعرضها.</td>
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
