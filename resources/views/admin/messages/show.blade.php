
@extends('layouts.admin')

@section('title', 'تفاصيل الرسالة - DAKEN')

@section('content')

    <section
        class="container"
        style="max-width: 900px; padding-block: 60px;"
    >

        <div style="margin-bottom: 24px;">

            <a
                href="{{ route('admin.messages.index') }}"
                style="
                color: #79512e;
                text-decoration: none;
                font-weight: 700;
            "
            >
                ← العودة إلى الرسائل
            </a>

        </div>

        <header style="margin-bottom: 30px;">

        <span
            style="
                display: inline-block;
                margin-bottom: 12px;
                padding: 7px 14px;
                border-radius: 20px;
                background: #e9f5e9;
                color: #225a2c;
                font-size: 13px;
                font-weight: 700;
            "
        >
            مقروءة
        </span>

            <h1
                style="
                margin: 0;
                color: #2d1c15;
                overflow-wrap: anywhere;
            "
            >
                {{ $message->subject }}
            </h1>

        </header>

        {{-- Sender Information --}}
        <div
            style="
            padding: 30px;
            margin-bottom: 24px;
            border: 1px solid #eee4da;
            border-radius: 18px;
            background: #fff;
        "
        >

            <h2 style="margin: 0 0 24px; font-size: 22px;">
                معلومات المرسل
            </h2>

            <dl
                style="
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 24px;
                margin: 0;
            "
            >

                <div>
                    <dt style="color: #76685d; margin-bottom: 8px;">
                        الاسم
                    </dt>

                    <dd style="margin: 0; font-weight: 700;">
                        {{ $message->name }}
                    </dd>
                </div>

                <div>
                    <dt style="color: #76685d; margin-bottom: 8px;">
                        البريد الإلكتروني
                    </dt>

                    <dd style="margin: 0; overflow-wrap: anywhere;">
                        <a
                            href="mailto:{{ $message->email }}"
                            style="color: #79512e;"
                        >
                            {{ $message->email }}
                        </a>
                    </dd>
                </div>

                <div>
                    <dt style="color: #76685d; margin-bottom: 8px;">
                        رقم الهاتف
                    </dt>

                    <dd style="margin: 0;">
                        @if($message->phone)
                            <a
                                href="tel:{{ preg_replace('/[^0-9+]/', '', $message->phone) }}"
                                style="color: #79512e;"
                            >
                                {{ $message->phone }}
                            </a>
                        @else
                            غير متوفر
                        @endif
                    </dd>
                </div>

                <div>
                    <dt style="color: #76685d; margin-bottom: 8px;">
                        تاريخ الإرسال
                    </dt>

                    <dd style="margin: 0;">
                        <time datetime="{{ $message->created_at->toIso8601String() }}">
                            {{ $message->created_at->format('Y-m-d H:i') }}
                        </time>
                    </dd>
                </div>

            </dl>

        </div>

        {{-- Message Body --}}
        <article
            style="
            padding: 30px;
            margin-bottom: 24px;
            border: 1px solid #eee4da;
            border-radius: 18px;
            background: #fff;
        "
        >

            <h2 style="margin: 0 0 22px; font-size: 22px;">
                نص الرسالة
            </h2>

            <div
                style="
                padding: 24px;
                border-radius: 12px;
                background: #fbf8f4;
                color: #45352b;
                line-height: 2;
                white-space: pre-wrap;
                overflow-wrap: anywhere;
            "
            >{{ $message->message }}</div>

        </article>

        {{-- Actions --}}
        <div
            style="
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        "
        >

            <a
                href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: ' . $message->subject) }}"
                style="
                display: inline-block;
                padding: 13px 22px;
                border-radius: 10px;
                background: #2d1c15;
                color: #fff;
                text-decoration: none;
                font-weight: 700;
            "
            >
                الرد عبر البريد
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
                    padding: 12px 22px;
                    border: 1px solid #e5c8c4;
                    border-radius: 10px;
                    background: #fff;
                    color: #a12622;
                    font: inherit;
                    font-weight: 700;
                    cursor: pointer;
                "
                >
                    حذف الرسالة
                </button>
            </form>

        </div>

    </section>

@endsection
