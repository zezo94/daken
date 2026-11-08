
@props([
    'name' => 'image',
    'label' => 'صورة',
    'currentImage' => null,
    'required' => false,
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

    <input
        {{ $attributes->except(['id'])->class([
            'admin-form-control',
            'is-invalid' => $errors->has($name),
        ]) }}
        id="{{ $inputId }}"
        type="file"
        name="{{ $name }}"
        accept=".jpg,.jpeg,.png,.webp"
        @if($required) required @endif
        @if($errors->has($name))
            aria-invalid="true"
        aria-describedby="{{ $errorId }}"
        @endif
    >

    @error($name)
    <small
        id="{{ $errorId }}"
        class="admin-field-error"
        role="alert"
    >
        {{ $message }}
    </small>
    @enderror

    @if($currentImage)
        <div class="admin-image-preview">
            <img
                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($currentImage) }}"
                alt="{{ $label }}"
            >

            <small>
                {{ app()->getLocale() === 'ar'
                    ? 'الصورة الحالية'
                    : 'Current image' }}
            </small>
        </div>
    @endif
</div>
