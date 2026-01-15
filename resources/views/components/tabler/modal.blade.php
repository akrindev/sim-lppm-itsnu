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
            document.addEventListener('livewire:initialized', () => {
                // Helper function to find the closest Livewire component
                const findLivewireComponent = (element) => {
                    // Walk up the DOM tree to find the closest [wire:id] element
                    let current = element;
                    while (current && current !== document.body) {
                        if (current.hasAttribute('wire:id')) {
                            const wireId = current.getAttribute('wire:id');
                            return window.Livewire?.find(wireId);
                        }
                        current = current.parentElement;
                    }
                    return null;
                };

                const setupTablerModalListeners = () => {
                    document.querySelectorAll('[data-livewire-modal]').forEach((modalEl) => {
                        if (modalEl.dataset.livewireModalBound === 'true') {
                            return;
                        }

                        modalEl.dataset.livewireModalBound = 'true';

                        const onShow = modalEl.dataset.livewireOnShow;
                        const onHide = modalEl.dataset.livewireOnHide;

                        if (onShow) {
                            modalEl.addEventListener('show.bs.modal', () => {
                                console.log('Modal show event:', modalEl.id, 'calling:', onShow);
                                try {
                                    const livewireComponent = findLivewireComponent(modalEl);
                                    if (livewireComponent) {
                                        if (typeof livewireComponent[onShow] === 'function') {
                                            livewireComponent[onShow]();
                                        } else {
                                            livewireComponent.call(onShow);
                                        }
                                    } else {
                                        console.warn('Livewire component not found for modal:', modalEl.id);
                                    }
                                } catch (error) {
                                    console.error('Error calling onShow:', error);
                                }
                            });
                        }

                        if (onHide) {
                            modalEl.addEventListener('hidden.bs.modal', () => {
                                console.log('Modal hide event:', modalEl.id, 'calling:', onHide);
                                try {
                                    const livewireComponent = findLivewireComponent(modalEl);
                                    if (livewireComponent) {
                                        if (typeof livewireComponent[onHide] === 'function') {
                                            livewireComponent[onHide]();
                                        } else {
                                            livewireComponent.call(onHide);
                                        }
                                    } else {
                                        console.warn('Livewire component not found for modal:', modalEl.id);
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

                // Manage aria-hidden attribute based on modal visibility
                const manageAriaHidden = () => {
                    document.querySelectorAll('[data-livewire-modal]').forEach((modalEl) => {
                        const isVisible = modalEl.classList.contains('show');
                        modalEl.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
                    });
                };

                // Listen for modal visibility changes
                document.addEventListener('show.bs.modal', manageAriaHidden);
                document.addEventListener('hidden.bs.modal', manageAriaHidden);

                // Set initial state
                manageAriaHidden();

                // Listen for open-modal dispatch from Livewire
                // Livewire v3 sends dispatch data as array [{ modalId: 'xxx' }]
                window.Livewire.on('open-modal', (data) => {
                    // Handle both array format (Livewire v3) and object format
                    const config = Array.isArray(data) ? data[0] : data;
                    const modalId = config?.modalId || config?.detail?.modalId;
                    
                    console.log('Received open-modal for:', modalId);
                    if (modalId) {
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            // Try bootstrap first, then tabler
                            const bsModal = bootstrap?.Modal?.getOrCreateInstance(modal) || 
                                           tabler?.Modal?.getOrCreateInstance(modal) ||
                                           new bootstrap.Modal(modal);
                            if (bsModal) {
                                bsModal.show();
                            }
                        }
                    }
                });

                // Listen for close-modal dispatch from Livewire
                // Livewire v3 sends dispatch data as array [{ modalId: 'xxx' }]
                window.Livewire.on('close-modal', (data) => {
                    // Handle both array format (Livewire v3) and object format
                    const config = Array.isArray(data) ? data[0] : data;
                    const modalId = config?.modalId || config?.detail?.modalId;
                    
                    console.log('Received close-modal for:', modalId);
                    if (modalId) {
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            // Try to get existing instance first
                            let bsModal = bootstrap?.Modal?.getInstance(modal) || 
                                           tabler?.Modal?.getInstance(modal);
                            
                            // If instance not found, try to get or create (it might be in a weird state)
                            if (!bsModal && (bootstrap?.Modal || tabler?.Modal)) {
                                bsModal = (bootstrap?.Modal || tabler?.Modal).getOrCreateInstance(modal);
                            }

                            if (bsModal) {
                                bsModal.hide();
                                
                                // Force remove backdrop and classes if it's still stuck
                                setTimeout(() => {
                                    if (modal.classList.contains('show')) {
                                        console.warn('Modal hide failed via BS, forcing cleanup...');
                                        modal.classList.remove('show');
                                        modal.style.display = 'none';
                                        document.body.classList.remove('modal-open');
                                        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                                    }
                                }, 500);
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endonce
