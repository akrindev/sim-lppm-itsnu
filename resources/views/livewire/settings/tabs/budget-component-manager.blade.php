<div>
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Komponen Anggaran</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal"
                data-bs-target="#modal-budget-component">
                <x-lucide-plus class="icon" />
                Tambah Komponen Anggaran
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Kelompok</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Unit</th>
                        <th>Deskripsi</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($budgetComponents as $item)
                        <tr wire:key="budget-component-{{ $item->id }}">
                            <td><span class="bg-blue-lt badge">{{ $item->budgetGroup->code }}</span></td>
                            <td><span class="bg-green-lt badge">{{ $item->code }}</span></td>
                            <td>{{ $item->name }}</td>
                            <td><x-tabler.badge>{{ $item->unit ?? '-' }}</x-tabler.badge></td>
                            <td>{{ $item->description ?? '-' }}</td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-budget-component" wire:click="edit({{ $item->id }})">
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
            {{ $budgetComponents->links() }}
        </div>
    </div>
    <x-tabler.modal id="modal-budget-component" :title="$modalTitle">
        <x-slot:body>
            <form wire:submit="save" id="form-budget-component">
                <div class="mb-3">
                    <label class="form-label">Kelompok Anggaran</label>
                    <select wire:model="budgetGroupId" class="form-select">
                        <option value="">Pilih Kelompok</option>
                        @foreach ($budgetGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->code }} - {{ $group->name }}</option>
                        @endforeach
                    </select>
                    @error('budgetGroupId')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Kode</label>
                    <input type="text" wire:model="code" class="form-control" placeholder="Contoh: 1.1, 1.2">
                    @error('code')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="Nama komponen">
                    @error('name')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit</label>
                    <input type="text" wire:model="unit" class="form-control"
                        placeholder="Contoh: pcs, pack, liter, orang">
                    @error('unit')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi (Opsional)</label>
                    <textarea wire:model="description" class="form-control" rows="3" placeholder="Deskripsi komponen"></textarea>
                    @error('description')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-budget-component" class="btn btn-primary">Simpan</button>
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

    @script
        <script>
            const modal = document.getElementById('modal-budget-component');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', () => {
                    @this.call('resetForm');
                });
            }
        </script>
    @endscript
</div>
