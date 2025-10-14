<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Autentikasi Dua Faktor')"
        :subheading="__('Kelola pengaturan autentikasi dua faktor Anda')"
    >
        <div class="flex flex-col space-y-6 mx-auto w-full text-sm" wire:cloak>
            @if ($twoFactorEnabled)
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <flux:badge color="green">{{ __('Diaktifkan') }}</flux:badge>
                    </div>

                    <flux:text>
                        {{ __('Dengan autentikasi dua faktor diaktifkan, Anda akan diminta memasukkan pin acak yang aman saat login, yang dapat Anda ambil dari aplikasi yang mendukung TOTP di ponsel Anda.') }}
                    </flux:text>

                    <livewire:settings.two-factor.recovery-codes :$requiresConfirmation/>

                    <div class="flex justify-start">
                        <flux:button
                            variant="danger"
                            icon="shield-exclamation"
                            icon:variant="outline"
                            wire:click="disable"
                        >
                            {{ __('Nonaktifkan 2FA') }}
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <flux:badge color="red">{{ __('Dinonaktifkan') }}</flux:badge>
                    </div>

                    <flux:text variant="subtle">
                        {{ __('Saat Anda mengaktifkan autentikasi dua faktor, Anda akan diminta memasukkan pin yang aman saat login. Pin ini dapat diambil dari aplikasi yang mendukung TOTP di ponsel Anda.') }}
                    </flux:text>

                    <flux:button
                        variant="primary"
                        icon="shield-check"
                        icon:variant="outline"
                        wire:click="enable"
                    >
                        {{ __('Aktifkan 2FA') }}
                    </flux:button>
                </div>
            @endif
        </div>
    </x-settings.layout>

    <flux:modal
        name="two-factor-setup-modal"
        class="md:min-w-md max-w-md"
        @close="closeModal"
        wire:model="showModal"
    >
        <div class="space-y-6">
            <div class="flex flex-col items-center space-y-4">
                <div class="bg-white dark:bg-stone-800 shadow-sm p-0.5 border border-stone-100 dark:border-stone-600 rounded-full w-auto">
                    <div class="relative bg-stone-100 dark:bg-stone-200 p-2.5 border border-stone-200 dark:border-stone-600 rounded-full overflow-hidden">
                        <div class="absolute inset-0 flex [&>div]:flex-1 justify-around items-stretch opacity-50 divide-x divide-stone-200 dark:divide-stone-300 w-full h-full">
                            @for ($i = 1; $i <= 5; $i++)
                                <div></div>
                            @endfor
                        </div>

                        <div class="absolute inset-0 flex flex-col [&>div]:flex-1 justify-around items-stretch opacity-50 divide-y divide-stone-200 dark:divide-stone-300 w-full h-full">
                            @for ($i = 1; $i <= 5; $i++)
                                <div></div>
                            @endfor
                        </div>

                        <flux:icon.qr-code class="z-20 relative dark:text-accent-foreground"/>
                    </div>
                </div>

                <div class="space-y-2 text-center">
                    <flux:heading size="lg">{{ $this->modalConfig['title'] }}</flux:heading>
                    <flux:text>{{ $this->modalConfig['description'] }}</flux:text>
                </div>
            </div>

            @if ($showVerificationStep)
                <div class="space-y-6">
                    <div class="flex flex-col items-center space-y-3">
                        <x-input-otp
                            :digits="6"
                            name="code"
                            wire:model="code"
                            autocomplete="one-time-code"
                        />
                        @error('code')
                            <flux:text color="red">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                    <div class="flex items-center space-x-3">
                        <flux:button
                            variant="outline"
                            class="flex-1"
                            wire:click="resetVerification"
                        >
                            {{ __('Kembali') }}
                        </flux:button>

                        <flux:button
                            variant="primary"
                            class="flex-1"
                            wire:click="confirmTwoFactor"
                            x-bind:disabled="$wire.code.length < 6"
                        >
                            {{ __('Konfirmasi') }}
                        </flux:button>
                    </div>
                </div>
            @else
                @error('setupData')
                    <flux:callout variant="danger" icon="x-circle" heading="{{ $message }}"/>
                @enderror

                <div class="flex justify-center">
                    <div class="relative border border-stone-200 dark:border-stone-700 rounded-lg w-64 aspect-square overflow-hidden">
                        @empty($qrCodeSvg)
                            <div class="absolute inset-0 flex justify-center items-center bg-white dark:bg-stone-700 animate-pulse">
                                <flux:icon.loading/>
                            </div>
                        @else
                            <div class="flex justify-center items-center p-4 h-full">
                                {!! $qrCodeSvg !!}
                            </div>
                        @endempty
                    </div>
                </div>

                <div>
                    <flux:button
                        :disabled="$errors->has('setupData')"
                        variant="primary"
                        class="w-full"
                        wire:click="showVerificationIfNecessary"
                    >
                        {{ $this->modalConfig['buttonText'] }}
                    </flux:button>
                </div>

                <div class="space-y-4">
                    <div class="relative flex justify-center items-center w-full">
                        <div class="top-1/2 absolute inset-0 bg-stone-200 dark:bg-stone-600 w-full h-px"></div>
                        <span class="relative bg-white dark:bg-stone-800 px-2 text-stone-600 dark:text-stone-400 text-sm">
                            {{ __('atau, masukkan kode secara manual') }}
                        </span>
                    </div>

                    <div
                        class="flex items-center space-x-2"
                        x-data="{
                            copied: false,
                            async copy() {
                                try {
                                    await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 1500);
                                } catch (e) {
                                    console.warn('Could not copy to clipboard');
                                }
                            }
                        }"
                    >
                        <div class="flex items-stretch border dark:border-stone-700 rounded-xl w-full">
                            @empty($manualSetupKey)
                                <div class="flex justify-center items-center bg-stone-100 dark:bg-stone-700 p-3 w-full">
                                    <flux:icon.loading variant="mini"/>
                                </div>
                            @else
                                <input
                                    type="text"
                                    readonly
                                    value="{{ $manualSetupKey }}"
                                    class="bg-transparent p-3 outline-none w-full text-stone-900 dark:text-stone-100"
                                />

                                <button
                                    @click="copy()"
                                    class="px-3 border-stone-200 dark:border-stone-600 border-l transition-colors cursor-pointer"
                                >
                                    <flux:icon.document-duplicate x-show="!copied" variant="outline"></flux:icon>
                                    <flux:icon.check
                                        x-show="copied"
                                        variant="solid"
                                        class="text-green-500"
                                    ></flux:icon>
                                </button>
                            @endempty
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>
</section>
