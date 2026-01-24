<x-slot:title>Penelitian</x-slot:title>
<x-slot:pageTitle>Daftar Penelitian</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola proposal penelitian Anda dengan fitur lengkap.</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        @php
            $startDate = \App\Models\Setting::where('key', 'research_proposal_start_date')->value('value');
            $endDate = \App\Models\Setting::where('key', 'research_proposal_end_date')->value('value');
            $isWithinSchedule = false;
            if ($startDate && $endDate) {
                $now = now();
                $start = \Carbon\Carbon::parse($startDate)->startOfDay();
                $end = \Carbon\Carbon::parse($endDate)->endOfDay();
                $isWithinSchedule = $now->between($start, $end);
            }
        @endphp

        @if ($isWithinSchedule && auth()->user()->activeHasRole('dosen'))
            <a href="{{ route('research.proposal.create') }}" wire:navigate.hover class="btn btn-primary">
                <x-lucide-plus class="icon" />
                Usulan Penelitian Baru
            </a>
        @endif
    </div>
</x-slot:pageActions>


<div>
    <x-tabler.alert />

    <!-- Role-based Tabs (only for regular dosen users) -->
    @unless (auth()->user()->activeHasAnyRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']))
        <div class="mb-3">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($roleFilter === 'ketua') active @endif"
                        wire:click="$set('roleFilter', 'ketua')" role="tab"
                        aria-selected="@if ($roleFilter === 'ketua') true @else false @endif">
                        <x-lucide-crown class="icon me-2" />
                        Sebagai Ketua
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($roleFilter === 'anggota') active @endif"
                        wire:click="$set('roleFilter', 'anggota')" role="tab"
                        aria-selected="@if ($roleFilter === 'anggota') true @else false @endif">
                        <x-lucide-users class="icon me-2" />
                        Sebagai Anggota
                    </button>
                </li>
            </ul>
        </div>
    @endunless

    <div class="row mb-3 gap-3">
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
                                @foreach (\App\Enums\ProposalStatus::filterOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
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

        <div class="col-12">
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
                                    <td class="text-wrap">
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
                                        <small
                                            class="text-secondary">{{ $proposal->submitter->identity->identity_id }}</small>
                                    </td>
                                    <td>
                                        <x-tabler.badge :color="$proposal->status->color()" class="fw-normal">
                                            {{ $proposal->status->label() }}
                                        </x-tabler.badge>
                                        <div class="mt-1">
                                            <small class="text-secondary">
                                                {{ $proposal->created_at?->format('d M Y') }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('research.proposal.show', $proposal) }}"
                                                class="btn btn-icon btn-ghost-primary" title="Lihat" wire:navigate.hover>
                                                <x-lucide-eye class="icon" />
                                            </a>
                                            {{-- @if ($proposal->status->value === 'draft')
                                                <a href="#" class="btn btn-icon btn-ghost-info" title="Edit">
                                                    <x-lucide-pencil class="icon" />
                                                </a>
                                            @endif --}}
                                            @if (
                                                $proposal->status->value === 'draft' &&
                                                    (auth()->user()->hasRole('admin lppm') || $proposal->submitter_id === auth()->id()))
                                                <button type="button" class="btn btn-icon btn-ghost-danger"
                                                    title="Hapus"
                                                    wire:click="confirmDeleteProposal('{{ $proposal->id }}')">
                                                    <x-lucide-trash-2 class="icon" />
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

        <!-- Delete Proposal Confirmation Modal -->
        <x-tabler.modal-confirmation id="deleteProposalModal" title="Hapus Proposal?"
            message="Apakah Anda yakin ingin menghapus proposal ini? Tindakan ini tidak dapat dibatalkan."
            confirm-text="Ya, Hapus Proposal" cancel-text="Batal" variant="danger" icon="trash"
            component-id="{{ $this->getId() }}" on-confirm="deleteProposal" on-cancel="cancelDeleteProposal" />
    </div>
</div>
