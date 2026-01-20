<div>
    <x-tabler.alert />
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Prioritas Nasional</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal"
                data-bs-target="#modal-national-priority">
                <x-lucide-plus class="icon" />
                Tambah Prioritas Nasional
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nationalPriorities as $item)
                        <tr wire:key="priority-{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-national-priority"
                                        wire:click="edit('{{ $item->id }}')">
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
            {{ $nationalPriorities->links() }}
        </div>
    </div>
    @teleport('body')
        <x-tabler.modal id="modal-national-priority" :title="$modalTitle" onHide="resetForm">
            <x-slot:body>
                <form wire:submit="save" id="form-national-priority">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="Enter name">
                        @error('name')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="form-national-priority" class="btn btn-primary">Simpan</button>
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
    @endteleport
</div>
