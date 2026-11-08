
@extends('layouts.admin')

@section('title', 'إضافة منتج - DAKEN')

@section('content')

    <x-admin.page-header
        title="إضافة منتج جديد"
        description="أدخل بيانات المنتج بالعربية والإنجليزية."
    />

    <x-admin.card>

        <form
            action="{{ route('admin.products.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @include('admin.products._form', [
                'product' => null,
                'categories' => $categories,
            ])

        </form>

    </x-admin.card>

@endsection
