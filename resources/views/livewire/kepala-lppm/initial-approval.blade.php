<x-slot:title>Persetujuan Awal</x-slot:title>
<x-slot:pageTitle>Persetujuan Awal Kepala LPPM</x-slot:pageTitle>
<x-slot:pageSubtitle>Berikan persetujuan awal untuk proposal yang telah disetujui Dekan agar dapat ditugaskan ke
    reviewer.</x-slot:pageSubtitle>

<div>
    <x-tabler.alert />

    <!-- Statistics Cards -->
    <div class="row row-deck row-cards mb-3">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Proposal</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $this->statusStats['all'] }}</div>
                        <div class="me-auto">
                            <span class="d-inline-flex align-items-center text-secondary lh-1">
                                Menunggu persetujuan awal
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Proposal Penelitian</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $this->statusStats['research'] }}</div>
                        <div class="me-auto">
                            <span class="d-inline-flex align-items-center text-blue lh-1">
                                <x-lucide-microscope class="icon icon-sm" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Proposal Pengabdian</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $this->statusStats['community_service'] }}</div>
                        <div class="me-auto">
                            <span class="d-inline-flex align-items-center text-green lh-1">
                                <x-lucide-hand-heart class="icon icon-sm" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-3">
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

                        <!-- Type Filter -->
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="typeFilter">
                                <option value="all">Semua Jenis</option>
                                <option value="research">Penelitian</option>
                                <option value="community_service">Pengabdian</option>
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
            <table class="card-table table-vcenter table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Pengusul</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->proposals as $proposal)
                        <tr wire:key="proposal-{{ $proposal->id }}">
                            <td class="text-wrap">
                                <div class="text-reset fw-bold">{{ $proposal->title }}</div>
                                <div class="mt-1">
                                    <x-tabler.badge variant="outline" class="text-uppercase" style="font-size: 0.65rem;">
                                        {{ $proposal->focusArea?->name ?? '—' }}
                                    </x-tabler.badge>
                                </div>
                            </td>
                            <td>
                                @if ($proposal->detailable_type === 'App\Models\Research')
                                    <x-tabler.badge color="blue" variant="light">
                                        <x-lucide-microscope class="icon icon-sm me-1" />
                                        Penelitian
                                    </x-tabler.badge>
                                @else
                                    <x-tabler.badge color="green" variant="light">
                                        <x-lucide-hand-heart class="icon icon-sm me-1" />
                                        Pengabdian
                                    </x-tabler.badge>
                                @endif
                            </td>
                            <td>
                                <div>{{ $proposal->submitter?->name }}</div>
                                <small class="text-secondary">
                                    {{ $proposal->submitter?->identity->identity_id }} &middot; {{ $proposal->updated_at?->format('d M Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    @if ($proposal->detailable_type === 'App\Models\Research')
                                        <a href="{{ route('research.proposal.show', $proposal) }}"
                                            class="btn btn-sm btn-primary" wire:navigate.hover>
                                            <x-lucide-eye class="icon" />
                                            Lihat & Proses
                                        </a>
                                    @else
                                        <a href="{{ route('community-service.proposal.show', $proposal) }}"
                                            class="btn btn-sm btn-primary" wire:navigate.hover>
                                            <x-lucide-eye class="icon" />
                                            Lihat & Proses
                                        </a>
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
                                <p class="text-secondary">Tidak ada proposal yang menunggu persetujuan awal.</p>
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
