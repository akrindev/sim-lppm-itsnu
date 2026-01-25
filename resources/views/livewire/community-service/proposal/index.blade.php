<x-slot:title>Pengabdian</x-slot:title>
<x-slot:pageTitle>Daftar Pengabdian kepada Masyarakat</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola proposal pengabdian Anda dengan fitur lengkap.</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        @php
            $startDate = \App\Models\Setting::where('key', 'community_service_proposal_start_date')->value('value');
            $endDate = \App\Models\Setting::where('key', 'community_service_proposal_end_date')->value('value');
            $isWithinSchedule = false;
            if ($startDate && $endDate) {
                $now = now();
                $start = \Carbon\Carbon::parse($startDate)->startOfDay();
                $end = \Carbon\Carbon::parse($endDate)->endOfDay();
                $isWithinSchedule = $now->between($start, $end);
            }
        @endphp

        @if ($isWithinSchedule && auth()->user()->activeHasRole('dosen'))
            <a href="{{ route('community-service.proposal.create') }}" wire:navigate.hover class="btn btn-primary">
                <x-lucide-plus class="icon" />
                Usulan Pengabdian Baru
            </a>
        @endif
    </div>
</x-slot:pageActions>

<div>
    <x-tabler.alert />

    @php
        $user = auth()->user();
        $isKepala = $user->activeHasRole('kepala lppm');
    @endphp

    <div class="collapse shadow-sm border-0 alert alert-info alert-dismissible fade show" id="pkmIndexInfo" role="alert">
        <div class="d-flex">
            <div>
                <x-lucide-info class="me-2 alert-icon icon" />
            </div>
            <div>
                @if ($isKepala)
                    <h4 class="alert-title">Panduan Kepala LPPM (Daftar PKM)</h4>
                    <div class="text-secondary">
                        Halaman ini menampilkan seluruh usulan pengabdian masyarakat (PKM) yang ada dalam sistem. Anda
                        dapat memantau
                        distribusi status usulan secara makro dan melihat detail progres masing-masing PKM.
                        Untuk memberikan keputusan persetujuan, silakan gunakan menu <strong>Persetujuan
                            Awal/Akhir</strong> di Navbar.
                    </div>
                @else
                    <h4 class="alert-title">Panduan Daftar Pengabdian (PKM)</h4>
                    <div class="text-secondary">
                        Halaman ini menampilkan seluruh usulan pengabdian masyarakat (PKM) Anda. Anda dapat memantau
                        status usulan,
                        mengedit draft, atau melihat detail usulan yang sedang dalam proses review.
                        Klik tombol <strong>Usulan Pengabdian Baru</strong> untuk mulai membuat usulan jika jadwal
                        sedang dibuka.
                    </div>
                @endif
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#pkmIndexInfo"
            aria-label="Close"></button>
    </div>

    <div class="mb-3">
        <button class="btn btn-ghost-info btn-sm" type="button" data-bs-toggle="collapse"
            data-bs-target="#pkmIndexInfo" aria-expanded="false" aria-controls="pkmIndexInfo">
            <x-lucide-info class="me-1 icon" />
            Bantuan Penggunaan
        </button>
    </div>

    <!-- Status Stats -->
    <div class="mb-3 row row-cards">
        <div class="col-sm-6 col-lg-2">
            <div class="shadow-sm border-0 card card-sm">
                <div class="card-body">
                    <div class="align-items-center row">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <x-lucide-list class="icon" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $this->statusStats['total'] }}</div>
                            <div class="text-secondary small">Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="shadow-sm border-0 card card-sm">
                <div class="card-body">
                    <div class="align-items-center row">
                        <div class="col-auto">
                            <span class="bg-secondary text-white avatar">
                                <x-lucide-file-text class="icon" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $this->statusStats['by_status']['draft'] ?? 0 }}</div>
                            <div class="text-secondary small">Draft</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="shadow-sm border-0 card card-sm">
                <div class="card-body">
                    <div class="align-items-center row">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <x-lucide-send class="icon" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $this->statusStats['by_status']['submitted'] ?? 0 }}
                            </div>
                            <div class="text-secondary small">Diajukan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="shadow-sm border-0 card card-sm">
                <div class="card-body">
                    <div class="align-items-center row">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <x-lucide-check-circle class="icon" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $this->statusStats['by_status']['approved'] ?? 0 }}</div>
                            <div class="text-secondary small">Disetujui</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="shadow-sm border-0 card card-sm">
                <div class="card-body">
                    <div class="align-items-center row">
                        <div class="col-auto">
                            <span class="bg-danger text-white avatar">
                                <x-lucide-x-circle class="icon" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $this->statusStats['by_status']['rejected'] ?? 0 }}</div>
                            <div class="text-secondary small">Ditolak</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="shadow-sm border-0 card card-sm">
                <div class="card-body">
                    <div class="align-items-center row">
                        <div class="col-auto">
                            <span class="bg-azure text-white avatar">
                                <x-lucide-award class="icon" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $this->statusStats['by_status']['completed'] ?? 0 }}
                            </div>
                            <div class="text-secondary small">Selesai</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                @foreach (\App\Enums\ProposalStatus::filterOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
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
                        <th>Author</th>
                        <th>Status</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->proposals as $proposal)
                        <tr wire:key="proposal-{{ $proposal->id }}">
                            <td style="max-width: 250px;">
                                <div class="text-reset fw-bold">{{ $proposal->title }}</div>
                                <div class="mt-1">
                                    <x-tabler.badge variant="outline" class="text-uppercase"
                                        style="font-size: 0.65rem;">
                                        {{ $proposal->focusArea?->name ?? '—' }}
                                    </x-tabler.badge>
                                </div>
                            </td>
                            <td>
                                <div>{{ $proposal->submitter->name }}</div>
                                <small class="text-secondary">{{ $proposal->submitter->email }}</small>
                            </td>
                            <td>
                                <x-tabler.badge :color="$proposal->status->color()" class="fw-normal">
                                    {{ $proposal->status->label() }}
                                </x-tabler.badge>
                                <div class="mt-1">
                                    <small class="text-secondary">
                                        {{ $proposal->created_at->format('d M Y') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="flex-nowrap btn-list">
                                    <a href="{{ route('community-service.proposal.show', $proposal) }}"
                                        class="btn btn-icon btn-ghost-primary" wire:navigate.hover title="Lihat">
                                        <x-lucide-eye class="icon" />
                                    </a>
                                    {{-- @if ($proposal->status->value === 'draft' && $proposal->submitter_id === auth()->id())
                                        <a href="{{ route('community-service.proposal.edit', $proposal) }}"
                                            class="btn btn-icon btn-ghost-info" title="Edit" wire:navigate.hover>
                                            <x-lucide-pencil class="icon" />
                                        </a>
                                    @endif --}}
                                    @if (
                                        $proposal->status->value === 'draft' &&
                                            (auth()->user()->hasRole('admin lppm') || $proposal->submitter_id === auth()->id()))
                                        <button type="button" class="btn btn-icon btn-ghost-danger" title="Hapus"
                                            wire:click="confirmDeleteProposal('{{ $proposal->id }}')">
                                            <x-lucide-trash class="icon" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center">
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

        @if ($this->proposals->hasPages())
            <div class="d-flex align-items-center card-footer">
                {{ $this->proposals->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Proposal Confirmation Modal -->
    <x-tabler.modal-confirmation id="deleteProposalModal" title="Hapus Proposal?"
        message="Apakah Anda yakin ingin menghapus proposal ini? Tindakan ini tidak dapat dibatalkan."
        confirm-text="Ya, Hapus Proposal" cancel-text="Batal" variant="danger" icon="trash"
        component-id="{{ $this->getId() }}" on-confirm="deleteProposal" on-cancel="cancelDeleteProposal" />
</div>
