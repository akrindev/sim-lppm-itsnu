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
            <a href="{{ route('community-service.proposal.create') }}" wire:navigate class="btn btn-primary">
                <x-lucide-plus class="icon" />
                Usulan Pengabdian Baru
            </a>
        @endif
    </div>
</x-slot:pageActions>

<div>
    <x-tabler.alert />

    <!-- Status Stats -->
    <div class="row row-cards row-deck mb-3">
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <div class="text-truncate">
                        <h3 class="card-title">
                            {{ $this->statusStats['total'] }}
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
                            {{ $this->statusStats['by_status']['draft'] ?? 0 }}
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
                            {{ $this->statusStats['by_status']['submitted'] ?? 0 }}
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
                            {{ $this->statusStats['by_status']['approved'] ?? 0 }}
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
                            {{ $this->statusStats['by_status']['rejected'] ?? 0 }}
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
                            {{ $this->statusStats['by_status']['completed'] ?? 0 }}
                        </h3>
                        <div class="text-secondary">Selesai</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
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
            <table class="card-table table-vcenter table">
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
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('community-service.proposal.show', $proposal) }}"
                                        class="btn btn-icon btn-ghost-primary" wire:navigate title="Lihat">
                                        <x-lucide-eye class="icon" />
                                    </a>
                                    {{-- @if ($proposal->status->value === 'draft' && $proposal->submitter_id === auth()->id())
                                        <a href="{{ route('community-service.proposal.edit', $proposal) }}"
                                            class="btn btn-icon btn-ghost-info" title="Edit" wire:navigate>
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
