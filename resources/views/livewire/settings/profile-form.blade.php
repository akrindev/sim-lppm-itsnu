<div>
    <h3 class="mt-4 card-title">Informasi Dasar</h3>
    <div class="row g-3">
        <div class="col-md">
            <div class="form-label">Nama Lengkap <span class="text-danger">*</span></div>
            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
                placeholder="Masukkan nama lengkap" />
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md">
            <div class="form-label">Email <span class="text-danger">*</span></div>
            <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email"
                placeholder="Masukkan email" />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mt-1 row g-3">
        <div class="col-md">
            <div class="form-label">NIDN/NIM <span class="text-danger">*</span></div>
            <input type="text" class="form-control @error('identity_id') is-invalid @enderror"
                wire:model="identity_id" placeholder="Masukkan NIDN/NIM" />
            @error('identity_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md">
            <div class="form-label">Tipe User <span class="text-danger">*</span></div>
            <select class="form-select @error('type') is-invalid @enderror" wire:model="type">
                <option value="">Pilih Tipe</option>
                <option value="dosen">Dosen</option>
                <option value="mahasiswa">Mahasiswa</option>
            </select>
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md">
            <div class="form-label">ID SINTA</div>
            <input type="text" class="form-control @error('sinta_id') is-invalid @enderror" wire:model="sinta_id"
                placeholder="Masukkan ID SINTA (opsional)" />
            @error('sinta_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <h3 class="mt-4 card-title">Informasi Pribadi</h3>
    <div class="row g-3">
        <div class="col-md">
            <div class="form-label">Tempat Lahir</div>
            <input type="text" class="form-control @error('birthplace') is-invalid @enderror" wire:model="birthplace"
                placeholder="Masukkan tempat lahir" />
            @error('birthplace')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md">
            <div class="form-label">Tanggal Lahir</div>
            <input type="date" class="form-control @error('birthdate') is-invalid @enderror"
                wire:model="birthdate" />
            @error('birthdate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <div class="form-label">Alamat</div>
            <textarea class="form-control @error('address') is-invalid @enderror" wire:model="address" rows="3"
                placeholder="Masukkan alamat lengkap"></textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <h3 class="mt-4 card-title">Informasi Akademik</h3>
    <div class="row g-3">
        <div class="col-md">
            <div class="form-label">Institusi</div>
            <select class="form-select @error('institution_id') is-invalid @enderror" wire:model.live="institution_id"
                disabled>
                <option value="">Pilih Institusi</option>
                @foreach ($institutions as $institution)
                    <option value="{{ $institution['id'] }}">{{ $institution['name'] }}</option>
                @endforeach
            </select>
            @error('institution_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md">
            <div class="form-label">Program Studi</div>
            <select class="form-select @error('study_program_id') is-invalid @enderror" wire:model="study_program_id"
                disabled>
                <option value="">Pilih Program Studi</option>
                @foreach ($studyPrograms as $program)
                    <option value="{{ $program['id'] }}">{{ $program['name'] }}</option>
                @endforeach
            </select>
            @error('study_program_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
        <div class="mt-4">
            <div class="alert alert-warning">
                <div class="d-flex">
                    <div>
                        <x-lucide-alert-triangle class="icon alert-icon" />
                    </div>
                    <div class="ms-2">
                        <h4 class="alert-title">Email belum diverifikasi</h4>
                        <div class="text-muted">
                            Alamat email Anda belum diverifikasi.
                            <button type="button" class="ms-1 p-0 btn btn-link"
                                wire:click.prevent="resendVerificationNotification">
                                Klik di sini untuk mengirim ulang email verifikasi.
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success">
                    <div class="d-flex">
                        <div>
                            <x-lucide-check-circle class="icon alert-icon" />
                        </div>
                        <div class="ms-2">
                            <h4 class="alert-title">Email verifikasi dikirim</h4>
                            <div class="text-muted">
                                Tautan verifikasi baru telah dikirim ke alamat email Anda.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="bg-transparent mt-auto card-footer">
        <div class="justify-content-end btn-list">
            <button type="button" class="btn" wire:click="resetForm">
                Batal
            </button>
            <button type="submit" class="btn btn-primary" wire:click="updateProfileInformation"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan Perubahan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </div>

    <x-tabler.alert />
</div>
