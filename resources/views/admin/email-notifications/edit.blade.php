
@extends('layouts.admin')

@section('title', 'إشعارات البريد الإلكتروني - DAKEN')

@section('content')

    <div class="daken-email-settings">

        <div class="daken-email-settings__heading">
            <h1>إشعارات البريد الإلكتروني</h1>

            <p>
                حدد عناوين البريد الإلكتروني التي تستقبل
                إشعارًا عند وصول رسالة جديدة من العملاء.
            </p>
        </div>

        @if (session('success'))
            <div class="daken-email-settings__success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="daken-email-settings__errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('admin.email-notifications.update') }}"
            method="POST"
            class="daken-email-settings__card"
        >
            @csrf
            @method('PUT')

            <h2>إعدادات إشعارات الرسائل</h2>

            <div class="daken-email-settings__field">
                <label class="daken-email-settings__toggle">
                    <input type="hidden" name="enabled" value="0">

                    <input
                        type="checkbox"
                        name="enabled"
                        value="1"
                        {{ old('enabled', $enabled ? '1' : '0') == '1'
                            ? 'checked'
                            : '' }}
                    >

                    <span>تفعيل إرسال الإشعارات</span>
                </label>

                <small>
                    عند التفعيل، سيتم إرسال إشعار عند
                    استقبال رسالة جديدة.
                </small>
            </div>

            <div class="daken-email-settings__field">
                <label for="notification-emails">
                    عناوين البريد الإلكتروني للمستلمين
                </label>

                <textarea
                    id="notification-emails"
                    name="emails"
                    rows="8"
                    dir="ltr"
                    placeholder="admin@example.com&#10;manager@example.com&#10;support@example.com"
                >{{ old('emails', $emails) }}</textarea>

                <small>
                    أدخل كل عنوان في سطر مستقل.
                    يمكنك إضافة حتى 20 عنوانًا.
                </small>
            </div>

            <button
                type="submit"
                class="daken-email-settings__save"
            >
                حفظ إعدادات الإشعارات
            </button>
        </form>

    </div>

@endsection
