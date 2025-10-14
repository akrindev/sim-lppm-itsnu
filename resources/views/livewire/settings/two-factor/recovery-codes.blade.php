<div
    class="space-y-6 shadow-sm py-6 border border-zinc-200 dark:border-white/10 rounded-xl"
    wire:cloak
    x-data="{ showRecoveryCodes: false }"
>
    <div class="space-y-2 px-6">
        <div class="flex items-center gap-2">
            <flux:icon.lock-closed variant="outline" class="size-4"/>
            <flux:heading size="lg" level="3">{{ __('Kode Pemulihan 2FA') }}</flux:heading>
        </div>
        <flux:text variant="subtle">
            {{ __('Kode pemulihan memungkinkan Anda mendapatkan kembali akses jika Anda kehilangan perangkat 2FA Anda. Simpan di pengelola kata sandi yang aman.') }}
        </flux:text>
    </div>

    <div class="px-6">
        <div class="flex sm:flex-row flex-col sm:justify-between sm:items-center gap-3">
            <flux:button
                x-show="!showRecoveryCodes"
                icon="eye"
                icon:variant="outline"
                variant="primary"
                @click="showRecoveryCodes = true;"
                aria-expanded="false"
                aria-controls="recovery-codes-section"
            >
                {{ __('Lihat Kode Pemulihan') }}
            </flux:button>

            <flux:button
                x-show="showRecoveryCodes"
                icon="eye-slash"
                icon:variant="outline"
                variant="primary"
                @click="showRecoveryCodes = false"
                aria-expanded="true"
                aria-controls="recovery-codes-section"
            >
                {{ __('Sembunyikan Kode Pemulihan') }}
            </flux:button>

            @if (filled($recoveryCodes))
                <flux:button
                    x-show="showRecoveryCodes"
                    icon="arrow-path"
                    variant="filled"
                    wire:click="regenerateRecoveryCodes"
                >
                    {{ __('Regenerasi Kode') }}
                </flux:button>
            @endif
        </div>

        <div
            x-show="showRecoveryCodes"
            x-transition
            id="recovery-codes-section"
            class="relative overflow-hidden"
            x-bind:aria-hidden="!showRecoveryCodes"
        >
            <div class="space-y-3 mt-3">
                @error('recoveryCodes')
                    <flux:callout variant="danger" icon="x-circle" heading="{{$message}}"/>
                @enderror

                @if (filled($recoveryCodes))
                    <div
                        class="gap-1 grid bg-zinc-100 dark:bg-white/5 p-4 rounded-lg font-mono text-sm"
                        role="list"
                        aria-label="Recovery codes"
                    >
                        @foreach($recoveryCodes as $code)
                            <div
                                role="listitem"
                                class="select-text"
                                wire:loading.class="opacity-50 animate-pulse"
                            >
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <flux:text variant="subtle" class="text-xs">
                        {{ __('Setiap kode pemulihan dapat digunakan sekali untuk mengakses akun Anda dan akan dihapus setelah digunakan. Jika Anda membutuhkan lebih banyak, klik Regenerasi Kode di atas.') }}
                    </flux:text>
                @endif
            </div>
        </div>
    </div>
</div>
