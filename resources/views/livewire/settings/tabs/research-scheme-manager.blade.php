<div>
    <x-tabler.alert />
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Skema Penelitian</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal"
                data-bs-target="#modal-research-scheme">
                <x-lucide-plus class="icon" />
                Tambah Skema Penelitian
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Strata</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($researchSchemes as $item)
                        <tr wire:key="scheme-{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->strata }}</td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-research-scheme" wire:click="edit('{{ $item->id }}')">
                                        Edit
                                    </button>
                                    <button type="button" class="btn-outline-danger btn btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modal-confirm-delete-research-scheme" wire:click="confirmDelete('{{ $item->id }}')">
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
            {{ $researchSchemes->links() }}
        </div>
    </div>
    
        
@teleport('body')
<x-tabler.modal-confirmation
            id="modal-confirm-delete-research-scheme"
            title="Konfirmasi Hapus"
            message="Apakah Anda yakin ingin menghapus {{ $deleteItemName ?? '' }}?"
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            component-id="{{ $this->getId() }}"
            on-confirm="handleConfirmDeleteAction"
        />
<x-tabler.modal id="modal-research-scheme" :title="$modalTitle" onHide="resetForm">
            <x-slot:body>
                <form wire:submit="save" id="form-research-scheme">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="Enter name">
                        @error('name')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Strata</label>
                        <select wire:model="strata" class="form-control">
                            <option value="">Select strata</option>
                            <option value="Dasar">Dasar</option>
                            <option value="Terapan">Terapan</option>
                            <option value="Pengembangan">Pengembangan</option>
                        </select>
                        @error('strata')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="form-research-scheme" class="btn btn-primary">Simpan</button>
            </x-slot:footer>
        </x-tabler.modal>
@endteleport
</div>
