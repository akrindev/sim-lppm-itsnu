<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="align-items-center row g-2">
                <div class="col">
                    <h2 class="page-title">
                        Dashboard Dosen
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
                                <div class="subheader">Penelitian Saya</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['my_research'] ?? 0 }}</div>
                            <div class="text-muted">Sebagai Pengaju</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pengmas Saya</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['my_community_service'] ?? 0 }}</div>
                            <div class="text-muted">Sebagai Pengaju</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Penelitian Anggota</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['research_as_member'] ?? 0 }}</div>
                            <div class="text-muted">Tim Peneliti</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pengmas Anggota</div>
                            </div>
                            <div class="mb-3 h1">{{ $stats['community_service_as_member'] ?? 0 }}</div>
                            <div class="text-muted">Tim Pengmas</div>
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
                                                @if ($research->status->value === 'approved')
                                                    <x-tabler.badge color="success">Disetujui</x-tabler.badge>
                                                @elseif($research->status->value === 'rejected')
                                                    <x-tabler.badge color="danger">Ditolak</x-tabler.badge>
                                                @elseif($research->status->value === 'submitted')
                                                    <x-tabler.badge color="warning">Menunggu Review</x-tabler.badge>
                                                @elseif($research->status->value === 'reviewed')
                                                    <x-tabler.badge color="info">Sudah Direview</x-tabler.badge>
                                                @elseif($research->status->value === 'completed')
                                                    <x-tabler.badge color="success">Selesai</x-tabler.badge>
                                                @else
                                                    <x-tabler.badge
                                                        color="secondary">{{ $research->status->label() }}</x-tabler.badge>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                {{ $research->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-4 text-muted text-center">
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
                                                @if ($communityService->status->value === 'approved')
                                                    <x-tabler.badge color="success">Disetujui</x-tabler.badge>
                                                @elseif($communityService->status->value === 'rejected')
                                                    <x-tabler.badge color="danger">Ditolak</x-tabler.badge>
                                                @elseif($communityService->status->value === 'submitted')
                                                    <x-tabler.badge color="warning">Menunggu Review</x-tabler.badge>
                                                @elseif($communityService->status->value === 'reviewed')
                                                    <x-tabler.badge color="info">Sudah Direview</x-tabler.badge>
                                                @elseif($communityService->status->value === 'completed')
                                                    <x-tabler.badge color="success">Selesai</x-tabler.badge>
                                                @else
                                                    <x-tabler.badge
                                                        color="secondary">{{ $communityService->status->label() }}</x-tabler.badge>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                {{ $communityService->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-4 text-muted text-center">
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
</div>
</div>
</div>
