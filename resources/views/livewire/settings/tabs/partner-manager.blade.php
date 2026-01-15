<div>
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Mitra</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal"
                data-bs-target="#modal-partner">
                <x-lucide-plus class="icon" />
                Tambah Mitra
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Alamat</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($partners as $item)
                        <tr wire:key="partner-{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->address }}</td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-partner" wire:click="edit('{{ $item->id }}')">
                                        Edit
                                    </button>
                                    <button type="button" class="btn-outline-danger btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-confirm-delete"
                                        wire:click="confirmDelete('{{ $item->id }}', '{{ $item->name }}')">
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
            {{ $partners->links() }}
        </div>
    </div>
    <x-tabler.modal id="modal-partner" :title="$modalTitle" onHide="resetForm">
        <x-slot:body>
            <form wire:submit="save" id="form-partner">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="Enter name">
                    @error('name')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis</label>
                    <input type="text" wire:model="type" class="form-control" placeholder="Enter type">
                    @error('type')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea wire:model="address" class="form-control" placeholder="Enter address" rows="3"></textarea>
                    @error('address')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-partner" class="btn btn-primary">Simpan</button>
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
