<div>
    <x-slot:pageActions>
        <a href="{{ route('users.show', $user) }}" class="btn-outline-secondary btn">
            {{ __('Lihat profil') }}
        </a>
    </x-slot:pageActions>

    @if (session('status'))
        <div class="mb-3 alert alert-success alert-important" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="card card-stacked" novalidate>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="user-name">{{ __('Nama') }}</label>
                        <input id="user-name" type="text" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name" autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="user-email">{{ __('Alamat email') }}</label>
                        <input id="user-email" type="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email" autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Peran') }}</label>
                        <div class="form-selectgroup @error('selectedRole') is-invalid @enderror">
                            @foreach ($roleOptions as $option)
                                <label class="form-selectgroup-item">
                                    <input
                                        type="radio"
                                        name="role"
                                        value="{{ $option['value'] }}"
                                        class="form-selectgroup-input"
                                        wire:model.defer="selectedRole"
                                        @if($selectedRole === $option['value']) checked @endif
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

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="d-block form-label">{{ __('Verifikasi email') }}</label>
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model.defer="emailVerified">
                            <span class="form-check-label">{{ __('Tandai email sebagai terverifikasi') }}</span>
                        </label>
                        @error('emailVerified')
                            <div class="mt-1 text-danger small">{{ $message }}</div>
                        @enderror
                        <p class="mt-2 text-secondary">{{ __('Alihkan untuk mengatur status verifikasi secara manual.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between card-footer">
            <a href="{{ route('users.show', $user) }}" class="btn btn-link">
                {{ __('Batal') }}
            </a>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Simpan perubahan') }}</span>
                <span wire:loading>{{ __('Menyimpan...') }}</span>
            </button>
        </div>
    </form>
</div>
