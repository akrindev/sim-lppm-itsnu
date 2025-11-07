@props([
    'id' => 'toast-' . uniqid(),
    'title' => null,
    'subtitle' => null,
    'avatar' => null,
    'icon' => null,
    'autoHide' => true,
    'delay' => 5000, // milliseconds
    'position' => 'bottom-end', // top-start, top-center, top-end, middle-start, middle-center, middle-end, bottom-start, bottom-center, bottom-end
    'variant' => 'default', // default, success, danger, warning, info
    'showHeader' => true,
    'closeButton' => true,
])

@php
    $positionMap = [
        'top-start' => 'top-0 start-0',
        'top-center' => 'top-0 start-50 translate-middle-x',
        'top-end' => 'top-0 end-0',
        'middle-start' => 'top-50 start-0 translate-middle-y',
        'middle-center' => 'top-50 start-50 translate-middle',
        'middle-end' => 'top-50 end-0 translate-middle-y',
        'bottom-start' => 'bottom-0 start-0',
        'bottom-center' => 'bottom-0 start-50 translate-middle-x',
        'bottom-end' => 'bottom-0 end-0',
    ];

    $positionClass = $positionMap[$position] ?? $positionMap['bottom-end'];

    $variantClasses = match ($variant) {
        'success' => 'border-success',
        'danger' => 'border-danger',
        'warning' => 'border-warning',
        'info' => 'border-info',
        default => '',
    };

    $variantIconClasses = match ($variant) {
        'success' => 'icon-tabler icon-tabler-check text-success',
        'danger' => 'icon-tabler icon-tabler-x text-danger',
        'warning' => 'icon-tabler icon-tabler-alert-triangle text-warning',
        'info' => 'icon-tabler icon-tabler-info-circle text-info',
        default => '',
    };
@endphp

<div class="toast-container position-fixed {{ $positionClass }} p-3" style="z-index: 1090;">
    <div class="toast {{ $variantClasses }}" id="{{ $id }}" role="alert" aria-live="assertive" aria-atomic="true"
        data-bs-autohide="{{ $autoHide ? 'true' : 'false' }}" data-bs-delay="{{ $delay }}">
        @if ($showHeader)
            <div class="toast-header">
                @if ($avatar)
                    <span class="me-2 avatar avatar-xs" style="background-image: url({{ $avatar }})"></span>
                @elseif ($icon)
                    <x-lucide-{{ $icon }} class="me-2 icon" />
                @elseif ($variant !== 'default')
                    <i class="{{ $variantIconClasses }} me-2"></i>
                @endif

                @if ($title)
                    <strong class="me-auto">{{ $title }}</strong>
                @endif

                @if ($subtitle)
                    <small>{{ $subtitle }}</small>
                @endif

                @if ($closeButton)
                    <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                @endif
            </div>
        @endif

        <div class="toast-body">
            {{ $slot }}
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Auto-show toasts that are triggered via data attributes
                document.querySelectorAll('[data-bs-toggle="toast"]').forEach(trigger => {
                    trigger.addEventListener('click', (e) => {
                        e.preventDefault();
                        const target = trigger.getAttribute('data-bs-target');
                        if (target) {
                            const toastElement = document.querySelector(target);
                            if (toastElement) {
                                const toast = new bootstrap.Toast(toastElement);
                                toast.show();
                            }
                        }
                    });
                });
            });

            // Livewire integration for showing toasts
            document.addEventListener('livewire:init', () => {
                Livewire.on('show-toast', (data) => {
                    const toastId = data[0]?.id || data.id;
                    if (toastId) {
                        const toastElement = document.getElementById(toastId);
                        if (toastElement) {
                            const toast = new bootstrap.Toast(toastElement);
                            toast.show();
                        }
                    }
                });

                // Support for dynamic toast creation
                Livewire.on('toast', (data) => {
                    const config = data[0] || data;
                    showToast(config);
                });
            });

            // Helper function to create and show dynamic toasts
            function showToast({
                message = 'Notification',
                title = null,
                variant = 'default',
                position = 'bottom-end',
                autoHide = true,
                delay = 5000
            }) {
                const toastId = 'toast-' + Date.now();
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
                    'success': '<i class="me-2 text-success icon-tabler icon-tabler-check"></i>',
                    'danger': '<i class="me-2 text-danger icon-tabler icon-tabler-x"></i>',
                    'warning': '<i class="me-2 text-warning icon-tabler icon-tabler-alert-triangle"></i>',
                    'info': '<i class="me-2 text-info icon-tabler icon-tabler-info-circle"></i>',
                    'default': ''
                };

                let container = document.querySelector(`.toast-container.${positionMap[position].replace(/ /g, '.')}`);
                if (!container) {
                    container = document.createElement('div');
                    container.className = `toast-container position-fixed ${positionMap[position]} p-3`;
                    container.style.zIndex = '1090';
                    document.body.appendChild(container);
                }

                const toastHtml = `
                    <div class="toast ${variantClasses[variant]}" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true"
                        data-bs-autohide="${autoHide}" data-bs-delay="${delay}">
                        ${title ? `
                                    <div class="toast-header">
                                        ${variantIcons[variant]}
                                        <strong class="me-auto">${title}</strong>
                                        <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                ` : ''}
                        <div class="toast-body">
                            ${!title && variantIcons[variant] ? variantIcons[variant] : ''}
                            ${message}
                            ${!title ? '<button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>' : ''}
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
                });
            }

            // Make showToast available globally
            window.showToast = showToast;
        </script>
    @endpush
@endonce

{{--
Usage Example:

<!-- Static Toast -->
<x-tabler.toast
    id="toast-welcome"
    title="Welcome!"
    subtitle="2 mins ago"
    variant="success"
    position="top-end"
>
    Your account has been created successfully.
</x-tabler.toast>

<!-- Toast with Avatar -->
<x-tabler.toast
    id="toast-message"
    title="Mallory Hulme"
    subtitle="11 mins ago"
    avatar="/avatars/002m.jpg"
>
    Hello, world! This is a toast message.
</x-tabler.toast>

<!-- Toast with Icon -->
<x-tabler.toast
    id="toast-notification"
    title="Notification"
    icon="bell"
    variant="info"
    :auto-hide="false"
>
    You have a new notification.
</x-tabler.toast>

<!-- Toast without Header -->
<x-tabler.toast
    id="toast-simple"
    :show-header="false"
    variant="warning"
>
    🍪 Our site uses cookies. By continuing to use our site, you agree to our Cookie Policy.
    <div class="mt-2 pt-2 border-top">
        <a href="#" class="btn btn-primary btn-sm">I understand</a>
    </div>
</x-tabler.toast>

<!-- Trigger Toast -->
<a href="#" class="btn btn-primary" data-bs-toggle="toast" data-bs-target="#toast-welcome">
    Show Toast
</a>

<!-- Livewire Integration -->
In your Livewire component:
$this->dispatch('show-toast', id: 'toast-welcome');

Or create dynamic toast:
$this->dispatch('toast', [
    'message' => 'Item saved successfully!',
    'title' => 'Success',
    'variant' => 'success',
    'position' => 'top-end'
]);

<!-- JavaScript -->
<script>
    // Show toast via JavaScript
    showToast({
        message: 'Operation completed!',
        title: 'Success',
        variant: 'success',
        position: 'top-end',
        autoHide: true,
        delay: 5000
    });
</script>
--}}
