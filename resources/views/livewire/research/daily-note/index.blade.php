<x-slot:title>Catatan Harian Penelitian</x-slot:title>
<x-slot:pageTitle>Catatan Harian Penelitian</x-slot:pageTitle>
<x-slot:pageSubtitle>
    Kelola buku harian (logbook) aktivitas penelitian Anda.
</x-slot:pageSubtitle>

<div>
    <x-tabler.alert />

    <!-- Role-based Tabs (only for regular dosen users) -->
    @if (auth()->user()->activeHasAnyRole(['dosen']))
        <div class="mb-3">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($roleFilter === 'ketua') active @endif"
                        wire:click="$set('roleFilter', 'ketua')" role="tab"
                        aria-selected="@if ($roleFilter === 'ketua') true @else false @endif">
                        <x-lucide-crown class="me-2 icon" />
                        Sebagai Ketua
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($roleFilter === 'anggota') active @endif"
                        wire:click="$set('roleFilter', 'anggota')" role="tab"
                        aria-selected="@if ($roleFilter === 'anggota') true @else false @endif">
                        <x-lucide-users class="me-2 icon" />
                        Sebagai Anggota
                    </button>
                </li>
            </ul>
        </div>
    @endif

    <!-- Search & Filter Section -->
    <div class="mb-3 row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control"
                                placeholder="Cari berdasarkan judul atau peneliti..."
                                wire:model.live.debounce.300ms="search" />
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="selectedYear">
                                <option value="">Semua Tahun</option>
                                @foreach ($this->availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                                <x-lucide-rotate-ccw class="icon me-2" />
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
                        <th>Status</th>
                        <th>Catatan Terakhir</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->proposals as $proposal)
                        <tr wire:key="proposal-{{ $proposal->id }}">
                            <td class="text-wrap">
                                <div class="text-reset fw-bold">{{ $proposal->title }}</div>
                                <small class="text-muted">{{ $proposal->researchScheme?->name }}</small>
                            </td>
                            <td>
                                <div>{{ $proposal->submitter?->name }}</div>
                            </td>
                            <td>
                                <x-tabler.badge :color="$proposal->status->color()">
                                    {{ $proposal->status->label() }}
                                </x-tabler.badge>
                            </td>
                            <td>
                                @php
                                    $latestNote = $proposal->dailyNotes()->latest('activity_date')->first();
                                @endphp
                                @if ($latestNote)
                                    <div class="small">
                                        <span class="text-secondary">{{ $latestNote->activity_date->format('d/m/Y') }}</span>
                                        <div class="text-truncate" style="max-width: 150px;">{{ $latestNote->activity_description }}</div>
                                    </div>
                                @else
                                    <span class="text-muted small">Belum ada catatan</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('research.daily-note.show', $proposal) }}"
                                    class="btn btn-primary btn-sm" wire:navigate.hover>
                                    <x-lucide-book class="icon me-2" />
                                    Buka Logbook
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center">
                                <div class="mb-3">
                                    <x-lucide-inbox class="text-secondary icon icon-lg" />
                                </div>
                                <p class="text-secondary">Tidak ada penelitian yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
