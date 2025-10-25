<div class="py-4 container-tight">
    <div class="mb-4 text-center">
        <a href="." class="navbar-brand navbar-brand-autodark">
            <img src="/logo.png" alt="Logo" width="100" height="100">
        </a>
    </div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="mb-4 text-center h2">{{ __('Lupa kata sandi') }}</h2>
            <p class="mb-4 text-secondary text-center">{{ __('Masukkan email Anda untuk menerima tautan reset kata sandi') }}</p>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" wire:submit="sendPasswordResetLink" autocomplete="off" novalidate>
                <!-- Email Address -->
                <div class="mb-3">
                    <label class="form-label">{{ __('Alamat Email') }}</label>
                    <input type="email" wire:model="email" class="form-control" placeholder="email@contoh.com"
                        autocomplete="email" required autofocus />
                    @error('email')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="w-100 btn btn-primary">
                        {{ __('Kirim tautan reset kata sandi') }}
                    </button>
                </div>
            </form>

            <div class="mt-3 text-secondary text-center">
                <span>{{ __('Atau, kembali ke') }}</span>
                <a href="{{ route('login') }}" wire:navigate>{{ __('masuk') }}</a>
            </div>
        </div>
    </div>
</div>
