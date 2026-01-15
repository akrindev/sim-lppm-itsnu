{{--
Inline Alert Component (for non-toast alerts)
Use this when you need a persistent inline alert that stays on the page.
For toast notifications, the toast-container component handles those automatically.

Note: Session flash messages (success, error, warning, info) are now displayed
as toast notifications via the toast-container component in the layout.
This component is kept for inline alerts when needed.

Usage:
    <x-tabler.alert />  <!-- Auto-detects session flash (for backward compatibility) -->
    <x-tabler.alert type="success" message="Custom message" />
    <x-tabler.alert type="warning" :dismissible="false">Custom content</x-tabler.alert>
--}}

@props([
    'type' => null,
    'message' => null,
    'dismissible' => true,
])

@php
    // If type and message are provided directly, use them
    // Otherwise, check session flash (backward compatibility)
    $alerts = [];

    if ($type && $message) {
        $alerts[$type] = $message;
    } elseif ($slot && $slot->isNotEmpty()) {
        // If slot content is provided with a type
        if ($type) {
            $alerts[$type] = null; // Will use slot
        }
    } else {
        // Check session flash for backward compatibility
        // Note: Toast container now handles these, but we keep this for inline alerts
        $flashTypes = ['danger' => 'error', 'warning' => 'warning', 'success' => 'success', 'info' => 'info'];
        foreach ($flashTypes as $alertType => $sessionKey) {
            if (session($sessionKey)) {
                $alerts[$alertType] = session($sessionKey);
            }
        }
    }
    // Session flash detection removed to prevent duplicates with Toast system.
    // Use explicit type/message or slot for inline alerts.
@endphp

@foreach ($alerts as $alertType => $alertMessage)
    <div {{ $attributes->merge(['class' => "alert alert-{$alertType}" . ($dismissible ? ' alert-dismissible' : '')]) }}
        role="alert">
        <div class="d-flex">
            <div class="alert-icon">
                @if ($alertType === 'danger')
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon alert-icon icon-2">
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                @elseif($alertType === 'warning')
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon alert-icon icon-2">
                        <path d="M12 9v4"></path>
                        <path
                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                        </path>
                        <path d="M12 16h.01"></path>
                    </svg>
                @elseif($alertType === 'success')
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon alert-icon icon-2">
                        <path d="M5 12l5 5l10 -10"></path>
                    </svg>
                @elseif($alertType === 'info')
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon alert-icon icon-2">
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                        <path d="M12 9h.01"></path>
                        <path d="M11 12h1v4h1"></path>
                    </svg>
                @endif
            </div>
            <div>
                @if ($alertMessage)
                    {{ $alertMessage }}
                @else
                    {{ $slot }}
                @endif
            </div>
        </div>
        @if ($dismissible)
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        @endif
    </div>
@endforeach
