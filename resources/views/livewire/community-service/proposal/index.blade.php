<x-slot:title>Pengabdian</x-slot:title>
<x-slot:pageTitle>Daftar Pengabdian kepada Masyarakat</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola proposal pengabdian Anda dengan fitur lengkap.</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        <a href="{{ route('community-service.proposal.create') }}" wire:navigate class="btn btn-primary">
            <x-lucide-plus class="icon" />
            Usulan Pengabdian Baru
        </a>
    </div>
</x-slot:pageActions>

<div>

    <div class="mb-3 row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-6">
                            <input type="text" class="form-control"
                                placeholder="Cari berdasarkan judul atau ringkasan..."
                                wire:model.live.debounce.300ms="search" />
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="all">Semua Status</option>
                                <option value="draft">Draft</option>
                                <option value="submitted">Diajukan</option>
                                <option value="under_review">Dalam Review</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="col-md-3">
                            <button type="button" class="btn-outline-secondary w-100 btn" wire:click="resetFilters">
                                <x-lucide-rotate-ccw class="icon" />
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Stats -->
    <div class="mb-3 row row-deck row-cards">
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-title">
                            {{ $statusStats['all'] }}
                        </h3>
                        <div class="text-secondary">Total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-title">
                            {{ $statusStats['draft'] }}
                        </h3>
                        <div class="text-secondary">Draft</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-title">
                            {{ $statusStats['submitted'] }}
                        </h3>
                        <div class="text-secondary">Diajukan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-title">
                            {{ $statusStats['approved'] }}
                        </h3>
                        <div class="text-secondary">Disetujui</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-title">
                            {{ $statusStats['rejected'] }}
                        </h3>
                        <div class="text-secondary">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-title">
                            {{ $statusStats['completed'] }}
                        </h3>
                        <div class="text-secondary">Selesai</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proposals Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>
                            <button type="button" class="p-0 btn btn-link" wire:click="setSortBy('title')">
                                Judul
                                @if ($sortBy === 'title')
                                    <x-lucide-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}
                                        class="icon" />
                                @endif
                            </button>
                        </th>
                        <th>Author</th>
                        <th>
                            <button type="button" class="p-0 btn btn-link" wire:click="setSortBy('status')">
                                Status
                                @if ($sortBy === 'status')
                                    <x-lucide-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}
                                        class="icon" />
                                @endif
                            </button>
                        </th>
                        <th>Bidang Fokus</th>
                        <th>
                            <button type="button" class="p-0 btn btn-link" wire:click="setSortBy('created_at')">
                                Tanggal Dibuat
                                @if ($sortBy === 'created_at')
                                    <x-lucide-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}
                                        class="icon" />
                                @endif
                            </button>
                        </th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proposals as $proposal)
                        <tr wire:key="proposal-{{ $proposal->id }}">
                            <td style="max-width: 250px;">
                                <div class="text-reset fw-bold">{{ $proposal->title }}</div>
                            </td>
                            <td>
                                <div>{{ $proposal->submitter?->name }}</div>
                                <small class="text-secondary">{{ $proposal->submitter?->email }}</small>
                            </td>
                            <td>
                                <x-tabler.badge :color="$proposal->status" class="fw-normal">
                                    {{ ucfirst($proposal->status) }}
                                </x-tabler.badge>
                            </td>
                            <td>
                                <div class="badge-outline badge">
                                    {{ $proposal->focusArea?->name ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <small class="text-secondary">
                                    {{ $proposal->created_at?->format('d M Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="flex-nowrap btn-list">
                                    <a href="{{ route('community-service.proposal.show', $proposal) }}"
                                        class="btn btn-icon btn-ghost-primary" wire:navigate title="Lihat">
                                        <x-lucide-eye class="icon" />
                                    </a>
                                    @if ($proposal->status === 'draft')
                                        <a href="{{ route('community-service.proposal.edit', $proposal) }}"
                                            class="btn btn-icon btn-ghost-info" title="Edit" wire:navigate>
                                            <x-lucide-pencil class="icon" />
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-icon btn-ghost-danger" title="Hapus"
                                        wire:click="deleteProposal({{ $proposal->id }})"
                                        wire:confirm="Yakin ingin menghapus proposal ini?">
                                        <x-lucide-trash-2 class="icon" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center">
                                <div class="mb-3">
                                    <x-lucide-inbox class="text-secondary icon icon-lg" />
                                </div>
                                <p class="text-secondary">Tidak ada data pengabdian yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($proposals->hasPages())
            <div class="d-flex align-items-center card-footer">
                {{ $proposals->links() }}
            </div>
        @endif
    </div>
</div>
