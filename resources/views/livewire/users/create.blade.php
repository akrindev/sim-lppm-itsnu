<div>
    <x-slot:pageActions>
        <a href="{{ route('users.index') }}" class="btn-outline-secondary btn">
            {{ __('Kembali ke daftar pengguna') }}
        </a>
    </x-slot:pageActions>

    <form wire:submit.prevent="save" class="card card-stacked" novalidate>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="create-name">{{ __('Nama') }}</label>
                        <input
                            id="create-name"
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            wire:model.defer="name"
                            autocomplete="name"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="create-email">{{ __('Alamat email') }}</label>
                        <input
                            id="create-email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            wire:model.defer="email"
                            autocomplete="email"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="create-password">{{ __('Kata sandi') }}</label>
                        <input
                            id="create-password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            wire:model.defer="password"
                            autocomplete="new-password"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="create-password-confirmation">{{ __('Konfirmasi kata sandi') }}</label>
                        <input
                            id="create-password-confirmation"
                            type="password"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            wire:model.defer="password_confirmation"
                            autocomplete="new-password"
                            required
                        >
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <hr class="my-4">
                    <h3 class="mb-4">{{ __('Informasi Identitas') }}</h3>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="identity-id">{{ __('ID Identitas') }}</label>
                        <input
                            id="identity-id"
                            type="text"
                            class="form-control @error('identity_id') is-invalid @enderror"
                            wire:model.defer="identity_id"
                            placeholder="{{ __('mis., NIP, NIDN, atau ID Karyawan') }}"
                            required
                        >
                        @error('identity_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="birthplace">{{ __('Tempat Lahir') }}</label>
                        <input
                            id="birthplace"
                            type="text"
                            class="form-control @error('birthplace') is-invalid @enderror"
                            wire:model.defer="birthplace"
                        >
                        @error('birthplace')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="birthdate">{{ __('Tanggal Lahir') }}</label>
                        <input
                            id="birthdate"
                            type="date"
                            class="form-control @error('birthdate') is-invalid @enderror"
                            wire:model.defer="birthdate"
                        >
                        @error('birthdate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="address">{{ __('Alamat') }}</label>
                        <textarea
                            id="address"
                            class="form-control @error('address') is-invalid @enderror"
                            wire:model.defer="address"
                            rows="3"
                        ></textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Tetapkan peran') }}</label>
                        <div class="form-selectgroup @error('selectedRole') is-invalid @enderror">
                            @foreach ($roleOptions as $option)
                                <label class="form-selectgroup-item">
                                    <input
                                        type="radio"
                                        name="create-role"
                                        value="{{ $option['value'] }}"
                                        class="form-selectgroup-input"
                                        wire:model.defer="selectedRole"
                                    >
                                    <span class="form-selectgroup-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1 icon">
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                        </svg>
                                        {{ $option['label'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedRole')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                        <p class="mt-2 text-secondary">{{ __('Biarkan tidak dipilih untuk menetapkan peran nanti.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between card-footer">
            <a href="{{ route('users.index') }}" class="btn btn-link">
                {{ __('Batal') }}
            </a>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Buat pengguna') }}</span>
                <span wire:loading>{{ __('Membuat...') }}</span>
            </button>
        </div>
    </form>
</div>
