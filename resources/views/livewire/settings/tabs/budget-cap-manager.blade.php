<div>
    <x-tabler.alert />
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Pengaturan Anggaran Tahunan</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal"
                data-bs-target="#modal-budget-cap">
                <x-lucide-plus class="icon" />
                Tambah Pengaturan Anggaran
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table-vcenter table">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Batas Anggaran Penelitian</th>
                        <th>Batas Anggaran Pengabdian</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($budgetCaps as $item)
                        <tr wire:key="budget-cap-{{ $item->id }}">
                            <td><span class="badge bg-blue-lt">{{ $item->year }}</span></td>
                            <td>
                                @if ($item->research_budget_cap)
                                    <span class="text-success fw-bold">Rp
                                        {{ number_format($item->research_budget_cap, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">Tidak dibatasi</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->community_service_budget_cap)
                                    <span class="text-success fw-bold">Rp
                                        {{ number_format($item->community_service_budget_cap, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">Tidak dibatasi</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-budget-cap" wire:click="edit('{{ $item->id }}')">
                                        Edit
                                    </button>
                                    <button type="button" class="btn-outline-danger btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-confirm-delete"
                                        wire:click="confirmDelete('{{ $item->id }}', '{{ $item->year }}')">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center">Belum ada pengaturan anggaran</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $budgetCaps->links() }}
        </div>
    </div>

    @teleport('body')
        <x-tabler.modal id="modal-budget-cap" :title="$modalTitle" onHide="resetForm">
            <x-slot:body>
                <form wire:submit="save" id="form-budget-cap">
                    <div class="mb-3">
                        <label class="form-label required">Tahun Anggaran</label>
                        <input type="number" wire:model="year" class="form-control" placeholder="2025" min="2000"
                            max="2100">
                        @error('year')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batas Anggaran Penelitian</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" wire:model="research_budget_cap" class="form-control"
                                placeholder="50000000" step="1000" min="0">
                        </div>
                        <small class="form-hint">Kosongkan jika tidak ada batasan anggaran untuk penelitian.</small>
                        @error('research_budget_cap')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batas Anggaran Pengabdian Masyarakat</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" wire:model="community_service_budget_cap" class="form-control"
                                placeholder="30000000" step="1000" min="0">
                        </div>
                        <small class="form-hint">Kosongkan jika tidak ada batasan anggaran untuk pengabdian.</small>
                        @error('community_service_budget_cap')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="form-budget-cap" class="btn btn-primary">Simpan</button>
            </x-slot:footer>
        </x-tabler.modal>

        <x-tabler.modal id="modal-confirm-delete" title="Konfirmasi Hapus">
            <x-slot:body>
                <p>Apakah Anda yakin ingin menghapus pengaturan anggaran untuk tahun
                    <strong>{{ $deleteItemYear ?? '' }}</strong>?</p>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" wire:click="handleConfirmDeleteAction"
                    data-bs-dismiss="modal">Ya, Hapus</button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport
</div>
