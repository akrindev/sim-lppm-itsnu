<div>
    <x-tabler.alert />
    <div class="mb-3">
        <div class="form-label">Kata Sandi Saat Ini</div>
        <div class="input-group input-group-flat">
            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                wire:model="current_password" placeholder="Masukkan kata sandi saat ini" autocomplete="current-password" />
            @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <div class="form-label">Kata Sandi Baru</div>
        <div class="input-group input-group-flat">
            <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password"
                placeholder="Masukkan kata sandi baru" autocomplete="new-password" />
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <div class="form-label">Konfirmasi Kata Sandi</div>
        <div class="input-group input-group-flat">
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                wire:model="password_confirmation" placeholder="Konfirmasi kata sandi baru"
                autocomplete="new-password" />
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="bg-transparent mt-auto card-footer">
        <div class="justify-content-end btn-list">
            <button type="button" class="btn" wire:click="resetForm">
                Batal
            </button>
            <button type="submit" class="btn btn-primary" wire:click="updatePassword" wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan Kata Sandi</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </div>

    @session('status')
        <div class="mt-3 alert alert-success">
            Kata sandi berhasil diperbarui.
        </div>
    @endsession

    <div class="mt-3 alert alert-info">
        <h4 class="alert-title">Tips Keamanan Kata Sandi</h4>
        <div class="text-muted">
            Gunakan minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, angka, dan simbol untuk keamanan
            maksimal.
        </div>
    </div>
</div>
