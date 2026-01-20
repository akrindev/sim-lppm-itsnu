<div>
    <x-tabler.alert />

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Manajemen Cluster Ilmu</h3>
    </div>

    {{-- Main Split Panel Layout --}}
    <div class="row g-3">
        {{-- Left Panel: Tree View --}}
        <div class="col-md-4 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Hierarki Cluster</h4>
                    <div class="card-actions">
                        <button type="button" class="btn btn-primary btn-sm" wire:click="startAddCluster(null)"
                            title="Tambah Level 1">
                            <x-lucide-plus class="icon" />
                        </button>
                    </div>
                </div>
                <div class="card-body p-2">
                    {{-- Search Box --}}
                    <div class="mb-2">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <x-lucide-search class="icon" />
                            </span>
                            <input type="text" class="form-control form-control-sm" placeholder="Cari cluster..."
                                wire:model.live.debounce.300ms="search">
                        </div>
                    </div>

                    {{-- Add Cluster Form --}}
                    @if ($addingCluster)
                        <div class="mb-2 p-2 border rounded">
                            <div class="mb-2">
                                <input type="text"
                                    class="form-control form-control-sm @error('newClusterName') is-invalid @enderror"
                                    wire:model="newClusterName" placeholder="Nama cluster baru..."
                                    wire:keydown.enter="saveNewCluster" wire:keydown.escape="cancelAddCluster" autofocus>
                                @error('newClusterName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($newClusterParentId)
                                    <small class="text-muted">
                                        Parent: {{ ScienceCluster::find($newClusterParentId)?->name }}
                                    </small>
                                @endif
                            </div>
                            <div class="btn-list">
                                <button type="button" class="btn btn-success btn-sm" wire:click="saveNewCluster">
                                    <x-lucide-check class="icon" />
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelAddCluster">
                                    <x-lucide-x class="icon" />
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Tree View --}}
                    <div class="cluster-tree" style="max-height: 500px; overflow-y: auto;">
                        @forelse($this->level1Clusters as $level1)
                            <div class="cluster-tree-item" wire:key="l1-{{ $level1->id }}">
                                {{-- Level 1 Header --}}
                                <div class="d-flex align-items-center py-1 px-2 rounded cluster-tree-header
                                    {{ $selectedClusterId === $level1->id ? 'bg-primary-subtle' : '' }}"
                                    style="cursor: pointer;"
                                    @if ($editingClusterId !== $level1->id) wire:click="toggleLevel1({{ $level1->id }})" @endif>

                                    @if ($editingClusterId === $level1->id)
                                        {{-- Edit Mode --}}
                                        <div class="flex-grow-1 d-flex align-items-center gap-1" wire:click.stop>
                                            <input type="text"
                                                class="form-control form-control-sm @error('clusterNameInput') is-invalid @enderror"
                                                wire:model="clusterNameInput" wire:keydown.enter="saveCluster"
                                                wire:keydown.escape="cancelEditCluster" autofocus style="font-size: 0.85rem;">
                                            <button type="button" class="btn btn-success btn-sm p-1" wire:click="saveCluster">
                                                <x-lucide-check class="icon" style="width: 14px; height: 14px;" />
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm p-1"
                                                wire:click="cancelEditCluster">
                                                <x-lucide-x class="icon" style="width: 14px; height: 14px;" />
                                            </button>
                                        </div>
                                    @else
                                        {{-- Expand/Collapse Icon --}}
                                        <span class="me-1">
                                            @if ($this->isLevel1Expanded($level1->id))
                                                <x-lucide-chevron-down class="icon" style="width: 16px; height: 16px;" />
                                            @else
                                                <x-lucide-chevron-right class="icon" style="width: 16px; height: 16px;" />
                                            @endif
                                        </span>

                                        {{-- Cluster Name with Icon --}}
                                        <x-lucide-folder class="icon me-1 text-primary" style="width: 14px; height: 14px;" />
                                        <span class="flex-grow-1 text-truncate fw-semibold" style="font-size: 0.85rem;"
                                            title="{{ $level1->name }}" wire:click.stop="selectCluster({{ $level1->id }})">
                                            {{ $level1->name }}
                                        </span>

                                        {{-- Children Count Badge --}}
                                        <span class="badge bg-secondary-subtle text-secondary ms-1">
                                            {{ $level1->children_count }}
                                        </span>

                                        {{-- Action Buttons --}}
                                        <div class="btn-group ms-1" wire:click.stop>
                                            <button type="button" class="btn btn-ghost-success btn-sm p-1"
                                                wire:click="startAddCluster({{ $level1->id }})" title="Tambah Sub-Cluster">
                                                <x-lucide-plus class="icon" style="width: 12px; height: 12px;" />
                                            </button>
                                            <button type="button" class="btn btn-ghost-primary btn-sm p-1"
                                                wire:click="startEditCluster({{ $level1->id }})" title="Rename">
                                                <x-lucide-pencil class="icon" style="width: 12px; height: 12px;" />
                                            </button>
                                            <button type="button" class="btn btn-ghost-danger btn-sm p-1"
                                                wire:click="deleteCluster({{ $level1->id }})"
                                                wire:confirm="Hapus cluster '{{ $level1->name }}' beserta semua sub-cluster?"
                                                title="Hapus">
                                                <x-lucide-trash-2 class="icon" style="width: 12px; height: 12px;" />
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                {{-- Level 2 Children (Collapsible) --}}
                                @if ($this->isLevel1Expanded($level1->id) && $level1->children->isNotEmpty())
                                    <div class="cluster-tree-levels ps-3 border-start ms-2">
                                        @foreach ($level1->children as $level2)
                                            <div class="cluster-tree-item" wire:key="l2-{{ $level2->id }}">
                                                {{-- Level 2 Header --}}
                                                <div class="d-flex align-items-center py-1 px-2 rounded cluster-tree-header
                                                    {{ $selectedClusterId === $level2->id ? 'bg-primary text-white' : '' }}"
                                                    style="cursor: pointer; font-size: 0.8rem;"
                                                    @if ($editingClusterId !== $level2->id) wire:click="toggleLevel2({{ $level2->id }})" @endif>

                                                    @if ($editingClusterId === $level2->id)
                                                        {{-- Edit Mode --}}
                                                        <div class="flex-grow-1 d-flex align-items-center gap-1" wire:click.stop>
                                                            <input type="text"
                                                                class="form-control form-control-sm @error('clusterNameInput') is-invalid @enderror"
                                                                wire:model="clusterNameInput" wire:keydown.enter="saveCluster"
                                                                wire:keydown.escape="cancelEditCluster" autofocus
                                                                style="font-size: 0.75rem;">
                                                            <button type="button" class="btn btn-success btn-sm p-1"
                                                                wire:click="saveCluster">
                                                                <x-lucide-check class="icon" style="width: 12px; height: 12px;" />
                                                            </button>
                                                            <button type="button" class="btn btn-secondary btn-sm p-1"
                                                                wire:click="cancelEditCluster">
                                                                <x-lucide-x class="icon" style="width: 12px; height: 12px;" />
                                                            </button>
                                                        </div>
                                                    @else
                                                        {{-- Expand/Collapse Icon --}}
                                                        <span class="me-1">
                                                            @if ($this->isLevel2Expanded($level2->id))
                                                                <x-lucide-chevron-down class="icon"
                                                                    style="width: 14px; height: 14px;" />
                                                            @else
                                                                <x-lucide-chevron-right class="icon"
                                                                    style="width: 14px; height: 14px;" />
                                                            @endif
                                                        </span>

                                                        {{-- Cluster Name with Icon --}}
                                                        <x-lucide-folder-open class="icon me-1 text-success"
                                                            style="width: 12px; height: 12px;" />
                                                        <span class="flex-grow-1 text-truncate"
                                                            wire:click.stop="selectCluster({{ $level2->id }})">
                                                            {{ $level2->name }}
                                                        </span>

                                                        {{-- Children Count Badge --}}
                                                        <span
                                                            class="badge {{ $selectedClusterId === $level2->id ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }}"
                                                            style="font-size: 0.65rem;">
                                                            {{ $level2->children_count }}
                                                        </span>

                                                        {{-- Action Buttons --}}
                                                        <div class="btn-group ms-1" wire:click.stop>
                                                            <button type="button" class="btn btn-ghost-success btn-sm p-1"
                                                                wire:click="startAddCluster({{ $level2->id }})"
                                                                title="Tambah Sub-Cluster">
                                                                <x-lucide-plus class="icon"
                                                                    style="width: 10px; height: 10px;" />
                                                            </button>
                                                            <button type="button" class="btn btn-ghost-primary btn-sm p-1"
                                                                wire:click="startEditCluster({{ $level2->id }})"
                                                                title="Rename">
                                                                <x-lucide-pencil class="icon"
                                                                    style="width: 10px; height: 10px;" />
                                                            </button>
                                                            <button type="button" class="btn btn-ghost-danger btn-sm p-1"
                                                                wire:click="deleteCluster({{ $level2->id }})"
                                                                wire:confirm="Hapus cluster '{{ $level2->name }}'?"
                                                                title="Hapus">
                                                                <x-lucide-trash-2 class="icon"
                                                                    style="width: 10px; height: 10px;" />
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Level 3 Children (Collapsible) --}}
                                                @if ($this->isLevel2Expanded($level2->id) && $level2->children->isNotEmpty())
                                                    <div class="cluster-tree-levels ps-3 border-start ms-2">
                                                        @foreach ($level2->children as $level3)
                                                            <div class="d-flex align-items-center py-1 px-2 rounded cluster-tree-header
                                                                {{ $selectedClusterId === $level3->id ? 'bg-primary text-white' : '' }}"
                                                                style="cursor: pointer; font-size: 0.75rem;"
                                                                wire:click="selectCluster({{ $level3->id }})"
                                                                wire:key="l3-{{ $level3->id }}">

                                                                @if ($editingClusterId === $level3->id)
                                                                    {{-- Edit Mode --}}
                                                                    <div class="flex-grow-1 d-flex align-items-center gap-1"
                                                                        wire:click.stop>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm @error('clusterNameInput') is-invalid @enderror"
                                                                            wire:model="clusterNameInput"
                                                                            wire:keydown.enter="saveCluster"
                                                                            wire:keydown.escape="cancelEditCluster" autofocus
                                                                            style="font-size: 0.7rem;">
                                                                        <button type="button" class="btn btn-success btn-sm p-1"
                                                                            wire:click="saveCluster">
                                                                            <x-lucide-check class="icon"
                                                                                style="width: 10px; height: 10px;" />
                                                                        </button>
                                                                        <button type="button" class="btn btn-secondary btn-sm p-1"
                                                                            wire:click="cancelEditCluster">
                                                                            <x-lucide-x class="icon"
                                                                                style="width: 10px; height: 10px;" />
                                                                        </button>
                                                                    </div>
                                                                @else
                                                                    <span class="me-1">
                                                                        <x-lucide-minus class="icon"
                                                                            style="width: 12px; height: 12px;" />
                                                                    </span>

                                                                    {{-- Cluster Name with Icon --}}
                                                                    <x-lucide-file-text class="icon me-1 text-orange"
                                                                        style="width: 10px; height: 10px;" />
                                                                    <span class="flex-grow-1 text-truncate">
                                                                        {{ $level3->name }}
                                                                    </span>

                                                                    {{-- Action Buttons --}}
                                                                    <div class="btn-group ms-1" wire:click.stop>
                                                                        <button type="button"
                                                                            class="btn btn-ghost-primary btn-sm p-1"
                                                                            wire:click="startEditCluster({{ $level3->id }})"
                                                                            title="Rename">
                                                                            <x-lucide-pencil class="icon"
                                                                                style="width: 9px; height: 9px;" />
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-ghost-danger btn-sm p-1"
                                                                            wire:click="deleteCluster({{ $level3->id }})"
                                                                            wire:confirm="Hapus cluster '{{ $level3->name }}'?"
                                                                            title="Hapus">
                                                                            <x-lucide-trash-2 class="icon"
                                                                                style="width: 9px; height: 9px;" />
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-muted text-center py-3">
                                @if ($search)
                                    Tidak ada cluster yang cocok
                                @else
                                    Belum ada cluster
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel: Detail --}}
        <div class="col-md-8 col-lg-9">
            @if ($this->selectedCluster)
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title mb-1">
                                {{ $this->selectedCluster->name }}
                                <span class="badge bg-secondary-subtle text-secondary ms-2">
                                    Level {{ $this->selectedCluster->level }}
                                </span>
                            </div>
                            <div class="text-muted" style="font-size: 0.8rem;">
                                @if ($this->selectedCluster->parent)
                                    Parent: <strong>{{ $this->selectedCluster->parent->name }}</strong>
                                @else
                                    Cluster Level 1 (Root)
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Cluster Info --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label text-muted mb-1">Nama Cluster</label>
                                    <div class="fw-semibold">{{ $this->selectedCluster->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label text-muted mb-1">Level</label>
                                    <div class="fw-semibold">{{ $this->selectedCluster->level }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label text-muted mb-1">Jumlah Sub-Cluster</label>
                                    <div class="fw-semibold">{{ $this->selectedCluster->children->count() }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Sub-Clusters Section --}}
                        @if ($this->selectedCluster->level < 3)
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0 fw-semibold">
                                        Sub-Cluster Level {{ $this->selectedCluster->level + 1 }}
                                        <span class="badge bg-secondary-subtle text-secondary ms-1">
                                            {{ $this->selectedCluster->children->count() }}
                                        </span>
                                    </label>
                                    <button type="button" class="btn btn-primary btn-sm"
                                        wire:click="startAddCluster({{ $this->selectedCluster->id }})">
                                        <x-lucide-plus class="icon me-1" /> Tambah Sub-Cluster
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th style="width: 100px;">Sub-Cluster</th>
                                                <th style="width: 100px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($this->selectedCluster->children as $child)
                                                <tr wire:key="child-{{ $child->id }}"
                                                    wire:click="selectCluster({{ $child->id }})" style="cursor: pointer;">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if ($child->level === 2)
                                                                <x-lucide-folder-open class="icon me-2 text-success" />
                                                            @else
                                                                <x-lucide-file-text class="icon me-2 text-orange" />
                                                            @endif
                                                            {{ $child->name }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary-subtle text-secondary">
                                                            {{ $child->children_count }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-list flex-nowrap" wire:click.stop>
                                                            <button type="button" class="btn btn-ghost-primary btn-sm p-1"
                                                                wire:click="startEditCluster({{ $child->id }})"
                                                                title="Edit">
                                                                <x-lucide-pencil class="icon" />
                                                            </button>
                                                            <button type="button" class="btn btn-ghost-danger btn-sm p-1"
                                                                wire:click="deleteCluster({{ $child->id }})"
                                                                wire:confirm="Hapus cluster '{{ $child->name }}'?"
                                                                title="Hapus">
                                                                <x-lucide-trash-2 class="icon" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-muted text-center py-4">
                                                        <x-lucide-inbox class="icon mb-2" style="width: 32px; height: 32px;" />
                                                        <div>Belum ada sub-cluster</div>
                                                        <button type="button" class="btn btn-primary btn-sm mt-2"
                                                            wire:click="startAddCluster({{ $this->selectedCluster->id }})">
                                                            <x-lucide-plus class="icon me-1" /> Tambah Sub-Cluster Pertama
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <x-lucide-info class="icon me-2" />
                                Cluster Level 3 adalah level terakhir dan tidak dapat memiliki sub-cluster.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- Empty State --}}
                <div class="card">
                    <div class="card-body">
                        <div class="empty">
                            <div class="empty-img">
                                <x-lucide-layers class="icon" style="width: 64px; height: 64px; opacity: 0.5;" />
                            </div>
                            <p class="empty-title">Pilih Cluster Ilmu</p>
                            <p class="empty-subtitle text-muted">
                                Pilih cluster dari panel kiri untuk melihat detail dan mengelola sub-cluster.
                            </p>
                            @if ($this->level1Clusters->isEmpty())
                                <div class="empty-action">
                                    <button type="button" class="btn btn-primary" wire:click="startAddCluster(null)">
                                        <x-lucide-plus class="icon me-1" /> Tambah Cluster Level 1 Pertama
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Custom Styles --}}
    <style>
        .cluster-tree-header:hover {
            background-color: var(--tblr-bg-surface-secondary);
        }

        .cluster-tree-levels {
            border-color: var(--tblr-border-color) !important;
        }

        .btn-ghost-primary {
            color: var(--tblr-primary);
            background: transparent;
            border: none;
        }

        .btn-ghost-primary:hover {
            background-color: var(--tblr-primary-bg-subtle);
        }

        .btn-ghost-success {
            color: var(--tblr-success);
            background: transparent;
            border: none;
        }

        .btn-ghost-success:hover {
            background-color: var(--tblr-success-bg-subtle);
        }

        .btn-ghost-danger {
            color: var(--tblr-danger);
            background: transparent;
            border: none;
        }

        .btn-ghost-danger:hover {
            background-color: var(--tblr-danger-bg-subtle);
        }
    </style>
</div>
