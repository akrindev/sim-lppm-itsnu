@props([
    'color' => 'primary', // primary, secondary, success, danger, warning, info, blue, azure, indigo, purple, pink, red, orange, yellow, lime, green, teal, cyan, dark, light
    'variant' => 'light', // solid, light, outline
    'size' => 'md', // sm, md, lg
])

@php
    // Map color to text color class for light variant
    $colorMap = [
        'primary' => 'primary',
        'secondary' => 'secondary',
        'success' => 'green',
        'danger' => 'red',
        'warning' => 'yellow',
        'info' => 'blue',
        'blue' => 'blue',
        'azure' => 'azure',
        'indigo' => 'indigo',
        'purple' => 'purple',
        'pink' => 'pink',
        'red' => 'red',
        'orange' => 'orange',
        'yellow' => 'yellow',
        'lime' => 'lime',
        'green' => 'green',
        'teal' => 'teal',
        'cyan' => 'cyan',
        'dark' => 'dark',
        'light' => 'light',
        // Status mappings
        'draft' => 'secondary',
        'submitted' => 'info',
        'need_assignment' => 'warning',
        'approved' => 'primary',
        'under_review' => 'cyan',
        'reviewed' => 'orange',
        'revision_needed' => 'yellow',
        'completed' => 'green',
        'rejected' => 'danger',
    ];

    $textColor = $colorMap[$color] ?? 'primary';

    $bgClass = match ($variant) {
        'light' => "bg-{$textColor}-lt text-{$textColor}",
        'outline' => "border border-{$textColor} text-{$textColor}",
        default => "bg-{$color}", // solid
    };

    $sizeClass = match ($size) {
        'sm' => 'badge-sm',
        'lg' => 'badge-lg',
        default => '', // md is default
    };

    $classes = "badge $bgClass $sizeClass";
@endphp

<span {{ $attributes->merge(['class' => trim($classes)]) }}>
    {{ $slot }}
</span>
