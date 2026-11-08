
@extends('layouts.admin')

@section('content')
    @php
        $editing = $user !== null;

        $selectedPermissions = old(
            'admin_permissions',
            $user?->admin_permissions ?? []
        );
    @endphp

    <div class="daken-users-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="daken-users-heading">
            <div>
                <h1>{{ $editing ? 'تعديل المستخدم' : 'إضافة مستخدم' }}</h1>
                <p>حدد بيانات الحساب ومستواه والصلاحيات المسموح بها.</p>
            </div>

            <a href="{{ route('admin.users.index') }}" class="daken-users-back">
                العودة للمستخدمين
            </a>
        </div>

        <form method="POST"
              action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}"
              class="daken-users-card daken-users-form">

            @csrf

            @if ($editing)
                @method('PUT')
            @endif

            <div class="daken-users-fields">
                <div>
                    <label for="name">الاسم</label>
                    <input id="name" name="name" type="text"
                           value="{{ old('name', $user?->name) }}"
                           required maxlength="255">
                    @error('name') <small class="daken-users-error">{{ $message }}</small> @enderror
                </div>

                <div>
                    <label for="email">البريد الإلكتروني</label>
                    <input id="email" name="email" type="email"
                           value="{{ old('email', $user?->email) }}"
                           required>
                    @error('email') <small class="daken-users-error">{{ $message }}</small> @enderror
                </div>

                <div>
                    <label for="password">
                        كلمة المرور
                        @if ($editing)
                            (اتركها فارغة للاحتفاظ بالحالية)
                        @endif
                    </label>
                    <input id="password" name="password"
                           type="password"
                           {{ $editing ? '' : 'required' }}
                           autocomplete="new-password">
                    @error('password') <small class="daken-users-error">{{ $message }}</small> @enderror
                </div>

                <div>
                    <label for="password_confirmation">تأكيد كلمة المرور</label>
                    <input id="password_confirmation"
                           name="password_confirmation"
                           type="password"
                           {{ $editing ? '' : 'required' }}
                           autocomplete="new-password">
                </div>

                <div>
                    <label for="admin_level">المستوى الإداري</label>
                    <select id="admin_level" name="admin_level" required>
                        @foreach ([0, 20, 50, 70, 90] as $level)
                            @if (auth()->user()->canAssignLevel($level))
                                <option value="{{ $level }}"
                                    @selected((int) old('admin_level', $user?->admin_level ?? 20) === $level)>
                                    {{ $level }}
                                    @if ($level === 70) — مدير مستخدمين @endif
                                    @if ($level === 50) — مدير قسم @endif
                                    @if ($level === 20) — محرر @endif
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('admin_level') <small class="daken-users-error">{{ $message }}</small> @enderror
                </div>

                <div>
                    <label for="is_active">حالة الحساب</label>
                    <select id="is_active" name="is_active">
                        <option value="1" @selected((string) old('is_active', $user?->is_active ?? 1) === '1')>
                            نشط
                        </option>
                        <option value="0" @selected((string) old('is_active', $user?->is_active ?? 1) === '0')>
                            معطل
                        </option>
                    </select>
                </div>
            </div>

            <h2 class="daken-users-subtitle">صلاحيات المستخدم</h2>

            <div class="daken-users-permissions">
                @foreach ($permissions as $key => $label)
                    @if (auth()->user()->hasAdminPermission($key))
                        <label class="daken-users-permission">
                            <input type="checkbox"
                                   name="admin_permissions[]"
                                   value="{{ $key }}"
                                @checked(in_array($key, $selectedPermissions, true))>
                            <span>{{ $label }}</span>
                        </label>
                    @endif
                @endforeach
            </div>

            @error('admin_permissions')
            <small class="daken-users-error">{{ $message }}</small>
            @enderror

            <div class="daken-users-actions">
                <button type="submit" class="daken-users-btn">
                    {{ $editing ? 'حفظ التعديلات' : 'إنشاء المستخدم' }}
                </button>

                <a href="{{ route('admin.users.index') }}" class="daken-users-back">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
@endsection
