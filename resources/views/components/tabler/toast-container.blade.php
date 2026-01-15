{{--
Toast Container Component
Handles:
1. Dynamic toasts from Livewire dispatch('toast', [...])
2. Session flash messages (success, error, warning, info)
3. Page load session flash detection
--}}

@once
    @push('scripts')
        <script>
            (function() {
                // Position and variant mappings
                const positionMap = {
                    'top-start': 'top-0 start-0',
                    'top-center': 'top-0 start-50 translate-middle-x',
                    'top-end': 'top-0 end-0',
                    'middle-start': 'top-50 start-0 translate-middle-y',
                    'middle-center': 'top-50 start-50 translate-middle',
                    'middle-end': 'top-50 end-0 translate-middle-y',
                    'bottom-start': 'bottom-0 start-0',
                    'bottom-center': 'bottom-0 start-50 translate-middle-x',
                    'bottom-end': 'bottom-0 end-0',
                };

                const variantClasses = {
                    'success': 'border-success',
                    'danger': 'border-danger',
                    'warning': 'border-warning',
                    'info': 'border-info',
                    'default': ''
                };

                const variantIcons = {
                    'success': `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check me-2 text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                    'danger': `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x me-2 text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>`,
                    'warning': `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle me-2 text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>`,
                    'info': `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle me-2 text-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>`,
                    'default': ''
                };

                const defaultTitles = {
                    'success': 'Berhasil',
                    'danger': 'Error',
                    'warning': 'Peringatan',
                    'info': 'Informasi',
                    'default': 'Notifikasi'
                };

                // Get or create toast container for a specific position
                function getToastContainer(position = 'top-end') {
                    const posClass = positionMap[position] || positionMap['top-end'];
                    const selector = `.toast-container.position-fixed.${posClass.split(' ').join('.')}`;
                    
                    let container = document.querySelector(selector);
                    if (!container) {
                        container = document.createElement('div');
                        container.className = `toast-container position-fixed ${posClass} p-3`;
                        container.style.zIndex = '1090';
                        document.body.appendChild(container);
                    }
                    return container;
                }

                // Create and show a toast
                function showToast({
                    message = 'Notification',
                    title = null,
                    variant = 'default',
                    position = 'top-end',
                    autoHide = true,
                    delay = 5000
                }) {
                    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                    const container = getToastContainer(position);
                    const icon = variantIcons[variant] || '';
                    const borderClass = variantClasses[variant] || '';
                    const displayTitle = title || defaultTitles[variant] || defaultTitles['default'];

                    const toastHtml = `
                        <div class="toast ${borderClass}" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true"
                            data-bs-autohide="${autoHide}" data-bs-delay="${delay}">
                            <div class="toast-header">
                                ${icon}
                                <strong class="me-auto">${displayTitle}</strong>
                                <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                ${message}
                            </div>
                        </div>
                    `;

                    container.insertAdjacentHTML('beforeend', toastHtml);
                    const toastElement = document.getElementById(toastId);
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();

                    // Remove toast element after it's hidden
                    toastElement.addEventListener('hidden.bs.toast', () => {
                        toastElement.remove();
                        // Clean up empty containers
                        if (container.children.length === 0) {
                            container.remove();
                        }
                    });

                    return toastElement;
                }

                // Make showToast available globally
                window.showToast = showToast;

                // Initialize Livewire listeners
                document.addEventListener('livewire:init', () => {
                    // Listen for 'toast' dispatch from Livewire components
                    Livewire.on('toast', (data) => {
                        const config = Array.isArray(data) ? data[0] : data;
                        showToast(config);
                    });

                    // Listen for 'show-toast' dispatch (for static toasts)
                    Livewire.on('show-toast', (data) => {
                        const config = Array.isArray(data) ? data[0] : data;
                        const toastId = config?.id;
                        if (toastId) {
                            const toastElement = document.getElementById(toastId);
                            if (toastElement) {
                                const toast = new bootstrap.Toast(toastElement);
                                toast.show();
                            }
                        }
                    });
                });

                // Show session flash messages on page load
                document.addEventListener('DOMContentLoaded', () => {
                    // Check for session flash data passed from server
                    const flashData = window.__toastFlashData || null;
                    if (flashData) {
                        Object.entries(flashData).forEach(([type, message]) => {
                            if (message) {
                                const variant = type === 'error' ? 'danger' : type;
                                showToast({
                                    message: message,
                                    variant: variant,
                                    position: 'top-end'
                                });
                            }
                        });
                    }
                });
            })();
        </script>
    @endpush
@endonce

{{-- Pass session flash data to JavaScript --}}
@php
    $flashData = [];
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        if (session($type)) {
            $flashData[$type] = session($type);
        }
    }
@endphp

@if (!empty($flashData))
    <script>
        window.__toastFlashData = {!! json_encode($flashData) !!};
    </script>
@endif
