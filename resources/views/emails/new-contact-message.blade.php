
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>رسالة جديدة - DAKEN</title>
</head>

<body style="margin:0;padding:30px;background:#F8F5F0;font-family:Tahoma,Arial,sans-serif;color:#21130F;">

<div style="max-width:650px;margin:auto;background:#FFFDF9;border:1px solid #E3D5C2;border-radius:14px;overflow:hidden;">

    <div style="padding:28px;background:#21130F;text-align:center;">
        <h1 style="margin:0;color:#D6A64D;">
            DAKEN
        </h1>

        <p style="color:#F7EBDD;margin-bottom:0;">
            إشعار برسالة تواصل جديدة
        </p>
    </div>

    <div style="padding:28px;">

        <h2 style="color:#67411F;">
            وصلت رسالة جديدة من الموقع
        </h2>

        <p>
            <strong>اسم المرسل:</strong>
            {{ $senderName }}
        </p>

        <p>
            <strong>البريد الإلكتروني:</strong>
            {{ $senderEmail }}
        </p>
        <p>
            <strong>رقم الهاتف:</strong>

            @if ($senderPhone !== '')
                <span dir="ltr">
            {{ $senderPhone }}
        </span>
            @else
                غير متوفر
            @endif
        </p>

        <p>
            <strong>الموضوع:</strong>
            {{ $messageSubject }}
        </p>

        <div style="margin-top:22px;padding:18px;background:#F7EBDD;border-radius:9px;">

            <strong>محتوى الرسالة:</strong>

            <p style="white-space:pre-wrap;line-height:1.9;overflow-wrap:anywhere;">{{ $messageBody }}</p>

        </div>

        @if ($adminUrl)
            <p style="margin-top:28px;">
                <a
                    href="{{ $adminUrl }}"
                    style="display:inline-block;background:#D6A64D;color:#21130F;padding:13px 22px;border-radius:8px;text-decoration:none;font-weight:bold;"
                >
                    عرض الرسالة في لوحة الإدارة
                </a>
            </p>
        @endif

    </div>

    <div style="padding:18px;text-align:center;background:#F7EBDD;color:#67411F;font-size:12px;">
        DAKEN Chocolate — Website Notifications
    </div>

</div>

</body>
</html>
