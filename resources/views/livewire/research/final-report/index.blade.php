<x-slot:title>Laporan Akhir Penelitian</x-slot:title>
<x-slot:pageTitle>Laporan Akhir Penelitian</x-slot:pageTitle>
<x-slot:pageSubtitle>
    Buat dan kelola laporan akhir untuk penelitian yang telah selesai.
</x-slot:pageSubtitle>

<div>
    <x-tabler.alert />

    <!-- Search & Filter Section -->
    <div class="mb-3 row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Cari berdasarkan judul atau peneliti..."
                                wire:model.live.debounce.300ms="search" />
                        </div>

                        <!-- Year Filter -->
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="selectedYear">
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
                        <th>Judul Penelitian</th>
                        <th>Peneliti</th>
                        <th>Skema</th>
                        <th>Status Laporan Akhir</th>
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
                                <small class="text-secondary">{{ $proposal->submitter?->identity->identity_id }}</small>
                            </td>
                            <td>
                                <x-tabler.badge variant="outline">
                                    {{ $proposal->researchScheme?->name ?? '—' }}
                                </x-tabler.badge>
                            </td>
                            <td>
                                @php
                                    $finalReport = $proposal->progressReports->first();
                                @endphp
                                @if ($finalReport)
                                    <small class="text-secondary">
                                        Tahun {{ $finalReport->reporting_year }}
                                        <br>
                                        <x-tabler.badge :color="$finalReport->status === 'approved' ? 'success' : ($finalReport->status === 'submitted' ? 'info' : 'secondary')"
                                            class="mt-1">
                                            {{ ucfirst($finalReport->status) }}
                                        </x-tabler.badge>
                                    </small>
                                @else
                                    <small class="text-muted">Belum ada laporan</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('research.final-report.show', $proposal) }}"
                                    class="btn btn-primary btn-sm" wire:navigate>
                                    <x-lucide-file-edit class="icon" />
                                    {{ $proposal->progressReports->isEmpty() ? 'Buat Laporan Akhir' : 'Lihat Laporan Akhir' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center">
                                <div class="mb-3">
                                    <x-lucide-inbox class="text-secondary icon icon-lg" />
                                </div>
                                <p class="text-secondary">
                                    Tidak ada penelitian yang perlu dilaporkan.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
