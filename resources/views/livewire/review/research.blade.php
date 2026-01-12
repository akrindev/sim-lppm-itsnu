<x-slot:title>Review Penelitian</x-slot:title>
<x-slot:pageTitle>Review Penelitian</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola tugas review proposal penelitian yang ditugaskan kepada Anda.</x-slot:pageSubtitle>

<div>
    <x-tabler.alert />

    <!-- Statistics Cards -->
    <div class="row row-deck row-cards mb-3">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Tugas Review</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $this->statusStats['all'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Menunggu Review</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $this->statusStats['pending'] }}</div>
                        <div class="me-auto">
                            <span class="text-warning d-inline-flex align-items-center lh-1">
                                <x-lucide-clock class="icon icon-sm" />
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
                        <div class="subheader">Selesai Direview</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $this->statusStats['completed'] }}</div>
                        <div class="me-auto">
                            <span class="text-success d-inline-flex align-items-center lh-1">
                                <x-lucide-check-circle class="icon icon-sm" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-3 card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="search">
                        <x-lucide-search class="me-2 icon" />
                        Cari Proposal
                    </label>
                    <input type="text" id="search" class="form-control"
                        placeholder="Cari berdasarkan judul atau nama author..." wire:model.live="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="status">
                        <x-lucide-filter class="me-2 icon" />
                        Status Review
                    </label>
                    <select id="status" class="form-select" wire:model.live="statusFilter">
                        <option value="all">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="year">
                        <x-lucide-calendar class="me-2 icon" />
                        Tahun
                    </label>
                    <select id="year" class="form-select" wire:model.live="selectedYear">
                        <option value="">Semua Tahun</option>
                        @foreach ($this->availableYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if (!empty($search) || !empty($selectedYear) || $statusFilter !== 'all')
                <div class="mt-3">
                    <button type="button" class="btn-outline-secondary btn btn-sm" wire:click="resetFilters">
                        <x-lucide-x class="me-1 icon" />
                        Reset Filter
                    </button>
                </div>
            @endif
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
                        <th>Bidang Fokus</th>
                        <th>Status Review</th>
                        <th>Rekomendasi</th>
                        <th>Tanggal Dibuat</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->proposals as $proposal)
                        @php
                            $review = $proposal->reviewers->firstWhere('user_id', auth()->id());
                        @endphp
                        <tr wire:key="proposal-{{ $proposal->id }}">
                            <td class="text-wrap">
                                <div class="text-reset fw-bold">{{ $proposal->title }}</div>
                            </td>
                            <td>
                                <div>{{ $proposal->submitter?->name }}</div>
                                <small class="text-secondary">{{ $proposal->submitter?->identity->identity_id }}</small>
                            </td>
                            <td>
                                <x-tabler.badge variant="outline">
                                    {{ $proposal->focusArea?->name ?? '—' }}
                                </x-tabler.badge>
                            </td>
                            <td>
                                @if ($review?->isCompleted())
                                    <x-tabler.badge color="green" class="fw-normal">
                                        Selesai
                                    </x-tabler.badge>
                                @else
                                    <x-tabler.badge color="yellow" class="fw-normal">
                                        Pending
                                    </x-tabler.badge>
                                @endif
                            </td>
                            <td>
                                @if ($review?->isCompleted())
                                    @if ($review->recommendation === 'approved')
                                        <x-tabler.badge variant="outline" color="success">✓ Disetujui</x-tabler.badge>
                                    @elseif ($review->recommendation === 'rejected')
                                        <x-tabler.badge variant="outline" color="danger">✗ Ditolak</x-tabler.badge>
                                    @else
                                        <x-tabler.badge variant="outline" color="warning">↺ Revisi</x-tabler.badge>
                                    @endif
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-secondary">
                                    {{ $proposal->created_at?->format('d M Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-list">
                                    <a href="{{ route('research.proposal.show', $proposal) }}"
                                        class="btn-outline-primary btn btn-sm" title="Lihat Detail" wire:navigate>
                                        <x-lucide-eye class="icon" />
                                        Lihat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center">
                                <div class="mb-3">
                                    <x-lucide-inbox class="text-secondary icon icon-lg" />
                                </div>
                                <p class="text-secondary">Tidak ada proposal yang perlu direview saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
