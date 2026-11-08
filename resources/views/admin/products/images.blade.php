
@extends('layouts.admin')

@section('title', 'صور المنتج - DAKEN')

@section('content')

    <x-admin.page-header
        title="معرض صور المنتج"
        description="رفع الصور وتحديد الصورة الرئيسية وترتيبها."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.products.edit', $product) }}"
                class="admin-ui-button admin-ui-button--secondary"
            >
                العودة إلى المنتج
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.card title="رفع صور جديدة">

        <form
            action="{{ route('admin.products.images.store', $product) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="admin-form-field">
                <label for="product-gallery-images">
                    اختر صور المنتج
                </label>

                <input
                    id="product-gallery-images"
                    type="file"
                    name="images[]"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    required
                    class="admin-form-control"
                >

                <small>
                    يمكنك رفع حتى 10 صور في كل عملية.
                    الحد الأقصى 4 ميجابايت للصورة.
                </small>

                @error('images')
                <div class="admin-field-error">
                    {{ $message }}
                </div>
                @enderror

                @error('images.*')
                <div class="admin-field-error">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="admin-form-actions">
                <button
                    type="submit"
                    class="admin-ui-button admin-ui-button--primary"
                >
                    رفع الصور
                </button>
            </div>
        </form>

    </x-admin.card>

    <div style="height:24px;"></div>

    <x-admin.card title="الصور الحالية">

        @if($product->images->isEmpty())

            <div class="admin-empty-state">
                لم تتم إضافة صور إلى المعرض بعد.
            </div>

        @else

            <div class="admin-gallery-grid">

                @foreach($product->images as $image)

                    <div class="admin-gallery-item">

                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                            alt="صورة {{ $loop->iteration }} من المنتج"
                            loading="lazy"
                        >

                        @if($image->is_primary)
                            <span class="admin-gallery-primary">
                            الصورة الرئيسية
                        </span>
                        @endif

                        <div class="admin-gallery-actions">

                            {{-- تحريك الصورة للأعلى --}}
                            @unless($loop->first)
                                <form
                                    action="{{ route('admin.products.images.move', [$product, $image]) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="direction"
                                        value="up"
                                    >

                                    <button
                                        type="submit"
                                        class="admin-ui-button admin-ui-button--secondary"
                                        title="تحريك الصورة للأعلى"
                                        aria-label="تحريك الصورة للأعلى"
                                    >
                                        ↑
                                    </button>
                                </form>
                            @endunless

                            {{-- تحريك الصورة للأسفل --}}
                            @unless($loop->last)
                                <form
                                    action="{{ route('admin.products.images.move', [$product, $image]) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="direction"
                                        value="down"
                                    >

                                    <button
                                        type="submit"
                                        class="admin-ui-button admin-ui-button--secondary"
                                        title="تحريك الصورة للأسفل"
                                        aria-label="تحريك الصورة للأسفل"
                                    >
                                        ↓
                                    </button>
                                </form>
                            @endunless

                            {{-- تعيين الصورة الرئيسية --}}
                            @unless($image->is_primary)
                                <form
                                    action="{{ route('admin.products.images.primary', [$product, $image]) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="admin-ui-button admin-ui-button--secondary"
                                    >
                                        جعلها رئيسية
                                    </button>
                                </form>
                            @endunless

                            {{-- حذف الصورة --}}
                            <form
                                action="{{ route('admin.products.images.destroy', [$product, $image]) }}"
                                method="POST"
                                data-confirm
                                data-confirm-title="حذف صورة"
                                data-confirm-message="هل تريد حذف هذه الصورة نهائيًا؟"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="admin-ui-button admin-ui-button--danger"
                                >
                                    حذف
                                </button>
                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </x-admin.card>

@endsection
