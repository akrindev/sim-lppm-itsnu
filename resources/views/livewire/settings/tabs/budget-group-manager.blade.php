<div>
    <x-tabler.alert />
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Kelompok Anggaran</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal"
                data-bs-target="#modal-budget-group">
                <x-lucide-plus class="icon" />
                Tambah Kelompok Anggaran
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table-vcenter table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Persentase</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($budgetGroups as $item)
                        <tr wire:key="budget-group-{{ $item->id }}">
                            <td><span class="badge bg-blue-lt">{{ $item->code }}</span></td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->description ?? '-' }}</td>
                            <td>
                                @if ($item->percentage)
                                    <span class="badge bg-green-lt">{{ number_format($item->percentage, 2) }}%</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-budget-group" wire:click="edit({{ $item->id }})">
                                        Edit
                                    </button>
                                    <button type="button" class="btn-outline-danger btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-confirm-delete"
                                        wire:click="confirmDelete({{ $item->id }}, '{{ $item->name }}')">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $budgetGroups->links() }}
        </div>
    </div>
    <x-tabler.modal id="modal-budget-group" :title="$modalTitle" onHide="resetForm">
        <x-slot:body>
            <form wire:submit="save" id="form-budget-group">
                <div class="mb-3">
                    <label class="form-label">Kode</label>
                    <input type="text" wire:model="code" class="form-control" placeholder="Contoh: A, B, C">
                    @error('code')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="Nama kelompok">
                    @error('name')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi (Opsional)</label>
                    <textarea wire:model="description" class="form-control" rows="3" placeholder="Deskripsi kelompok"></textarea>
                    @error('description')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Persentase Alokasi (0-100%)</label>
                    <div class="input-group">
                        <input type="number" wire:model="percentage" class="form-control" placeholder="30"
                            step="0.01" min="0" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="form-hint">Kosongkan jika tidak ada batasan persentase untuk kelompok ini.</small>
                    @error('percentage')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-budget-group" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <x-tabler.modal id="modal-confirm-delete" title="Konfirmasi Hapus">
        <x-slot:body>
            <p>Apakah Anda yakin ingin menghapus <strong>{{ $deleteItemName ?? '' }}</strong>?</p>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger" wire:click="handleConfirmDeleteAction"
                data-bs-dismiss="modal">Ya, Hapus</button>
        </x-slot:footer>
    </x-tabler.modal>

</div>
