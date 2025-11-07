@props([
    'count' => 3,
])

@php
    $classes = "nav nav-segmented nav-{$count} w-100";
@endphp

<nav {{ $attributes->merge(['class' => $classes]) }} role="tablist">
    {{ $slot }}
</nav>
