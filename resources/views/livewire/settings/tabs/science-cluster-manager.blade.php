<div>
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Cluster Ilmu</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal"
                data-bs-target="#modal-science-cluster">
                <x-lucide-plus class="icon" />
                Tambah Cluster Ilmu
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Parent</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scienceClusters as $item)
                        <tr wire:key="cluster-{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->parent?->name ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-science-cluster" wire:click="edit({{ $item->id }})">
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
            {{ $scienceClusters->links() }}
        </div>
    </div>
    <x-tabler.modal id="modal-science-cluster" :title="$modalTitle">
        <x-slot:body>
            <form wire:submit="save" id="form-science-cluster">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="Enter name">
                    @error('name')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Parent</label>
                    <select wire:model="parentId" class="form-control">
                        <option value="">Select parent</option>
                        @foreach ($parentClusters as $cluster)
                            <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                        @endforeach
                    </select>
                    @error('parentId')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-science-cluster" class="btn btn-primary">Simpan</button>
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
            const modal = document.getElementById('modal-science-cluster');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', () => {
                    @this.call('resetForm');
                });
            }
        </script>
    @endscript
</div>
