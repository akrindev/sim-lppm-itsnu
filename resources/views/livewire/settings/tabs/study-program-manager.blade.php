<div>
    <x-tabler.alert />
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Program Studi</h3>
            <button type="button" class="btn btn-primary" wire:click='create' data-bs-toggle="modal" data-bs-target="#modal-study-program">
                <x-lucide-plus class="icon" />
                Tambah Program Studi
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Institusi</th>
                        <th>Fakultas</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($studyPrograms as $item)
                        <tr wire:key="program-{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->institution?->name ?? 'N/A' }}</td>
                            <td>{{ $item->faculty?->name ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm"
                                        wire:click="edit('{{ $item->id }}')" data-bs-toggle="modal" data-bs-target="#modal-study-program">
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
            {{ $studyPrograms->links() }}
        </div>
    </div>
    
        
@teleport('body')
<x-tabler.modal-confirmation
            wire:key="modal-confirm-delete-study-program"
            id="modal-confirm-delete-study-program"
            title="Konfirmasi Hapus"
            message="Apakah Anda yakin ingin menghapus {{ $deleteItemName ?? '' }}?"
            confirm-text="Ya, Hapus"
            cancel-text="Batal"
            component-id="{{ $this->getId() }}"
            on-confirm="handleConfirmDeleteAction"
        />
<x-tabler.modal wire:key="modal-study-program" id="modal-study-program" :title="$modalTitle" onHide="resetForm" component-id="{{ $this->getId() }}">
            <x-slot:body>
                <form wire:submit="save" id="form-study-program">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="Enter name">
                        @error('name')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Institusi</label>
                        <select wire:model.live="institutionId" class="form-control">
                            <option value="">Select institution</option>
                            @foreach ($institutions as $institution)
                                <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                            @endforeach
                        </select>
                        @error('institutionId')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fakultas</label>
                        <select wire:model="facultyId" class="form-control" wire:key="faculty-select-{{ $institutionId }}">
                            <option value="">Select faculty</option>
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                        @error('facultyId')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="form-study-program" class="btn btn-primary" wire:loading.class="btn-loading" wire:target="save">Simpan</button>
            </x-slot:footer>
        </x-tabler.modal>
@endteleport
</div>
