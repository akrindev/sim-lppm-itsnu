<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="align-items-center row g-2">
                <div class="col">
                    <h2 class="page-title">
                        Dashboard Reviewer
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
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Penelitian Ditugaskan</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['research_assigned'] ?? 0 }}</div>
                            <div class="text-muted">Untuk Direview</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Penelitian Selesai</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['research_completed'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-success progress-bar" role="progressbar"
                                    style="width: {{ max(0, min(100, ($stats['research_assigned'] ?? 0) > 0 ? ($stats['research_completed'] / $stats['research_assigned']) * 100 : 0)) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pengmas Ditugaskan</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['community_service_assigned'] ?? 0 }}</div>
                            <div class="text-muted">Untuk Direview</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pengmas Selesai</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['community_service_completed'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-success progress-bar" role="progressbar"
                                    style="width: {{ max(0, min(100, ($stats['community_service_assigned'] ?? 0) > 0 ? ($stats['community_service_completed'] / $stats['community_service_assigned']) * 100 : 0)) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Penelitian Belum</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['research_pending'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-warning progress-bar" role="progressbar"
                                    style="width: {{ max(0, min(100, ($stats['research_assigned'] ?? 0) > 0 ? ($stats['research_pending'] / $stats['research_assigned']) * 100 : 0)) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pengmas Belum</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['community_service_pending'] ?? 0 }}</div>
                            <div class="progress progress-sm">
                                <div class="bg-warning progress-bar" role="progressbar"
                                    style="width: {{ max(0, min(100, ($stats['community_service_assigned'] ?? 0) > 0 ? ($stats['community_service_pending'] / $stats['community_service_assigned']) * 100 : 0)) }}%">
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
                                        <th>Status Review</th>
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
                                                @php
                                                    $reviewStatus =
                                                        $researchReviewerStats
                                                            ->where('proposal_id', $research->id)
                                                            ->first()?->status ?? 'pending';
                                                @endphp
                                                @if ($reviewStatus === 'completed')
                                                    <span class="bg-success badge">Selesai Review</span>
                                                @elseif($reviewStatus === 'pending')
                                                    <span class="bg-warning badge">Belum Review</span>
                                                @else
                                                    <span class="bg-info badge">{{ ucfirst($reviewStatus) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                {{ $research->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-muted text-center">
                                                Belum ada penelitian untuk direview
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
                                        <th>Status Review</th>
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
                                                @php
                                                    $reviewStatus =
                                                        $communityServiceReviewerStats
                                                            ->where('proposal_id', $communityService->id)
                                                            ->first()?->status ?? 'pending';
                                                @endphp
                                                @if ($reviewStatus === 'completed')
                                                    <span class="bg-success badge">Selesai Review</span>
                                                @elseif($reviewStatus === 'pending')
                                                    <span class="bg-warning badge">Belum Review</span>
                                                @else
                                                    <span class="bg-info badge">{{ ucfirst($reviewStatus) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                {{ $communityService->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-muted text-center">
                                                Belum ada pengmas untuk direview
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
