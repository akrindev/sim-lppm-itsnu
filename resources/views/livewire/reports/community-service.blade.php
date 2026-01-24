<div>
    <x-slot:pageHeader>
        <div class="mb-2 text-secondary">
            {{ __('Akses khusus Admin LPPM dan Rektor untuk memantau perkembangan laporan Pengabdian Masyarakat (PKM).') }}
        </div>
        <div class="text-muted small">
            {{ __('Pilih periode untuk melihat statistik dan daftar PKM.') }}
        </div>
    </x-slot:pageHeader>

    <x-slot:pageActions>
        <div class="btn-list">
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-calendar me-2"></i>
                    {{ __('Tahun') }}: {{ $period }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @foreach ($periods as $availablePeriod)
                        <li>
                            <button type="button" class="dropdown-item {{ $period === $availablePeriod ? 'active' : '' }}"
                                wire:click="setPeriod('{{ $availablePeriod }}')">
                                {{ $availablePeriod }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </x-slot:pageActions>

    <!-- KPI Cards -->
    <div class="mb-3 row row-deck">
        @foreach ($summary as $card)
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="d-flex align-items-center gap-3 card-body">
                        <span class="avatar avatar-rounded {{ $card['variant'] }}">
                            <i class="ti ti-{{ $card['icon'] }}"></i>
                        </span>
                        <div>
                            <h3 class="mb-1 lh-1">{{ $card['value'] }}</h3>
                            <div class="text-secondary small">{{ $card['label'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Analytics Section 1: Schemes, Focus Areas, Faculties -->
    <div class="row row-cards mb-3">
        <!-- Distribution by Scheme -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Distribusi Skema PKM') }}</h3>
                </div>
                <div class="p-0 card-body overflow-auto" style="max-height: 300px;">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>{{ __('Skema') }}</th>
                                <th class="text-center">{{ __('Jumlah') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($schemes as $scheme)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $scheme['name'] }}</div>
                                        <div class="text-muted small">Rp {{ number_format($scheme['budget'], 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-blue-lt">{{ $scheme['count'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-muted">
                                        {{ __('Tidak ada data skema') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Distribution by Focus Area -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Bidang Fokus PKM') }}</h3>
                </div>
                <div class="p-0 card-body overflow-auto" style="max-height: 300px;">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>{{ __('Bidang Fokus') }}</th>
                                <th class="text-center">{{ __('Prosentase') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalPkm = $schemes->sum('count'); @endphp
                            @forelse ($focusAreas as $area)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $area['name'] }}</div>
                                        <div class="text-muted small">{{ $area['count'] }} {{ __('PKM') }}</div>
                                    </td>
                                    <td class="text-center">
                                        @php $percent = $totalPkm > 0 ? round(($area['count'] / $totalPkm) * 100) : 0; @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress progress-xs flex-fill" style="min-width: 50px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <span class="small text-muted">{{ $percent }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-muted">
                                        {{ __('Tidak ada data bidang fokus') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Productivity by Faculty -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Produktivitas Fakultas (PKM)') }}</h3>
                </div>
                <div class="p-0 card-body overflow-auto" style="max-height: 300px;">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>{{ __('Fakultas') }}</th>
                                <th class="text-center">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($faculties as $faculty)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 180px;">{{ $faculty['name'] }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-green-lt">{{ $faculty['count'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-muted">
                                        {{ __('Tidak ada data fakultas') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Section 2: Output Analytics -->
    <div class="row row-cards mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Analitik Luaran PKM') }} — {{ $period }}</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse ($outputStats as $stat)
                            <div class="col-sm-6 col-md-4 col-lg-2">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    {{ $stat['category'] }}
                                                </div>
                                                <div class="text-secondary small">
                                                    {{ $stat['count'] }} {{ __('Total') }}
                                                </div>
                                                <div class="mt-1">
                                                    <span class="text-success fw-bold">{{ $stat['published'] }}</span>
                                                    <span class="text-muted small"> {{ __('Terbit') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">
                                {{ __('Belum ada luaran yang dilaporkan pada periode ini.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent PKM Section -->
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Daftar PKM Terbaru') }} — {{ $period }}</h3>
                </div>
                <div class="p-0 card-body">
                    <div class="table-responsive">
                        <table class="card-table table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>{{ __('Judul & Pengaju') }}</th>
                                    <th>{{ __('Skema & Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentPkm as $pkm)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm avatar-rounded me-2" style="background-image: url({{ $pkm->submitter->profile_picture }})"></span>
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium text-truncate" style="max-width: 500px;" title="{{ $pkm->title }}">
                                                        <a href="{{ route('community-service.proposal.show', $pkm) }}" class="text-reset" wire:navigate.hover>
                                                            {{ $pkm->title }}
                                                        </a>
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ $pkm->submitter->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted small mb-1">
                                                {{ $pkm->researchScheme->name ?? '-' }}
                                            </div>
                                            <x-tabler.badge :color="$pkm->status->color()">
                                                {{ $pkm->status->label() }}
                                            </x-tabler.badge>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-5 text-center text-muted">
                                            {{ __('Belum ada data PKM untuk periode ini.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($recentPkm->count() > 0)
                    <div class="card-footer d-flex align-items-center">
                        <p class="m-0 text-muted small">
                            {{ __('Menampilkan :count data terbaru', ['count' => $recentPkm->count()]) }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
