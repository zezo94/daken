
@props([
    'name',
    'label',
    'checked' => false,
])

@php
    $inputId = $attributes->get('id', $name);
    $isChecked = (bool) old($name, $checked);
@endphp

<div class="admin-checkbox-field">
    <input
        type="hidden"
        name="{{ $name }}"
        value="0"
    >

    <label for="{{ $inputId }}">
        <input
            {{ $attributes->except(['id']) }}
            id="{{ $inputId }}"
            type="checkbox"
            name="{{ $name }}"
            value="1"
            @checked($isChecked)
        >

        <span>{{ $label }}</span>
    </label>

    @error($name)
    <small class="admin-field-error" role="alert">
        {{ $message }}
    </small>
    @enderror
</div>
