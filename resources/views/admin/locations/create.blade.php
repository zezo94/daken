
@extends('layouts.admin')

@section('title', 'إضافة مكان بيع - DAKEN')

@section('content')

    <x-admin.page-header
        title="إضافة مكان بيع"
        description="أدخل بيانات المكان وارفع صورته أو شعاره."
    />

    <x-admin.card>

        <form
            action="{{ route('admin.locations.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @include('admin.locations._form', [
                'location' => null,
            ])

        </form>

    </x-admin.card>

@endsection
