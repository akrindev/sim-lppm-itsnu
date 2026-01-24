@props([
    'id',
    'title' => 'Loading...',
    'message' => 'Please wait while we process your request.',
    'progress' => null, // 0-100 for determinate progress
    'indeterminate' => true,
    'wireIgnore' => true,
    'showTitle' => true,
    'size' => 'sm',
    'backdrop' => 'static', // 'static' or 'true'
    'keyboard' => false,
])

@php
    $progressBarClass = $indeterminate ? 'progress-bar-indeterminate' : '';
    $spinnerSize = match ($size) {
        'sm' => 'spinner-border-sm',
        'lg' => 'spinner-border-lg',
        default => '',
    };
@endphp

<x-tabler.modal :id="$id" :title="$showTitle ? $title : null" :wire-ignore="$wireIgnore" size="$size" centered="true" :close-button="false"
    :scrollable="false" backdrop="$backdrop" keyboard="$keyboard" class="modal-loading">
    <x-slot name="body">
        <div class="py-4 text-center">
            @if ($indeterminate)
                <div class="spinner-border text-primary {{ $spinnerSize }}" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            @else
                <div class="mb-3 progress" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $progressBarClass }}"
                        role="progressbar" style="width: {{ $progress ?? 0 }}%">
                    </div>
                </div>
            @endif

            @if ($message)
                <p class="mt-3 mb-0 text-muted">{{ $message }}</p>
            @endif

            @if (!is_null($progress) && !$indeterminate)
                <small class="d-block mt-2 text-muted">{{ $progress }}%</small>
            @endif

            {{ $slot }}
        </div>
    </x-slot>
</x-tabler.modal>
