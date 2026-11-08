
@extends('layouts.admin')

@section('title', 'إضافة تصنيف - DAKEN')

@section('content')

    <x-admin.page-header
        title="إضافة تصنيف"
        description="إنشاء تصنيف جديد لمنتجات DAKEN."
    />

    <x-admin.card>

        <form
            action="{{ route('admin.categories.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @include('admin.categories._form', [
                'category' => null,
            ])

        </form>

    </x-admin.card>

@endsection
