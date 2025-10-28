<x-slot:title>Penelitian</x-slot:title>
<x-slot:pageTitle>Daftar Penelitian</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola proposal penelitian Anda dengan fitur lengkap.</x-slot:pageSubtitle>
<x-slot:pageActions>
    @unless (auth()->user()->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']))
        <div class="btn-list">
            <a href="{{ route('research.proposal.create') }}" wire:navigate class="btn btn-primary">
                <x-lucide-plus class="icon" />
                Usulan Penelitian Baru
            </a>
        </div>
    @endunless
</x-slot:pageActions>

<div>
    <div class="mb-3 row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-4">
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
                                <option value="reviewed">Ditinjau</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="yearFilter">
                                <option value="">Semua Tahun</option>
                                @foreach ($this->availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
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
    <!-- Proposals Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Peneliti</th>
                        <th>Status</th>
                        <th>Skema</th>
                        <th>Bidang Fokus</th>
                        <th>Tanggal Dibuat</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->proposals as $proposal)
                        <tr wire:key="proposal-{{ $proposal->id }}">
                            <td class="text-wrap">
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
                                <div class="bg-blue-lt badge-outline badge">
                                    {{ $proposal->researchScheme?->name ?? '—' }}
                                </div>
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
                                    <a href="{{ route('research.proposal.show', $proposal) }}"
                                        class="btn btn-icon btn-ghost-primary" title="Lihat">
                                        <x-lucide-eye class="icon" />
                                    </a>
                                    @if ($proposal->status === 'draft')
                                        <a href="#" class="btn btn-icon btn-ghost-info" title="Edit">
                                            <x-lucide-pencil class="icon" />
                                        </a>
                                    @endif
                                    @if (auth()->user()->hasRole('admin lppm'))
                                        <button type="button" class="btn btn-icon btn-ghost-danger" title="Hapus"
                                            wire:click="deleteProposal({{ $proposal->id }})"
                                            wire:confirm="Yakin ingin menghapus proposal ini?">
                                            <x-lucide-trash-2 class="icon" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center">
                                <div class="mb-3">
                                    <x-lucide-inbox class="text-secondary icon icon-lg" />
                                </div>
                                <p class="text-secondary">Tidak ada data penelitian yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->proposals->hasPages())
            <div class="d-flex align-items-center card-footer">
                {{ $this->proposals->links() }}
            </div>
        @endif
    </div>
</div>
