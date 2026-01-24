{{--
Toast Container Component
Handles dynamic toasts and session flash messages.
Logic consolidated in resources/js/app.js
--}}

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
    <script data-navigate-once>
        window.__toastFlashData = {!! json_encode($flashData) !!};
    </script>
@endif
