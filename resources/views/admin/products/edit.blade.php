
@extends('layouts.admin')

@section('title', 'تعديل منتج - DAKEN')

@section('content')

    <x-admin.page-header
        title="تعديل المنتج"
        :description="$product->translated_name"
    />

    <x-admin.card>

        <form
            action="{{ route('admin.products.update', $product) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            @include('admin.products._form', [
                'product' => $product,
                'categories' => $categories,
            ])

        </form>

    </x-admin.card>

@endsection
