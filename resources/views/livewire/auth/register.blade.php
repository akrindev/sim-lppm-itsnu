<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Buat akun')" :description="__('Masukkan detail Anda di bawah ini untuk membuat akun Anda')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Nama')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Nama lengkap')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Alamat email')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@contoh.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Kata sandi')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Kata sandi')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Konfirmasi kata sandi')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Konfirmasi kata sandi')"
            viewable
        />

        <div class="flex justify-end items-center">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Buat akun') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-zinc-600 dark:text-zinc-400 text-sm text-center">
        <span>{{ __('Sudah punya akun?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Masuk') }}</flux:link>
    </div>
    </div>
</x-layouts.auth>
