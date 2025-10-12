@php
    $itemType = $item['type'] ?? 'link';
@endphp

@if ($itemType === 'dropend')
    <div class="dropend">
        <a class="{{ $item['toggle_class'] ?? 'dropdown-item dropdown-toggle' }}"
            href="{{ $item['href'] ?? '#' }}"
            data-bs-toggle="{{ $item['toggle']['data-bs-toggle'] ?? 'dropdown' }}"
            data-bs-auto-close="{{ $item['toggle']['data-bs-auto-close'] ?? 'outside' }}"
            role="{{ $item['toggle']['role'] ?? 'button' }}"
            aria-expanded="{{ $item['toggle']['aria-expanded'] ?? 'false' }}">
            {{ $item['label'] }}
        </a>
        <div class="dropdown-menu">
            @foreach ($item['children'] ?? [] as $child)
                @include('components.layouts.partials.menu.dropdown-item', ['item' => $child])
            @endforeach
        </div>
    </div>
@else
    <a class="{{ $item['class'] ?? 'dropdown-item' }}" href="{{ $item['href'] ?? '#' }}"
        @foreach (($item['attributes'] ?? []) as $attribute => $value)
            {{ $attribute }}="{{ $value }}"
        @endforeach>
        @if (!empty($item['prefix_icon']))
            @include('components.layouts.partials.menu.icon', [
                'name' => $item['prefix_icon'],
                'class' => $item['prefix_icon_class'] ?? 'icon icon-2 icon-inline me-1',
            ])
        @elseif (!empty($item['prefix_html']))
            {!! $item['prefix_html'] !!}
        @endif

        {{ $item['label'] }}

        @if (!empty($item['badge']))
            <span class="{{ $item['badge']['class'] ?? '' }}">{{ $item['badge']['text'] ?? '' }}</span>
        @endif
    </a>
@endif
