
@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class(['admin-page-header']) }}>
    <div class="admin-page-header__text">
        <h1>{{ $title }}</h1>

        @if($description)
            <p>{{ $description }}</p>
        @endif
    </div>

    @if(isset($actions))
        <div class="admin-page-header__actions">
            {{ $actions }}
        </div>
    @endif
</div>
