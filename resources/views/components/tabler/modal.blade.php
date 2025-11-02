@props([
    'id',
    'title' => 'Modal Title',
    'wireIgnore' => true,
    'closeButton' => true,
    'closable' => true,
    'backdrop' => 'static',
    'keyboard' => false,
    'scrollable' => false,
    'centered' => true,
    'size' => 'md',
    'variant' => 'default', // simple, large, small, full-width, scrollable, form, success, danger, confirmation
    'type' => 'default', // success, danger, warning, info, primary (for colored headers)
    'icon' => null, // icon class for header
    'componentId' => str()->random(15),
    'onShow' => null,
    'onHide' => null,
])

@php
    $dialogClasses = 'modal-dialog';
    $modalClasses = 'modal modal-blur fade';
    $headerClasses = 'modal-header';
    $iconMap = [
        'success' => 'ti ti-check-circle text-success',
        'danger' => 'ti ti-alert-triangle text-danger',
        'warning' => 'ti ti-alert-circle text-warning',
        'info' => 'ti ti-info-circle text-info',
    ];

    // Apply size based on variant if not explicitly set
    if (!$attributes->has('size') && $variant !== 'default') {
        switch ($variant) {
            case 'simple':
                $size = 'md';
                break;
            case 'large':
                $size = 'xl';
                break;
            case 'small':
                $size = 'sm';
                break;
            case 'full-width':
                $size = 'xl';
                $modalClasses .= ' w-100';
                break;
            default:
                $size = $size ?: 'lg';
        }
    }

    // Apply variant-specific styling
    if ($variant === 'scrollable') {
        $scrollable = true;
    }

    if ($centered) {
        $dialogClasses .= ' modal-dialog-centered';
    }

    if ($scrollable) {
        $dialogClasses .= ' modal-dialog-scrollable';
    }

    if ($size) {
        $dialogClasses .= ' modal-' . $size;
    }

    // Apply type-specific styling
    if ($type !== 'default') {
        $headerClasses .= ' text-bg-' . $type;
        if (!$icon && isset($iconMap[$type])) {
            $icon = $iconMap[$type];
        }
    }

    $titleId = $id . '-label';

    // Prepare wire:ignore.self attribute if wireIgnore is true
    $wireIgnoreAttr = $wireIgnore ? ['wire:ignore.self' => true] : [];
@endphp

<div
    {{ $attributes->merge(
        array_merge(
            [
                'class' => $modalClasses,
                'id' => $id,
                'tabindex' => '-1',
                'aria-labelledby' => $titleId,
                'aria-hidden' => 'true',
                'data-livewire-modal' => $id,
                'data-livewire-component' => $componentId,
                'data-livewire-on-show' => $onShow,
                'data-livewire-on-hide' => $onHide,
            ],
            $wireIgnoreAttr,
        ),
    ) }}>
    <div class="{{ trim($dialogClasses) }}">
        <div class="modal-content">
            @if ($title || isset($header))
                <div class="{{ trim($headerClasses) }}">
                    @isset($header)
                        {{ $header }}
                    @else
                        @if ($icon)
                            <i class="{{ $icon }} me-2"></i>
                        @endif
                        <h5 class="modal-title" id="{{ $titleId }}">{{ $title }}</h5>
                    @endisset

                    @if ($closeButton)
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    @endif
                </div>
            @endif

            <div class="modal-body">
                {{ $body ?? $slot }}
            </div>

            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                const setupTablerModalListeners = () => {
                    document.querySelectorAll('[data-livewire-modal]').forEach((modalEl) => {
                        if (modalEl.dataset.livewireModalBound === 'true') {
                            return;
                        }

                        modalEl.dataset.livewireModalBound = 'true';

                        const componentId = modalEl.dataset.livewireComponent;
                        const onShow = modalEl.dataset.livewireOnShow;
                        const onHide = modalEl.dataset.livewireOnHide;

                        if (onShow && componentId) {
                            modalEl.addEventListener('show.bs.modal', () => {
                                console.log('Modal show event:', modalEl.id, 'calling:', onShow);
                                try {
                                    const livewireComponent = window.Livewire?.find(componentId);
                                    if (livewireComponent) {
                                        livewireComponent.call(onShow);
                                    } else {
                                        console.warn('Livewire component not found:', componentId);
                                    }
                                } catch (error) {
                                    console.error('Error calling onShow:', error);
                                }
                            });
                        }

                        if (onHide && componentId) {
                            modalEl.addEventListener('hidden.bs.modal', () => {
                                console.log('Modal hide event:', modalEl.id, 'calling:', onHide);
                                try {
                                    const livewireComponent = window.Livewire?.find(componentId);
                                    if (livewireComponent) {
                                        livewireComponent.call(onHide);
                                    } else {
                                        console.warn('Livewire component not found:', componentId);
                                    }
                                } catch (error) {
                                    console.error('Error calling onHide:', error);
                                }
                            });
                        }
                    });
                };

                // Set up listeners on initial load
                setupTablerModalListeners();

                // Re-setup listeners when Livewire updates the DOM using v3 hooks
                Livewire.hook('morph.updated', setupTablerModalListeners);
                Livewire.hook('morph.removed', setupTablerModalListeners);

                // Listen for open-modal dispatch from Livewire
                window.Livewire?.on('open-modal', (event) => {
                    const modal = document.getElementById(event.detail.modalId);
                    console.log('Received open-modal for:', event.detail);
                    if (modal) {
                        // Use setTimeout to ensure Bootstrap is loaded
                        setTimeout(() => {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                const bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap
                                    .Modal(modal);
                                bsModal.show();
                            }
                        }, 0);
                    }
                });

                // Listen for close-modal dispatch from Livewire
                window.Livewire?.on('close-modal', (event) => {
                    const modal = document.getElementById(event.detail.modalId);
                    console.log('Received close-modal for:', event.detail);
                    if (modal) {
                        // Use setTimeout to ensure Bootstrap is loaded
                        setTimeout(() => {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                const bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap
                                    .Modal(modal);
                                bsModal.hide();
                            }
                        }, 0);
                    }
                });
            });
        </script>
    @endpush
@endonce
