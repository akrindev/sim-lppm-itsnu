@props(['message' => 'Data telah diperbarui', 'show' => false])

<div 
    x-data="{ 
        show: @js($show),
        message: @js($message),
        showNotification() {
            this.show = true;
            setTimeout(() => {
                this.show = false;
            }, 3000);
        }
    }"
    x-init="$watch('show', (value) => {
        if (value) {
            // Add animation class
            $el.classList.add('updated-content');
            setTimeout(() => {
                $el.classList.remove('updated-content');
            }, 1000);
        }
    })"
>
    <!-- Toast Notification -->
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="position-fixed"
        style="top: 80px; right: 20px; z-index: 1050;"
    >
        <div class="alert alert-success alert-dismissible mb-3" role="alert">
            <div class="d-flex">
                <div>
                    <!-- Download SVG icon from http://tabler-icons.io/i/check -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 12l5 5l10 -10"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="alert-title">
                        {{ $message }}
                    </h4>
                    <div class="text-muted">
                        <small class="text-muted">
                            <x-lucide-clock class="icon icon-sm" />
                            {{ now()->format('H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .updated-content {
        animation: updatedPulse 1s ease-in-out;
    }

    @keyframes updatedPulse {
        0% { background-color: rgba(13, 110, 253, 0); }
        25% { background-color: rgba(13, 110, 253, 0.1); }
        50% { background-color: rgba(13, 110, 253, 0.15); }
        75% { background-color: rgba(13, 110, 253, 0.1); }
        100% { background-color: rgba(13, 110, 253, 0); }
    }
</style>
