
@props([
    'title' => null,
])

<section {{ $attributes->class(['admin-ui-card']) }}>
    @if($title)
        <div class="admin-ui-card__header">
            <h2>{{ $title }}</h2>
        </div>
    @endif

    <div class="admin-ui-card__body">
        {{ $slot }}
    </div>
</section>
