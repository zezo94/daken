
@extends('layouts.admin')

@section('title', 'رسائل التواصل - DAKEN')

@section('content')

    <section class="container" style="padding-block: 60px;">

        <header style="margin-bottom: 30px;">
            <h1>رسائل التواصل</h1>

            <p style="color: #76685d; margin-top: 10px;">
                متابعة الرسائل الواردة من نموذج اتصل بنا.
            </p>
        </header>

        @if(session('success'))
            <div
                role="status"
                style="
                padding: 15px 20px;
                margin-bottom: 24px;
                border-radius: 12px;
                background: #e9f5e9;
                color: #225a2c;
            "
            >
                {{ session('success') }}
            </div>
        @endif

        @php
            $unreadCount = $messages->getCollection()
                ->where('is_read', false)
                ->count();
        @endphp

        <div
            style="
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        "
        >
            <div
                style="
                padding: 18px 24px;
                border: 1px solid #e9e0d7;
                border-radius: 14px;
                background: #fff;
            "
            >
            <span style="color: #76685d;">
                إجمالي الرسائل
            </span>

                <strong
                    style="
                    display: block;
                    margin-top: 6px;
                    font-size: 28px;
                    color: #2d1c15;
                "
                >
                    {{ $messages->total() }}
                </strong>
            </div>

            <div
                style="
                padding: 18px 24px;
                border: 1px solid #e9e0d7;
                border-radius: 14px;
                background: #fff;
            "
            >
            <span style="color: #76685d;">
                الرسائل الجديدة في هذه الصفحة
            </span>

                <strong
                    style="
                    display: block;
                    margin-top: 6px;
                    font-size: 28px;
                    color: #9b7049;
                "
                >
                    {{ $unreadCount }}
                </strong>
            </div>
        </div>

        <div
            style="
            overflow-x: auto;
            border: 1px solid #eee4da;
            border-radius: 16px;
            background: #fff;
        "
        >

            <table
                class="admin-table"
                style="width: 100%; border-collapse: collapse;"
            >
                <thead>
                <tr>
                    <th scope="col">المرسل</th>
                    <th scope="col">الموضوع</th>
                    <th scope="col">التاريخ</th>
                    <th scope="col">الحالة</th>
                    <th scope="col">الإجراءات</th>
                </tr>
                </thead>

                <tbody>

                @forelse($messages as $message)

                    <tr
                        style="{{ !$message->is_read
                            ? 'background: #fdf8f1;'
                            : '' }}"
                    >

                        <td>
                            <strong
                                style="
                                    display: block;
                                    color: #2d1c15;
                                "
                            >
                                {{ $message->name }}
                            </strong>

                            <small
                                style="
                                    display: block;
                                    margin-top: 5px;
                                    color: #76685d;
                                    direction: ltr;
                                    text-align: start;
                                "
                            >
                                {{ $message->email }}
                            </small>
                        </td>

                        <td>
                            <span
                                style="
                                    color: #45352b;
                                    font-weight: {{ $message->is_read ? '400' : '700' }};
                                    overflow-wrap: anywhere;
                                "
                            >
                                {{ $message->subject }}
                            </span>
                        </td>

                        <td>
                            <time
                                datetime="{{ $message->created_at->toIso8601String() }}"
                                style="white-space: nowrap; color: #76685d;"
                            >
                                {{ $message->created_at->format('Y-m-d H:i') }}
                            </time>
                        </td>

                        <td>
                            @if($message->is_read)

                                <span
                                    style="
                                        display: inline-block;
                                        padding: 6px 12px;
                                        border-radius: 20px;
                                        background: #eef1f3;
                                        color: #52616b;
                                        font-size: 13px;
                                        font-weight: 700;
                                    "
                                >
                                    مقروءة
                                </span>

                            @else

                                <span
                                    style="
                                        display: inline-block;
                                        padding: 6px 12px;
                                        border-radius: 20px;
                                        background: #fff0d9;
                                        color: #8b5719;
                                        font-size: 13px;
                                        font-weight: 700;
                                    "
                                >
                                    جديدة
                                </span>

                            @endif
                        </td>

                        <td>
                            <div
                                style="
                                    display: flex;
                                    align-items: center;
                                    gap: 10px;
                                    flex-wrap: wrap;
                                "
                            >

                                <a
                                    href="{{ route('admin.messages.show', $message) }}"
                                    style="
                                        display: inline-block;
                                        padding: 9px 15px;
                                        border-radius: 9px;
                                        background: #2d1c15;
                                        color: #fff;
                                        text-decoration: none;
                                        font-size: 13px;
                                        font-weight: 700;
                                    "
                                >
                                    عرض
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.messages.destroy', $message) }}"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة نهائيًا؟');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        style="
                                            padding: 9px 15px;
                                            border: 1px solid #e5c8c4;
                                            border-radius: 9px;
                                            background: #fff;
                                            color: #a12622;
                                            font: inherit;
                                            font-size: 13px;
                                            font-weight: 700;
                                            cursor: pointer;
                                        "
                                    >
                                        حذف
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="5"
                            style="
                                padding: 55px 24px;
                                text-align: center;
                                color: #76685d;
                            "
                        >
                            لا توجد رسائل تواصل حتى الآن.
                        </td>
                    </tr>

                @endforelse

                </tbody>
            </table>

        </div>

        @if($messages->hasPages())
            <div style="margin-top: 30px;">
                {{ $messages->links() }}
            </div>
        @endif

    </section>

@endsection
