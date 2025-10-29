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

@once
    @push('scripts')
        <style>
            .modal-loading .modal-content {
                border: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }

            .modal-loading.modal-blur {
                backdrop-filter: blur(2px);
            }

            .progress-bar-indeterminate {
                background: linear-gradient(90deg, #0ea5e9 0%, #3b82f6 50%, #0ea5e9 100%);
                background-size: 200% 100%;
                animation: indeterminate 1.5s infinite;
            }

            @keyframes indeterminate {
                0% {
                    background-position: 200% 0;
                }

                100% {
                    background-position: -200% 0;
                }
            }

            .spinner-border-lg {
                width: 3rem;
                height: 3rem;
                border-width: 0.3em;
            }

            .modal-loading .btn-close {
                display: none;
            }

            .modal-loading.modal-static .modal-backdrop {
                opacity: 0.8;
            }
        </style>

        <script>
            document.addEventListener('livewire:init', () => {
                // Global loading modal functions
                window.LoadingModal = {
                    show(modalId) {
                        const modal = document.getElementById(modalId);
                        if (modal && bootstrap.Modal) {
                            const bsModal = new bootstrap.Modal(modal, {
                                backdrop: modal.dataset.backdrop || 'static',
                                keyboard: modal.dataset.keyboard !== 'false'
                            });
                            bsModal.show();
                            return bsModal;
                        }
                    },

                    hide(modalId) {
                        const modal = document.getElementById(modalId);
                        if (modal && bootstrap.Modal) {
                            const bsModal = bootstrap.Modal.getInstance(modal);
                            if (bsModal) {
                                bsModal.hide();
                            }
                        }
                    },

                    updateProgress(modalId, progress, message = null) {
                        const modal = document.getElementById(modalId);
                        if (!modal) return;

                        // Update progress bar
                        const progressBar = modal.querySelector('.progress-bar');
                        if (progressBar) {
                            progressBar.style.width = `${progress}%`;
                            progressBar.setAttribute('aria-valuenow', progress);
                        }

                        // Update percentage text
                        const percentageText = modal.querySelector('small.text-muted');
                        if (percentageText) {
                            percentageText.textContent = `${progress}%`;
                        }

                        // Update message if provided
                        if (message) {
                            const messageEl = modal.querySelector('p.text-muted');
                            if (messageEl) {
                                messageEl.textContent = message;
                            }
                        }
                    },

                    setMessage(modalId, message) {
                        const modal = document.getElementById(modalId);
                        if (!modal) return;

                        const messageEl = modal.querySelector('p.text-muted');
                        if (messageEl) {
                            messageEl.textContent = message;
                        }
                    }
                };

                // Auto-hide loading modals when page changes
                document.addEventListener('livewire:navigated', () => {
                    document.querySelectorAll('.modal-loading.show').forEach(modal => {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    });
                });

                // Prevent closing loading modals with ESC
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        const loadingModal = document.querySelector('.modal-loading.show');
                        if (loadingModal && loadingModal.dataset.keyboard === 'false') {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    }
                });
            });
        </script>
    @endpush
@endonce

{{--
Usage Example:

1. Basic Loading Modal:
<x-tabler.modal-loading
    id="loading-modal"
    title="Processing"
    message="Please wait while we save your data..."
/>

2. Determinate Progress:
<x-tabler.modal-loading
    id="upload-modal"
    title="Uploading File"
    message="Please wait while we upload your file..."
    :indeterminate="false"
    :progress="0"
>
    <div class="mt-3">
        <small class="text-muted">This may take a few moments...</small>
    </div>
</x-tabler.modal-loading>

3. Large Loading Modal:
<x-tabler.modal-loading
    id="large-loading"
    title="Generating Report"
    message="Creating your detailed report..."
    size="lg"
/>

JavaScript Usage:
window.LoadingModal.show('loading-modal');
window.LoadingModal.updateProgress('upload-modal', 45, 'Processing data...');
window.LoadingModal.hide('loading-modal');
--}}
