
@props([
    'name',
    'label',
    'value' => null,
    'rows' => 5,
    'required' => false,
    'dir' => null,
])

@php
    $inputId = $attributes->get('id', $name);
    $errorId = $inputId . '-error';
@endphp

<div class="admin-form-field">
    <label for="{{ $inputId }}">
        {{ $label }}

        @if($required)
            <span class="admin-required" aria-hidden="true">*</span>
        @endif
    </label>

    <textarea
        {{ $attributes->except(['id'])->class([
            'admin-form-control',
            'is-invalid' => $errors->has($name),
        ]) }}
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($required) required @endif
        @if($dir) dir="{{ $dir }}" @endif
        @if($errors->has($name))
            aria-invalid="true"
        aria-describedby="{{ $errorId }}"
        @endif
    >{{ old($name, $value) }}</textarea>

    @error($name)
    <small
        id="{{ $errorId }}"
        class="admin-field-error"
        role="alert"
    >
        {{ $message }}
    </small>
    @enderror
</div>
