
@props([
    'headers' => [],
])

<div class="admin-table-wrapper">
    <table {{ $attributes->class(['admin-data-table']) }}>
        <thead>
        <tr>
            @foreach($headers as $header)
                <th scope="col">
                    {{ $header }}
                </th>
            @endforeach
        </tr>
        </thead>

        <tbody>
        {{ $slot }}
        </tbody>
    </table>
</div>
