<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Perbarui kata sandi')" :subheading="__('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman')">
        <form method="POST" wire:submit="updatePassword" class="space-y-6 mt-6">
            <flux:input
                wire:model="current_password"
                :label="__('Kata sandi saat ini')"
                type="password"
                required
                autocomplete="current-password"
            />
            <flux:input
                wire:model="password"
                :label="__('Kata sandi baru')"
                type="password"
                required
                autocomplete="new-password"
            />
            <flux:input
                wire:model="password_confirmation"
                :label="__('Konfirmasi Kata Sandi')"
                type="password"
                required
                autocomplete="new-password"
            />

            <div class="flex items-center gap-4">
                <div class="flex justify-end items-center">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Simpan') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Tersimpan.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
