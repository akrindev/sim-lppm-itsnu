<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="align-items-center row g-2">
                <div class="col">
                    <h2 class="page-title">
                        Dashboard Eksekutif @if($stats['faculty_name']) - {{ $stats['faculty_name'] }} @endif
                    </h2>
                    <div class="mt-1 text-muted">
                        Selamat datang, {{ auth()->user()->name }} ({{ $roleName }})
                    </div>
                </div>
                <div class="col-auto">
                    <div class="dropdown">
                        <a href="#" class="btn-outline-primary btn dropdown-toggle" data-bs-toggle="dropdown">
                            <x-lucide-calendar class="me-2 icon" />
                            Tahun: {{ $selectedYear }}
                        </a>
                        <div class="dropdown-menu">
                            @foreach ($availableYears as $year)
                                <a href="#" class="dropdown-item {{ $selectedYear == $year ? 'active' : '' }}"
                                    wire:click="$set('selectedYear', {{ $year }})">
                                    {{ $year }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="subheader">Total Penelitian</div>
                            <div class="mb-3 h1">{{ $stats['total_research'] ?? 0 }}</div>
                            <div class="text-muted small">Proposal Penelitian</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="subheader">Total PKM</div>
                            <div class="mb-3 h1">{{ $stats['total_community_service'] ?? 0 }}</div>
                            <div class="text-muted small">Proposal PKM</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="subheader">Penelitian Disetujui</div>
                            <div class="mb-3 h1">{{ $stats['research_approved'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                @php
                                    $p = ($stats['total_research'] ?? 0) > 0 ? ($stats['research_approved'] / $stats['total_research']) * 100 : 0;
                                @endphp
                                <div class="bg-success progress-bar" x-data :style="'width: ' + {{ $p }} + '%'"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="subheader">PKM Disetujui</div>
                            <div class="mb-3 h1">{{ $stats['community_service_approved'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                @php
                                    $p = ($stats['total_community_service'] ?? 0) > 0 ? ($stats['community_service_approved'] / $stats['total_community_service']) * 100 : 0;
                                @endphp
                                <div class="bg-success progress-bar" x-data :style="'width: ' + {{ $p }} + '%'"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 row row-cards">
                <!-- Penelitian Terbaru -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Penelitian Terbaru (Disetujui/Selesai)</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="card-table table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Pengaju</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentResearch as $research)
                                        <tr wire:key="res-{{ $research->id }}">
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;">
                                                    {{ $research->title }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center py-1">
                                                    <span class="avatar avatar-sm me-2">{{ $research->submitter?->initials() }}</span>
                                                    <div class="flex-fill">
                                                        <div class="font-weight-medium">{{ $research->submitter?->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <x-tabler.badge :color="$research->status->color()">
                                                    {{ $research->status->label() }}
                                                </x-tabler.badge>
                                            </td>
                                            <td class="text-muted">
                                                {{ $research->created_at->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-muted text-center">Belum ada penelitian disetujui</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PKM Terbaru -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">PKM Terbaru (Disetujui/Selesai)</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="card-table table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Pengaju</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentCommunityService as $communityService)
                                        <tr wire:key="pkm-{{ $communityService->id }}">
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;">
                                                    {{ $communityService->title }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center py-1">
                                                    <span class="avatar avatar-sm me-2">{{ $communityService->submitter?->initials() }}</span>
                                                    <div class="flex-fill">
                                                        <div class="font-weight-medium">{{ $communityService->submitter?->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <x-tabler.badge :color="$communityService->status->color()">
                                                    {{ $communityService->status->label() }}
                                                </x-tabler.badge>
                                            </td>
                                            <td class="text-muted">
                                                {{ $communityService->created_at->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-muted text-center">Belum ada PKM disetujui</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
