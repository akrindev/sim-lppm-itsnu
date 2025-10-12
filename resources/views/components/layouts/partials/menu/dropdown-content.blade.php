@php
    $layout = $dropdown['layout'] ?? 'list';
@endphp

@if ($layout === 'columns')
    <div class="dropdown-menu-columns">
        @foreach ($dropdown['columns'] ?? [] as $column)
            <div class="dropdown-menu-column">
                @foreach ($column as $columnItem)
                    @include('components.layouts.partials.menu.dropdown-item', ['item' => $columnItem])
                @endforeach
            </div>
        @endforeach
    </div>
@else
    @foreach ($dropdown['items'] ?? [] as $item)
        @include('components.layouts.partials.menu.dropdown-item', ['item' => $item])
    @endforeach
@endif
