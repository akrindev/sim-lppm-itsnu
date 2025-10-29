<x-slot:title>Review Pengabdian Masyarakat</x-slot:title>
<x-slot:pageTitle>Review Pengabdian Masyarakat</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola tugas review proposal pengabdian masyarakat yang ditugaskan kepada Anda.</x-slot:pageSubtitle>

<div>
    <x-tabler.alert />

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="search">
                        <x-lucide-search class="icon me-2" />
                        Cari Proposal
                    </label>
                    <input type="text" id="search" class="form-control" placeholder="Cari berdasarkan judul atau nama author..."
                        wire:model.live="search">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="year">
                        <x-lucide-calendar class="icon me-2" />
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
            @if (!empty($search) || !empty($selectedYear))
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                        <x-lucide-x class="icon me-1" />
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
                                <div class="badge-outline badge">
                                    {{ $proposal->focusArea?->name ?? '—' }}
                                </div>
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
                                    <span class="badge-outline badge">
                                        @if ($review->recommendation === 'approved')
                                            <span class="text-success">✓ Disetujui</span>
                                        @elseif ($review->recommendation === 'rejected')
                                            <span class="text-danger">✗ Ditolak</span>
                                        @else
                                            <span class="text-warning">↺ Revisi</span>
                                        @endif
                                    </span>
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
                                    <a href="{{ route('community-service.proposal.show', $proposal) }}"
                                        class="btn btn-sm btn-outline-primary" title="Lihat Detail" wire:navigate>
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
