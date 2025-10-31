<div>
    <x-slot:title>Edit Pengguna</x-slot:title>
    <x-slot:pageTitle>Edit Pengguna</x-slot:pageTitle>
    <x-slot:pageSubtitle>Perbarui profil pengguna dan penetapan peran.</x-slot:pageSubtitle>
    <x-slot:pageActions>
        <a href="{{ route('users.show', $user) }}" class="btn-outline-secondary btn" wire:navigate>
            Lihat profil
        </a>
    </x-slot:pageActions>

    <x-tabler.alert />

    <form wire:submit.prevent="save" class="card card-stacked" novalidate>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="user-name">
                            Nama <span class="text-danger">*</span>
                        </label>
                        <input id="user-name" type="text" class="form-control @error('name') is-invalid @enderror"
                            wire:model.defer="name" autocomplete="name" placeholder="Masukkan nama lengkap" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="user-email">
                            Alamat email <span class="text-danger">*</span>
                        </label>
                        <input id="user-email" type="email" class="form-control @error('email') is-invalid @enderror"
                            wire:model.defer="email" autocomplete="email" placeholder="nama@contoh.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">
                            Peran <span class="text-muted">(opsional)</span>
                        </label>
                        <div class="form-selectgroup @error('selectedRole') is-invalid @enderror">
                            @foreach ($roleOptions as $option)
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="role" value="{{ $option['value'] }}"
                                        class="form-selectgroup-input" wire:model.defer="selectedRole"
                                        @if ($selectedRole === $option['value']) checked @endif>
                                    <span class="form-selectgroup-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-1 icon">
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
                    <h3 class="mb-4">Informasi Identitas</h3>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="identity-id">
                            ID Identitas <span class="text-danger">*</span>
                        </label>
                        <input id="identity-id" type="text"
                            class="form-control @error('identity_id') is-invalid @enderror"
                            wire:model.defer="identity_id" placeholder="mis., NIP, NIDN, atau ID Karyawan" required>
                        @error('identity_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="birthplace">
                            Tempat Lahir <span class="text-muted">(opsional)</span>
                        </label>
                        <input id="birthplace" type="text"
                            class="form-control @error('birthplace') is-invalid @enderror" wire:model.defer="birthplace"
                            placeholder="Contoh: Kota, Provinsi">
                        @error('birthplace')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="birthdate">
                            Tanggal Lahir <span class="text-muted">(opsional)</span>
                        </label>
                        <input id="birthdate" type="date"
                            class="form-control @error('birthdate') is-invalid @enderror" wire:model.defer="birthdate">
                        @error('birthdate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="address">
                            Alamat <span class="text-muted">(opsional)</span>
                        </label>
                        <textarea id="address" class="form-control @error('address') is-invalid @enderror" wire:model.defer="address"
                            rows="3" placeholder="Masukkan alamat lengkap Anda"></textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="sinta-id">
                            ID SINTA <span class="text-muted">(opsional)</span>
                        </label>
                        <input id="sinta-id" type="text"
                            class="form-control @error('sinta_id') is-invalid @enderror" wire:model.defer="sinta_id"
                            placeholder="mis., 1234567">
                        @error('sinta_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="identity-type">
                            Tipe Identitas <span class="text-danger">*</span>
                        </label>
                        <select id="identity-type" class="form-select @error('type') is-invalid @enderror"
                            wire:model.defer="type" required>
                            <option value="">Pilih tipe identitas...</option>
                            <option value="dosen">Dosen</option>
                            <option value="mahasiswa">Mahasiswa</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="institution">
                            Institusi
                        </label>
                        <select id="institution" class="form-select @error('institution_id') is-invalid @enderror"
                            wire:model.live="institution_id" disabled>
                            <option value="">Pilih institusi...</option>
                            @foreach ($institutionOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        @error('institution_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="study-program">
                            Program Studi <span class="text-muted">(opsional)</span>
                        </label>
                        <select id="study-program"
                            class="form-select @error('study_program_id') is-invalid @enderror"
                            wire:model.defer="study_program_id" @disabled(empty($studyProgramOptions))>
                            <option value="">Pilih program studi...</option>
                            @foreach ($studyProgramOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        @error('study_program_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="d-block form-label">Verifikasi email</label>
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model.defer="emailVerified">
                            <span class="form-check-label">Tandai email sebagai terverifikasi</span>
                        </label>
                        @error('emailVerified')
                            <div class="mt-1 text-danger small">{{ $message }}</div>
                        @enderror
                        <p class="mt-2 text-secondary">
                            Alihkan untuk mengatur status verifikasi secara manual.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between card-footer" x-data="{ show: false }"
            x-on:user-updated.window="console.log('updated');show = true; setTimeout(() => show = false, 5000);">
            <a href="{{ route('users.show', $user) }}" class="btn btn-link" wire:navigate>
                Batal
            </a>
            <div x-show="show" class="text-green fw-bold">
                <x-lucide-check-circle class="me-1 icon" />
                Pengguna telah diperbarui.
            </div>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan perubahan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
