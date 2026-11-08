
@php
    $messages = [
        'success' => session('success'),
        'error' => session('error'),
    ];
@endphp

<div
    class="admin-flash-container"
    aria-live="polite"
>
    @foreach($messages as $type => $message)
        @if($message)
            <div
                class="admin-flash admin-flash--{{ $type }}"
                role="{{ $type === 'error' ? 'alert' : 'status' }}"
                data-admin-flash
            >
                <div class="admin-flash__content">
                    <span class="admin-flash__icon" aria-hidden="true">
                        {{ $type === 'success' ? '✓' : '!' }}
                    </span>

                    <span>{{ $message }}</span>
                </div>

                <button
                    type="button"
                    class="admin-flash__close"
                    data-flash-close
                    aria-label="إغلاق الإشعار"
                >
                    &times;
                </button>
            </div>
        @endif
    @endforeach
</div>
