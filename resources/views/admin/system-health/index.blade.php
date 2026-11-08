@extends('layouts.admin')

@section('title', 'حالة النظام - DAKEN')

@section('content')

    <div class="daken-health">

        <div class="daken-health__header">
            <div>
                <h1>حالة نظام DAKEN</h1>
                <p>
                    متابعة سلامة المكونات الأساسية للموقع
                    ولوحة الإدارة.
                </p>
            </div>
        </div>

        <div class="daken-health__info">

            <div class="daken-health__card">
                <span>PHP Version</span>
                <strong>{{ $phpVersion }}</strong>
            </div>

            <div class="daken-health__card">
                <span>Laravel Version</span>
                <strong>{{ $laravelVersion }}</strong>
            </div>

            <div class="daken-health__card">
                <span>Environment</span>
                <strong>{{ $environment }}</strong>
            </div>

        </div>

        <div class="daken-health__checks">

            <h2>نتائج الفحص</h2>

            @foreach ($checks as $check)

                <div class="daken-health__check">

                    <div>
                        <h3>{{ $check['name'] }}</h3>
                        <p>{{ $check['description'] }}</p>
                    </div>

                    <span class="daken-health__status
                    {{ $check['status'] ? 'is-ok' : 'is-error' }}">

                    {{ $check['status'] ? 'سليم' : 'يحتاج مراجعة' }}

                </span>

                </div>

            @endforeach

        </div>

    </div>

@endsection
