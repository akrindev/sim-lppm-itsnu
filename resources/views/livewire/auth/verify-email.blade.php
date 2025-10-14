<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Verifikasi Email')" :description="__('Silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan kepada Anda.')" />

        <flux:text class="text-center">
            {{ __('Silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan kepada Anda.') }}
        </flux:text>

    @if (session('status') == 'verification-link-sent')
        <flux:text class="font-medium !text-green-600 !dark:text-green-400 text-center">
            {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
        </flux:text>
    @endif

    <div class="flex flex-col justify-between items-center space-y-3">
        <flux:button wire:click="sendVerification" variant="primary" class="w-full">
            {{ __('Kirim ulang email verifikasi') }}
        </flux:button>

        <flux:link class="text-sm cursor-pointer" wire:click="logout">
            {{ __('Keluar') }}
        </flux:link>
    </div>
</div>
