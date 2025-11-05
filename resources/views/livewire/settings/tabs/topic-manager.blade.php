<div>
    <div class="card">
        <div class="d-flex align-items-center justify-content-between card-header">
            <h3 class="card-title">Topik</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-topic">
                <x-lucide-plus class="icon" />
                Tambah Topik
            </button>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tema</th>
                        <th class="w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topics as $item)
                        <tr wire:key="topic-{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->theme?->name ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn-outline-warning btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-topic" wire:click="edit({{ $item->id }})">
                                        Edit
                                    </button>
                                    <button type="button" class="btn-outline-danger btn btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-confirm-delete"
                                        wire:click="$set('deleteItemId', {{ $item->id }}); $set('deleteItemName', '{{ $item->name }}')">
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
            {{ $topics->links() }}
        </div>
    </div>
    @teleport('body')
        <x-tabler.modal id="modal-topic" :title="$modalTitle">
            <x-slot:body>
                <form wire:submit="save" id="form-topic">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="Enter name">
                        @error('name')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tema</label>
                        <select wire:model="themeId" class="form-control">
                            <option value="">Select theme</option>
                            @foreach ($themes as $theme)
                                <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                            @endforeach
                        </select>
                        @error('themeId')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="form-topic" class="btn btn-primary">Simpan</button>
            </x-slot:footer>
        </x-tabler.modal>

        <x-tabler.modal id="modal-confirm-delete" title="Konfirmasi Hapus" on-hide="resetConfirmDelete">
            <x-slot:body>
                <p>Apakah Anda yakin ingin menghapus <strong>{{ $deleteItemName ?? '' }}</strong>?</p>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" wire:click="handleConfirmDeleteAction"
                    data-bs-dismiss="modal">Ya, Hapus</button>
            </x-slot:footer>
        </x-tabler.modal>

        <script>
            document.addEventListener('livewire:init', () => {
                const modal = document.getElementById('modal-topic');
                if (modal) {
                    modal.addEventListener('hidden.bs.modal', () => {
                        $wire.call('resetForm');
                    });
                }
            });
        </script>
    @endteleport
</div>
