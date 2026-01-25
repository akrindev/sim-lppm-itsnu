@props([
    'id',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'variant' => 'danger', // danger, warning, info, primary
    'icon' => null, // icon name (e.g., 'trash', 'alert-triangle')
    'wireIgnore' => true,
    'componentId' => null,
    'onConfirm' => null,
    'onCancel' => null,
])

@php
    $variantClasses = match ($variant) {
        'danger' => 'text-danger',
        'warning' => 'text-warning',
        'info' => 'text-info',
        'primary' => 'text-primary',
        default => 'text-danger',
    };

    $iconClasses = match ($variant) {
        'danger' => 'icon-tabler icon-tabler-trash',
        'warning' => 'icon-tabler icon-tabler-alert-triangle',
        'info' => 'icon-tabler icon-tabler-info-circle',
        'primary' => 'icon-tabler icon-tabler-check',
        default => 'icon-tabler icon-tabler-trash',
    };
@endphp

<x-tabler.modal :id="$id" :title="$title" :wire-ignore="$wireIgnore" :component-id="$componentId"
    :on-hide="$onCancel" size="sm" centered="true" close-button="true" class="modal-confirmation">
    <x-slot name="body">
        <div class="d-flex">
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
                <p class="{{ $variantClasses }} mb-0">{{ $message }}</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            {{ $cancelText }}
        </button>
        <button type="button" class="btn btn-{{ $variant }}" data-bs-dismiss="modal"
            @if ($onConfirm) wire:click="{{ $onConfirm }}" @endif>
            {{ $confirmText }}
        </button>
    </x-slot>
</x-tabler.modal>
