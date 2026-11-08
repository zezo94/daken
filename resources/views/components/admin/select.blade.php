
@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'required' => false,
])

@php
    $inputId = $attributes->get('id', str_replace(['[', ']'], ['_', ''], $name));
    $errorId = $inputId . '-error';
    $selectedValue = old($name, $value);
@endphp

<div class="admin-form-field">
    <label for="{{ $inputId }}">
        {{ $label }}

        @if($required)
            <span class="admin-required" aria-hidden="true">*</span>
        @endif
    </label>

    <select
        {{ $attributes->except(['id'])->class([
            'admin-form-control',
            'is-invalid' => $errors->has($name),
        ]) }}
        id="{{ $inputId }}"
        name="{{ $name }}"
        @if($required) required @endif
        @if($errors->has($name))
            aria-invalid="true"
        aria-describedby="{{ $errorId }}"
        @endif
    >
        @if($placeholder !== null)
            <option value="">
                {{ $placeholder }}
            </option>
        @endif

        @foreach($options as $optionValue => $optionLabel)
            <option
                value="{{ $optionValue }}"
                @selected((string) $selectedValue === (string) $optionValue)
            >
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

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
