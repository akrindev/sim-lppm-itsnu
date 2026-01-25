<div>
    <x-tabler.alert />
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Fakultas</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal"
                data-bs-target="#modal-faculty">
                <x-lucide-plus class="icon" />
                Tambah Fakultas
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kode</th>
                        <th>Institusi</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faculties as $item)
                        <tr wire:key="faculty-{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->institution?->name ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-faculty" wire:click="edit('{{ $item->id }}')">
                                        Edit
                                    </button>
                                    <button type="button" class="btn-outline-danger btn btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modal-confirm-delete-faculty" wire:click="confirmDelete('{{ $item->id }}')">
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
            {{ $faculties->links() }}
        </div>
    </div>
    
        
@teleport('body')
<x-tabler.modal-confirmation
            id="modal-confirm-delete-faculty"
            title="Konfirmasi Hapus"
            message="Apakah Anda yakin ingin menghapus {{ $deleteItemName ?? '' }}?"
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            component-id="{{ $this->getId() }}"
            on-confirm="handleConfirmDeleteAction"
        />
<x-tabler.modal id="modal-faculty" :title="$modalTitle" onHide="resetForm">
            <x-slot:body>
                <form wire:submit="save" id="form-faculty">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="Enter name">
                        @error('name')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" wire:model="code" class="form-control" placeholder="Enter code">
                        @error('code')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Institusi</label>
                        <select wire:model="institutionId" class="form-control">
                            <option value="">Select institution</option>
                            @foreach ($institutions as $institution)
                                <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                            @endforeach
                        </select>
                        @error('institutionId')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="form-faculty" class="btn btn-primary">Simpan</button>
            </x-slot:footer>
        </x-tabler.modal>
@endteleport
</div>
