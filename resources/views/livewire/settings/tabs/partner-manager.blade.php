<div>
    <x-tabler.alert />
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Mitra</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal" data-bs-target="#modal-partner">
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
                                    <button type="button" class="btn-outline-warning btn btn-sm"
                                        wire:click="edit('{{ $item->id }}')" data-bs-toggle="modal" data-bs-target="#modal-partner">
                                        Edit
                                    </button>
                                    <button type="button" class="btn-outline-danger btn btn-sm"
                                        wire:click="confirmDelete('{{ $item->id }}')" wire:loading.attr="disabled">
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
    
        
@teleport('body')
<x-tabler.modal-confirmation
            wire:key="modal-confirm-delete-partner"
            id="modal-confirm-delete-partner"
            title="Konfirmasi Hapus"
            message="Apakah Anda yakin ingin menghapus {{ $deleteItemName ?? '' }}?"
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            component-id="{{ $this->getId() }}"
            on-confirm="handleConfirmDeleteAction"
        />
<x-tabler.modal wire:key="modal-partner" id="modal-partner" :title="$modalTitle" onHide="resetForm" component-id="{{ $this->getId() }}">
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
                <button type="submit" form="form-partner" class="btn btn-primary" wire:loading.class="btn-loading" wire:target="save">Simpan</button>
            </x-slot:footer>
        </x-tabler.modal>
@endteleport
</div>
