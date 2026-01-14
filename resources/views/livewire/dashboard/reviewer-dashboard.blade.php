<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title text-primary">
                        <x-lucide-layout-dashboard class="icon me-2" />
                        Dashboard Reviewer
                    </h2>
                    <div class="text-muted mt-1">
                        Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong>. Berikut ringkasan tugas review Anda.
                    </div>
                </div>
                <div class="col-auto">
                    <div class="dropdown">
                        <button class="btn btn-white dropdown-toggle shadow-sm" data-bs-toggle="dropdown">
                            <x-lucide-calendar class="icon me-2 text-muted" />
                            Tahun: {{ $selectedYear }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow">
                            @foreach ($availableYears as $year)
                                <button class="dropdown-item {{ $selectedYear == $year ? 'active' : '' }}"
                                    wire:click="$set('selectedYear', {{ $year }})">
                                    {{ $year }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Urgent Action Section -->
            @if($overdueReviews->isNotEmpty() || $dueSoonReviews->isNotEmpty() || $reReviewNeeded->isNotEmpty())
                <div class="row row-cards mb-4">
                    <!-- Overdue Alerts -->
                    @foreach($overdueReviews as $review)
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-sm border-danger border-start-3 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger-lt avatar avatar-sm me-3">
                                            <x-lucide-alert-circle class="icon text-danger" />
                                        </div>
                                        <div class="flex-fill">
                                            <div class="fw-bold text-danger small">TERLAMBAT ({{ $review->days_overdue }} Hari)</div>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $review->proposal->title }}">
                                                {{ $review->proposal->title }}
                                            </div>
                                        </div>
                                        <a href="{{ route($review->proposal->detailable_type === 'App\Models\Research' ? 'research.proposal.show' : 'community-service.proposal.show', $review->proposal) }}" 
                                            class="btn btn-sm btn-danger shadow-sm" wire:navigate>
                                            Review
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Re-Review Needed -->
                    @foreach($reReviewNeeded as $review)
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-sm border-warning border-start-3 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning-lt avatar avatar-sm me-3">
                                            <x-lucide-refresh-cw class="icon text-warning" />
                                        </div>
                                        <div class="flex-fill">
                                            <div class="fw-bold text-warning small">REVIEW ULANG (PUTARAN {{ $review->round }})</div>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $review->proposal->title }}">
                                                {{ $review->proposal->title }}
                                            </div>
                                        </div>
                                        <a href="{{ route($review->proposal->detailable_type === 'App\Models\Research' ? 'research.proposal.show' : 'community-service.proposal.show', $review->proposal) }}" 
                                            class="btn btn-sm btn-warning shadow-sm" wire:navigate>
                                            Mulai
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Due Soon -->
                    @foreach($dueSoonReviews as $review)
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-sm border-info border-start-3 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info-lt avatar avatar-sm me-3">
                                            <x-lucide-clock class="icon text-info" />
                                        </div>
                                        <div class="flex-fill">
                                            <div class="fw-bold text-info small">BATAS WAKTU DEKAT ({{ $review->days_remaining }} Hari)</div>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $review->proposal->title }}">
                                                {{ $review->proposal->title }}
                                            </div>
                                        </div>
                                        <a href="{{ route($review->proposal->detailable_type === 'App\Models\Research' ? 'research.proposal.show' : 'community-service.proposal.show', $review->proposal) }}" 
                                            class="btn btn-sm btn-info shadow-sm" wire:navigate>
                                            Proses
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Main Stats -->
            <div class="row row-cards">
                <!-- Research Stats -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-blue-lt">
                            <h3 class="card-title text-blue">
                                <x-lucide-microscope class="icon me-2" />
                                Statistik Penelitian
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-secondary small mb-1">Ditugaskan</div>
                                    <div class="h2 mb-0">{{ $stats['research_assigned'] }}</div>
                                </div>
                                <div class="col-6 border-start ps-3">
                                    <div class="text-secondary small mb-1">Selesai</div>
                                    <div class="h2 mb-0 text-success">{{ $stats['research_completed'] }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-secondary small mb-1">Menunggu</div>
                                    <div class="h2 mb-0 text-warning">{{ $stats['research_pending'] }}</div>
                                </div>
                                <div class="col-6 border-start ps-3">
                                    <div class="text-secondary small mb-1">Review Ulang</div>
                                    <div class="h2 mb-0 text-purple">{{ $stats['research_re_review'] }}</div>
                                </div>
                            </div>
                            <div class="progress progress-sm mt-3">
                                @php 
                                    $researchPercent = ($stats['research_assigned'] > 0) ? ($stats['research_completed'] / $stats['research_assigned']) * 100 : 0;
                                @endphp
                                <div class="bg-success progress-bar" @style(['width' => $researchPercent . '%'])></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PKM Stats -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-green-lt">
                            <h3 class="card-title text-green">
                                <x-lucide-users class="icon me-2" />
                                Statistik Pengabdian (PKM)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-secondary small mb-1">Ditugaskan</div>
                                    <div class="h2 mb-0">{{ $stats['community_service_assigned'] }}</div>
                                </div>
                                <div class="col-6 border-start ps-3">
                                    <div class="text-secondary small mb-1">Selesai</div>
                                    <div class="h2 mb-0 text-success">{{ $stats['community_service_completed'] }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-secondary small mb-1">Menunggu</div>
                                    <div class="h2 mb-0 text-warning">{{ $stats['community_service_pending'] }}</div>
                                </div>
                                <div class="col-6 border-start ps-3">
                                    <div class="text-secondary small mb-1">Review Ulang</div>
                                    <div class="h2 mb-0 text-purple">{{ $stats['community_service_re_review'] }}</div>
                                </div>
                            </div>
                            <div class="progress progress-sm mt-3">
                                @php 
                                    $pkmPercent = ($stats['community_service_assigned'] > 0) ? ($stats['community_service_completed'] / $stats['community_service_assigned']) * 100 : 0;
                                @endphp
                                <div class="bg-success progress-bar" @style(['width' => $pkmPercent . '%'])></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Lists -->
            <div class="row row-cards mt-3">
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header">
                            <h3 class="card-title">Tugas Penelitian Terbaru</h3>
                            <div class="card-actions">
                                <a href="{{ route('review.research') }}" class="btn btn-link btn-sm" wire:navigate>Lihat Semua</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="card-table table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul & Pengaju</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentResearch as $research)
                                        @php $review = $researchReviewerStats->where('proposal_id', $research->id)->first(); @endphp
                                        <tr>
                                            <td>
                                                <div class="font-weight-medium text-dark text-wrap mb-1" style="max-width: 300px;">{{ $research->title }}</div>
                                                <div class="text-muted small d-flex align-items-center">
                                                    <x-lucide-user class="icon icon-inline me-1" />
                                                    {{ $research->submitter->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <x-tabler.badge :color="$review?->status->color() ?? 'secondary'">
                                                    {{ $review?->status->label() ?? 'Pending' }}
                                                </x-tabler.badge>
                                                @if($review && $review->round > 1)
                                                    <div class="small text-muted mt-1">Putaran {{ $review->round }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('research.proposal.show', $research) }}" class="btn btn-white btn-icon shadow-sm" title="Buka Proposal" wire:navigate>
                                                    <x-lucide-external-link class="icon text-primary" />
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="py-5 text-center text-muted">Belum ada tugas penelitian</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header">
                            <h3 class="card-title">Tugas PKM Terbaru</h3>
                            <div class="card-actions">
                                <a href="{{ route('review.community-service') }}" class="btn btn-link btn-sm" wire:navigate>Lihat Semua</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="card-table table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul & Pengaju</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentCommunityService as $pkm)
                                        @php $review = $communityServiceReviewerStats->where('proposal_id', $pkm->id)->first(); @endphp
                                        <tr>
                                            <td>
                                                <div class="font-weight-medium text-dark text-wrap mb-1" style="max-width: 300px;">{{ $pkm->title }}</div>
                                                <div class="text-muted small d-flex align-items-center">
                                                    <x-lucide-user class="icon icon-inline me-1" />
                                                    {{ $pkm->submitter->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <x-tabler.badge :color="$review?->status->color() ?? 'secondary'">
                                                    {{ $review?->status->label() ?? 'Pending' }}
                                                </x-tabler.badge>
                                                @if($review && $review->round > 1)
                                                    <div class="small text-muted mt-1">Putaran {{ $review->round }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('community-service.proposal.show', $pkm) }}" class="btn btn-white btn-icon shadow-sm" title="Buka Proposal" wire:navigate>
                                                    <x-lucide-external-link class="icon text-primary" />
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="py-5 text-center text-muted">Belum ada tugas PKM</td></tr>
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
