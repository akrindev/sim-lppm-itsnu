<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="align-items-center row g-2">
                <div class="col">
                    <h2 class="page-title">
                        Dashboard Admin
                    </h2>
                    <div class="mt-1 text-muted">
                        Selamat datang, {{ auth()->user()->name }} ({{ $roleName }})
                    </div>
                </div>
                <div class="col-auto">
                    <div class="dropdown">
                        <a href="#" class="btn-outline-primary btn dropdown-toggle" data-bs-toggle="dropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2 icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
                                <path d="M16 6v6l4 2" />
                            </svg>
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
                <!-- Baris 1: Statistik Utama -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Penelitian</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['total_research'] ?? 0 }}</div>
                            <div class="text-muted">Proposal Penelitian</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Pengmas</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['total_community_service'] ?? 0 }}</div>
                            <div class="text-muted">Proposal Pengmas</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Penelitian Pending</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['research_pending'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-warning progress-bar" role="progressbar"
                                    style="width: {{ ($stats['research_pending'] / ($stats['total_research'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pengmas Pending</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['community_service_pending'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-warning progress-bar" role="progressbar"
                                    style="width: {{ ($stats['community_service_pending'] / ($stats['total_community_service'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris 2: Statistik Status -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Penelitian Disetujui</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['research_approved'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-success progress-bar" role="progressbar"
                                    style="width: {{ ($stats['research_approved'] / ($stats['total_research'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pengmas Disetujui</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['community_service_approved'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-success progress-bar" role="progressbar"
                                    style="width: {{ ($stats['community_service_approved'] / ($stats['total_community_service'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Penelitian Ditolak</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['research_rejected'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-danger progress-bar" role="progressbar"
                                    style="width: {{ ($stats['research_rejected'] / ($stats['total_research'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pengmas Ditolak</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['community_service_rejected'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-danger progress-bar" role="progressbar"
                                    style="width: {{ ($stats['community_service_rejected'] / ($stats['total_community_service'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris 3: Statistik Pengguna -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Dosen</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['total_dosen'] ?? 0 }}</div>
                            <div class="text-muted">Pengguna Aktif</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Tingkat Persetujuan</div>
                            </div>
                            <div class="mb-3 h1">
                                @php
                                    $approvalRate =
                                        $stats['total_research'] + $stats['total_community_service'] > 0
                                            ? round(
                                                (($stats['research_approved'] + $stats['community_service_approved']) /
                                                    ($stats['total_research'] + $stats['total_community_service'])) *
                                                    100,
                                                1,
                                            )
                                            : 0;
                                @endphp
                                {{ $approvalRate }}%
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar" role="progressbar" style="width: {{ $approvalRate }}%">
                                    <span class="sr-only">{{ $approvalRate }}%</span>
                                </div>
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
                            <h3 class="card-title">Penelitian Terbaru</h3>
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
                                        <tr>
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;">
                                                    {{ $research->title }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center py-1">
                                                    <div class="avatar-rounded avatar"
                                                        style="background-image: url({{ $research->submitter->profile_picture }})">
                                                    </div>
                                                    <div class="flex-fill ms-2">
                                                        <div class="font-weight-medium">
                                                            {{ $research->submitter->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($research->status === 'approved')
                                                    <span class="bg-success badge">Disetujui</span>
                                                @elseif($research->status === 'rejected')
                                                    <span class="bg-danger badge">Ditolak</span>
                                                @elseif($research->status === 'submitted')
                                                    <span class="bg-warning badge">Menunggu Review</span>
                                                @elseif($research->status === 'reviewed')
                                                    <span class="bg-info badge">Sudah Direview</span>
                                                @elseif($research->status === 'completed')
                                                    <span class="bg-success badge">Selesai</span>
                                                @else
                                                    <span
                                                        class="bg-secondary badge">{{ ucfirst($research->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                {{ $research->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-muted text-center">
                                                Belum ada penelitian
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pengmas Terbaru -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pengmas Terbaru</h3>
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
                                        <tr>
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;">
                                                    {{ $communityService->title }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center py-1">
                                                    <div class="avatar-rounded avatar"
                                                        style="background-image: url({{ $communityService->submitter->profile_picture }})">
                                                    </div>
                                                    <div class="flex-fill ms-2">
                                                        <div class="font-weight-medium">
                                                            {{ $communityService->submitter->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($communityService->status === 'approved')
                                                    <span class="bg-success badge">Disetujui</span>
                                                @elseif($communityService->status === 'rejected')
                                                    <span class="bg-danger badge">Ditolak</span>
                                                @elseif($communityService->status === 'submitted')
                                                    <span class="bg-warning badge">Menunggu Review</span>
                                                @elseif($communityService->status === 'reviewed')
                                                    <span class="bg-info badge">Sudah Direview</span>
                                                @elseif($communityService->status === 'completed')
                                                    <span class="bg-success badge">Selesai</span>
                                                @else
                                                    <span
                                                        class="bg-secondary badge">{{ ucfirst($communityService->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                {{ $communityService->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-muted text-center">
                                                Belum ada pengmas
                                            </td>
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
