
@extends('layouts.admin')

@section('title', 'تعديل مكان بيع - DAKEN')

@section('content')

    <x-admin.page-header
        title="تعديل مكان البيع"
        :description="$location->translated_name"
    />

    <x-admin.card>

        <form
            action="{{ route('admin.locations.update', $location) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            @include('admin.locations._form', [
                'location' => $location,
            ])

        </form>

    </x-admin.card>

@endsection
