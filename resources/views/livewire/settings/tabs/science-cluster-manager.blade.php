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

        <div class="card-body">
            <x-tabler.nav-segmented count="3">
                <button
                    class="nav-link {{ $selectedLevel === 1 ? 'active' : '' }}"
                    role="tab"
                    wire:click="setSelectedLevel(1)"
                    aria-selected="{{ $selectedLevel === 1 ? 'true' : 'false' }}"
                >
                    Level 1
                </button>
                <button
                    class="nav-link {{ $selectedLevel === 2 ? 'active' : '' }}"
                    role="tab"
                    wire:click="setSelectedLevel(2)"
                    aria-selected="{{ $selectedLevel === 2 ? 'true' : 'false' }}"
                >
                    Level 2
                </button>
                <button
                    class="nav-link {{ $selectedLevel === 3 ? 'active' : '' }}"
                    role="tab"
                    wire:click="setSelectedLevel(3)"
                    aria-selected="{{ $selectedLevel === 3 ? 'true' : 'false' }}"
                >
                    Level 3
                </button>
            </x-tabler.nav-segmented>
        </div>

        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama Cluster</th>
                        @if($selectedLevel === 2)
                            <th>Parent (Level 1)</th>
                        @elseif($selectedLevel === 3)
                            <th>Parent (Level 2)</th>
                        @endif
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($selectedLevel === 1)
                        @foreach ($level1Clusters as $cluster)
                            <tr wire:key="cluster-{{ $cluster->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <x-lucide-folder class="icon me-2 text-primary" />
                                        <strong>{{ $cluster->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modal-science-cluster" wire:click="edit({{ $cluster->id }})">
                                            Edit
                                        </button>
                                        <button type="button" class="btn-outline-danger btn btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modal-confirm-delete"
                                            wire:click="confirmDelete({{ $cluster->id }}, '{{ $cluster->name }}')">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @elseif($selectedLevel === 2)
                        @foreach ($level2Clusters as $cluster)
                            <tr wire:key="cluster-{{ $cluster->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <x-lucide-folder-open class="icon me-2 text-success" />
                                        <span>{{ $cluster->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <x-tabler.badge color="primary">{{ $cluster->parent?->name ?? 'N/A' }}</x-tabler.badge>
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modal-science-cluster" wire:click="edit({{ $cluster->id }})">
                                            Edit
                                        </button>
                                        <button type="button" class="btn-outline-danger btn btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modal-confirm-delete"
                                            wire:click="confirmDelete({{ $cluster->id }}, '{{ $cluster->name }}')">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @elseif($selectedLevel === 3)
                        @foreach ($level3Clusters as $cluster)
                            <tr wire:key="cluster-{{ $cluster->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <x-lucide-file-text class="icon me-2 text-warning" />
                                        <span>{{ $cluster->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <x-tabler.badge color="success">{{ $cluster->parent?->name ?? 'N/A' }}</x-tabler.badge>
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modal-science-cluster" wire:click="edit({{ $cluster->id }})">
                                            Edit
                                        </button>
                                        <button type="button" class="btn-outline-danger btn btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modal-confirm-delete"
                                            wire:click="confirmDelete({{ $cluster->id }}, '{{ $cluster->name }}')">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
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
                        @if($level1Clusters->count() > 0)
                            @foreach ($level1Clusters as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }} (Level 1)</option>
                            @endforeach
                        @endif
                        @if($level2Clusters->count() > 0)
                            @foreach ($level2Clusters as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }} (Level 2)</option>
                            @endforeach
                        @endif
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
