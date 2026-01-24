@props([
    'id',
    'type' => 'success', // success, error, warning, info
    'title' => null,
    'message' => null,
    'icon' => null,
    'autoClose' => false,
    'duration' => 5000, // milliseconds
    'wireIgnore' => true,
    'dismissible' => true,
    'closable' => true,
])

@php
    $variantClasses = match ($type) {
        'success' => 'text-success border-success bg-success-subtle',
        'error' => 'text-danger border-danger bg-danger-subtle',
        'warning' => 'text-warning border-warning bg-warning-subtle',
        'info' => 'text-info border-info bg-info-subtle',
        default => 'text-success border-success bg-success-subtle',
    };

    $iconClasses = match ($type) {
        'success' => 'icon-tabler icon-tabler-check-circle-2',
        'error' => 'icon-tabler icon-tabler-alert-circle',
        'warning' => 'icon-tabler icon-tabler-alert-triangle',
        'info' => 'icon-tabler icon-tabler-info-circle',
        default => 'icon-tabler icon-tabler-check-circle-2',
    };
@endphp

<x-tabler.modal :id="$id" :title="$title" :wire-ignore="$wireIgnore" size="sm" centered="true"
    close-button="$closable" class="modal-alert" :data-auto-close="$autoClose ? 'true' : 'false'" :data-duration="$duration">
    <x-slot name="body">
        <div class="d-flex align-items-start">
            @if ($icon)
                <div class="me-3">
                    <div class="icon icon-shape icon-lg rounded-circle {{ $variantClasses }}">
                        <i class="{{ $icon }}"></i>
                    </div>
                </div>
            @else
                <div class="me-3">
                    <div class="icon icon-shape icon-lg rounded-circle {{ $variantClasses }}">
                        <i class="{{ $iconClasses }}"></i>
                    </div>
                </div>
            @endif

            <div class="flex-grow-1">
                @if ($title)
                    <h6 class="{{ explode(' ', $variantClasses)[0] }} mb-1">{{ $title }}</h6>
                @endif
                <p class="{{ explode(' ', $variantClasses)[0] }} mb-0">{{ $message }}</p>
                {{ $slot }}
            </div>

            @if ($dismissible)
                <div class="ms-auto">
                    <button type="button" class="btn-close {{ explode(' ', $variantClasses)[0] }}"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </x-slot>
</x-tabler.modal>
