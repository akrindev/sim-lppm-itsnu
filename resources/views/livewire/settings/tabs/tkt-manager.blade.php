<div>
    <x-tabler.alert />
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#" wire:click.prevent="viewTypes" class="{{ $currentTab === 'types' ? 'active' : '' }}">
                    Kategori TKT
                </a>
            </li>
            @if ($currentTab === 'levels' || $currentTab === 'indicators')
                <li class="breadcrumb-item">
                    <a href="#" wire:click.prevent="viewLevels('{{ $selectedType }}')"
                        class="{{ $currentTab === 'levels' ? 'active' : '' }}">
                        {{ $selectedType }}
                    </a>
                </li>
            @endif
            @if ($currentTab === 'indicators')
                <li class="breadcrumb-item active" aria-current="page">
                    Level {{ $levelInfo->level ?? '' }}
                </li>
            @endif
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">
            @if ($currentTab === 'types')
                Manajemen Kategori TKT
            @elseif($currentTab === 'levels')
                Level TKT: {{ $selectedType }}
            @elseif($currentTab === 'indicators')
                Indikator TKT: {{ $selectedType }} - Level {{ $levelInfo->level ?? '' }}
            @endif
        </h3>
        <div>
            @if ($currentTab === 'types')
                <button class="btn btn-primary" wire:click="createType">
                    <x-lucide-plus class="icon me-2" />
                    Tambah Kategori
                </button>
            @elseif($currentTab === 'indicators')
                <button class="btn btn-primary" wire:click="createIndicator">
                    <x-lucide-plus class="icon me-2" />
                    Tambah Indikator
                </button>
            @endif
        </div>
    </div>

    <!-- Types View -->
    @if ($currentTab === 'types')
        <div class="card">
            <div class="table-responsive">
                <table class="table-vcenter card-table table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                            <tr>
                                <td>{{ $type }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <button class="btn btn-sm btn-outline-primary"
                                            wire:click="viewLevels('{{ $type }}')">
                                            Kelola Level
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning"
                                            wire:click="editType('{{ $type }}')">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            wire:click="deleteType('{{ $type }}')"
                                            wire:confirm="Apakah Anda yakin ingin menghapus kategori ini beserta seluruh level dan indikatornya?">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-muted text-center">Belum ada kategori TKT</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Levels View -->
    @if ($currentTab === 'levels')
        <div class="card">
            <div class="table-responsive">
                <table class="table-vcenter card-table table">
                    <thead>
                        <tr>
                            <th class="w-1">Level</th>
                            <th>Deskripsi</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($levels as $level)
                            <tr>
                                <td>{{ $level->level }}</td>
                                <td>{{ Str::limit($level->description, 100) }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <button class="btn btn-sm btn-outline-primary"
                                            wire:click="viewIndicators({{ $level->id }})">
                                            Indikator ({{ $level->indicators->count() }})
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning"
                                            wire:click="editLevel({{ $level->id }})">
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted text-center">Belum ada level</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Indicators View -->
    @if ($currentTab === 'indicators')
        <div class="card">
            <div class="table-responsive">
                <table class="table-vcenter card-table table">
                    <thead>
                        <tr>
                            <th class="w-1">Kode</th>
                            <th>Indikator</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($indicators as $indicator)
                            <tr>
                                <td>{{ $indicator->code }}</td>
                                <td>{{ $indicator->indicator }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <button class="btn btn-sm btn-outline-warning"
                                            wire:click="editIndicator({{ $indicator->id }})">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            wire:click="deleteIndicator({{ $indicator->id }})"
                                            wire:confirm="Hapus indikator ini?">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted text-center">Belum ada indikator</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modal Type -->
    @teleport('body')
        <x-tabler.modal id="modal-type" title="{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori' }}">
            <x-slot:body>
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control @error('typeName') is-invalid @enderror"
                        wire:model="typeName">
                    @error('typeName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" wire:click="saveType">Simpan</button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport

    <!-- Modal Level -->
    @teleport('body')
        <x-tabler.modal id="modal-level" title="Edit Level">
            <x-slot:body>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Level</label>
                    <textarea class="form-control @error('levelDescription') is-invalid @enderror" wire:model="levelDescription"
                        rows="4"></textarea>
                    @error('levelDescription')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" wire:click="saveLevel">Simpan</button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport

    <!-- Modal Indicator -->
    @teleport('body')
        <x-tabler.modal id="modal-indicator" title="{{ $editingId ? 'Edit Indikator' : 'Tambah Indikator' }}">
            <x-slot:body>
                <div class="mb-3">
                    <label class="form-label">Kode (Opsional)</label>
                    <input type="text" class="form-control @error('indicatorCode') is-invalid @enderror"
                        wire:model="indicatorCode" placeholder="Contoh: 1.1">
                    @error('indicatorCode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Indikator</label>
                    <textarea class="form-control @error('indicatorText') is-invalid @enderror" wire:model="indicatorText" rows="3"></textarea>
                    @error('indicatorText')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </x-slot:body>
            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" wire:click="saveIndicator">Simpan</button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport>
</div>
