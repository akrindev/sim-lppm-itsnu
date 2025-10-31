<div>
    <div class="d-flex mb-3">
        <h2 class="mb-0">Profil Saya</h2>
    </div>
    <p class="mb-4 text-muted">Informasi profil Anda dan pengaturan verifikasi email.</p>

    <div class="mb-3 card">
        <div class="card-body">
            <h3 class="card-title">Detail Profil</h3>
            <div class="align-items-center row">
                <div class="col-auto">
                    <span class="avatar avatar-xl">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}

                    </span>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary" wire:click="$dispatch('open-avatar-upload')">
                        Ubah Avatar
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-ghost-danger" wire:click="removeAvatar">
                        Hapus Avatar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="updateProfileInformation">
        <div class="mb-3 card">
            <div class="card-body">
                <h3 class="card-title">Informasi Profil</h3>
                <div class="row g-3">
                    <div class="col-md">
                        <div class="form-label">Nama Lengkap</div>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
                            placeholder="Masukkan nama lengkap" />
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md">
                        <div class="form-label">Email</div>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            wire:model="email" placeholder="Masukkan email" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
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
        @endif

        <div class="bg-transparent card-footer">
            <div class="justify-content-end btn-list">
                <button type="button" class="btn" wire:click="resetForm">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Simpan Perubahan</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>

    <div class="mt-4">
        <livewire:settings.delete-user-form />
    </div>
</div>
