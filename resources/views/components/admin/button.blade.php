
@props([
    'type' => 'button',
    'variant' => 'primary',
])

<button
    {{ $attributes->class([
        'admin-ui-button',
        'admin-ui-button--' . $variant,
    ]) }}
    type="{{ $type }}"
>
    {{ $slot }}
</button>
