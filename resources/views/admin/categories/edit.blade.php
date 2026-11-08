
@extends('layouts.admin')

@section('title', 'تعديل تصنيف - DAKEN')

@section('content')

    <x-admin.page-header
        title="تعديل التصنيف"
        :description="$category->translated_name"
    />

    <x-admin.card>

        <form
            action="{{ route('admin.categories.update', $category) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            @include('admin.categories._form', [
                'category' => $category,
            ])

        </form>

    </x-admin.card>

@endsection
