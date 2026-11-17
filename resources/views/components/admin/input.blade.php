
@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'dir' => null,
])

@php
    $inputId = $attributes->get('id', $name);
    $errorId = $inputId . '-error';
    $fieldKey = str_replace(['[', ']'], ['.', ''], $name);
@endphp

<div class="admin-form-field">
    <label for="{{ $inputId }}">
        {{ $label }}

        @if($required)
            <span class="admin-required" aria-hidden="true">*</span>
        @endif
    </label>

    <input
        {{ $attributes->except(['id'])->class([
     'admin-form-control',
     'is-invalid' => $errors->has($fieldKey),
 ]) }}
        id="{{ $inputId }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($fieldKey, $value) }}"
        @if($required) required @endif
        @if($dir) dir="{{ $dir }}" @endif
        @if($errors->has($fieldKey))
            aria-invalid="true"
        aria-describedby="{{ $errorId }}"
        @endif
    >

    @error($fieldKey)
    <small
        id="{{ $errorId }}"
        class="admin-field-error"
        role="alert"
    >
        {{ $message }}
    </small>
    @enderror
</div>
