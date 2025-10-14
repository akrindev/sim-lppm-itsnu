<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset kata sandi')" :description="__('Silakan masukkan kata sandi baru Anda di bawah ini')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="resetPassword" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email')"
            type="email"
            required
            autocomplete="email"
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
                {{ __('Reset kata sandi') }}
            </flux:button>
        </div>
    </form>
    </div>
</x-layouts.auth>
