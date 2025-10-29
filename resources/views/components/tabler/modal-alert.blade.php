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
    close-button="$closable" class="modal-alert">
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

@once
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                // Auto-close functionality
                window.initializeAlertModal = (modalId, autoClose, duration) => {
                    const modal = document.getElementById(modalId);
                    if (!modal || !autoClose) return;

                    const timer = setTimeout(() => {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }, duration);

                    // Clear timer if modal is manually closed
                    modal.addEventListener('hidden.bs.modal', () => {
                        clearTimeout(timer);
                    });
                };

                // Initialize auto-close for existing alert modals
                document.querySelectorAll('.modal-alert').forEach(modal => {
                    if (modal.dataset.autoClose === 'true') {
                        window.initializeAlertModal(
                            modal.id,
                            true,
                            parseInt(modal.dataset.duration || 5000)
                        );
                    }
                });

                // Initialize when modal is shown
                document.addEventListener('show.bs.modal', (e) => {
                    const modal = e.target;
                    if (modal.classList.contains('modal-alert')) {
                        const autoClose = modal.dataset.autoClose === 'true';
                        const duration = parseInt(modal.dataset.duration || 5000);

                        if (autoClose) {
                            window.initializeAlertModal(modal.id, autoClose, duration);
                        }
                    }
                });
            });
        </script>
    @endpush
@endonce

{{--
Usage Example:
<x-tabler.modal-alert
    id="success-alert"
    type="success"
    title="Success!"
    message="Your changes have been saved successfully."
    auto-close="true"
    duration="3000"
/>

<x-tabler.modal-alert
    id="error-alert"
    type="error"
    title="Error"
    message="Something went wrong. Please try again."
    :dismissible="true"
>
    <div class="mt-3">
        <small class="text-muted">Contact support if the problem persists.</small>
    </div>
</x-tabler.modal-alert>

<x-tabler.modal-alert
    id="warning-alert"
    type="warning"
    title="Warning"
    message="Your session will expire in 5 minutes."
    icon="icon-tabler icon-tabler-clock"
    :auto-close="false"
    :dismissible="false"
/>
--}}
