<div>
    <div class="card border-0">
        <div class="card-header bg-transparent">
            <h3 class="card-title">
                <span class="text-danger">
                    <x-lucide-trash-2 class="icon me-1" />
                    Zona Berbahaya
                </span>
            </h3>
            <div class="card-subtitle">Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</div>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <div class="d-flex">
                    <div class="me-2">
                        <x-lucide-alert-triangle class="icon alert-icon" />
                    </div>
                    <div>
                        <h4 class="alert-title">Hapus Akun Permanen</h4>
                        <div class="text-muted">
                            Setelah akun Anda dihapus, semua sumber daya dan data akan hilang secara permanen. Tindakan ini tidak dapat dibatalkan.
                        </div>
                    </div>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-danger"
                data-bs-toggle="modal"
                data-bs-target="#confirmUserDeletionModal"
            >
                <x-lucide-trash-2 class="icon me-1" />
                Hapus Akun Saya
            </button>
        </div>
    </div>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUserDeletionLabel">Konfirmasi Penghapusan Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <div class="d-flex">
                            <div>
                                <x-lucide-alert-triangle class="icon alert-icon" />
                            </div>
                            <div class="ms-2">
                                <h4 class="alert-title">Peringatan!</h4>
                                <div class="text-muted">
                                    Apakah Anda yakin ingin menghapus akun Anda secara permanen? Tindakan ini tidak dapat dibatalkan.
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted mb-3">
                        Semua data yang terkait dengan akun Anda akan dihapus secara permanen, termasuk:
                    </p>
                    <ul class="text-muted">
                        <li>Profil dan informasi personal</li>
                        <li>Riwayat proposal penelitian</li>
                        <li>Riwayat pengajuan Abdimas</li>
                        <li>Semua file dan dokumen yang diunggah</li>
                        <li>Riwayat aktivitas dan log</li>
                    </ul>

                    <div class="mt-3">
                        <div class="form-label">Masukkan password untuk konfirmasi</div>
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan password Anda"
                            wire:model="password"
                        />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteUser">
                        Ya, Hapus Akun Permanen
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('user-deleted', () => {
                const modal = document.getElementById('confirmUserDeletionModal');
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            });
        });
    </script>
    @endpush
</div>
