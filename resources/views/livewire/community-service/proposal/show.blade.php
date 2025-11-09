<x-slot:title>{{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>{{ $proposal->title }}</x-slot:pageTitle>
<x-slot:pageSubtitle>Detail Proposal Pengabdian Masyarakat</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        @if (auth()->user()->hasRole('reviewer'))
            <a href="{{ route('review.community-service') }}" class="btn-outline-secondary btn" wire:navigate>
                <x-lucide-arrow-left class="icon" />
                Kembali
            </a>
        @else
            <a href="{{ route('community-service.proposal.index') }}" class="btn-outline-secondary btn" wire:navigate>
                <x-lucide-arrow-left class="icon" />
                Kembali
            </a>
        @endif
        @if ($proposal->status->value === 'draft' && $proposal->submitter_id === auth()->id())
            <a href="{{ route('community-service.proposal.edit', $proposal) }}" wire:navigate class="btn btn-primary">
                <x-lucide-pencil class="icon" />
                Edit
            </a>
        @endif
    </div>
</x-slot:pageActions>

<div class="row" x-data="{ currentStep: 1 }">
    <div class="col-md-12">
        <x-tabler.alert />
    </div>

    <!-- Steps Indicator -->
    <div class="mb-3 col-md-12">
        <div class="card">
            <div class="card-body">
                <ul class="my-4 steps steps-green steps-counter">
                    <li class="step-item" :class="{ 'active': currentStep === 1 }">
                        <a href="#" @click.prevent="currentStep = 1" class="text-decoration-none">Identitas
                            Usulan</a>
                    </li>
                    <li class="step-item" :class="{ 'active': currentStep === 2 }">
                        <a href="#" @click.prevent="currentStep = 2" class="text-decoration-none">Substansi
                            Usulan</a>
                    </li>
                    <li class="step-item" :class="{ 'active': currentStep === 3 }">
                        <a href="#" @click.prevent="currentStep = 3" class="text-decoration-none">RAB</a>
                    </li>
                    <li class="step-item" :class="{ 'active': currentStep === 4 }">
                        <a href="#" @click.prevent="currentStep = 4" class="text-decoration-none">Dokumen
                            Pendukung</a>
                    </li>
                    <li class="step-item" :class="{ 'active': currentStep === 5 }">
                        <a href="#" @click.prevent="currentStep = 5" class="text-decoration-none">Workflow &
                            Aksi</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="col-md-12">
        <!-- Section 1: Identitas Usulan -->
        <div id="section-identitas" x-show="currentStep === 1">
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">1.1 Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-file-text class="me-2 icon" />Judul</label>
                            <p class="text-reset">{{ $proposal->title }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-info class="me-2 icon" />Status</label>
                            <p>
                                <x-tabler.badge :color="$proposal->status->color()" class="fw-normal">
                                    {{ $proposal->status->label() }}
                                </x-tabler.badge>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-user class="me-2 icon" />Author</label>
                            <p class="text-reset">{{ $proposal->submitter?->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-mail class="me-2 icon" />Email</label>
                            <p class="text-reset">{{ $proposal->submitter?->email }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-clipboard-list class="me-2 icon" />Skema</label>
                            <p class="text-reset">{{ $proposal->communityServiceScheme?->name ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-calendar class="me-2 icon" />Durasi (Tahun)</label>
                            <p class="text-reset">{{ $proposal->duration_in_years ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">1.2 Informasi Dasar Proposal</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-focus class="me-2 icon" />Bidang Fokus</label>
                            <p class="text-reset">{{ $proposal->focusArea?->name ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-tag class="me-2 icon" />Tema</label>
                            <p class="text-reset">{{ $proposal->theme?->name ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-hash class="me-2 icon" />Topik</label>
                            <p class="text-reset">{{ $proposal->topic?->name ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-star class="me-2 icon" />Prioritas Nasional</label>
                            <p class="text-reset">{{ $proposal->nationalPriority?->name ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-dollar-sign class="me-2 icon" />Nilai SBK</label>
                            <p class="text-reset">{{ number_format($proposal->sbk_value, 2) ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">1.3 Klasifikasi Ilmu</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-12">
                            <label class="form-label">Level 1</label>
                            <p class="text-reset">{{ $proposal->clusterLevel1?->name ?? '—' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Level 2</label>
                            <p class="text-reset">{{ $proposal->clusterLevel2?->name ?? '—' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Level 3</label>
                            <p class="text-reset">{{ $proposal->clusterLevel3?->name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">1.4 Ringkasan</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-reset">{{ $proposal->summary ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">1.5 Detail Pengabdian</h3>
                </div>
                <div class="card-body">
                    @php $communityService = $proposal->detailable; @endphp
                    <div class="mb-3">
                        <label class="form-label">Lokasi Kegiatan</label>
                        <p class="text-reset">{{ $communityService?->activity_location ?? '—' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Latar Belakang</label>
                        <p class="text-reset">{{ $communityService?->background ?? '—' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metodologi</label>
                        <p class="text-reset">{{ $communityService?->methodology ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Team Members Management -->
            <div class="mb-3">
                <livewire:community-service.proposal.team-member-form :proposalId="$proposal->id" :key="'team-form-' . $proposal->id" />
            </div>
        </div>

        <!-- Section 2: Substansi Usulan -->
        <div id="section-substansi" x-show="currentStep === 2">
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">2.1 Kelompok Makro Riset</h3>
                </div>
                <div class="card-body">
                    @php $communityService = $proposal->detailable; @endphp
                    <div class="mb-3">
                        <label class="form-label">Kelompok Makro Riset</label>
                        <p class="text-reset">{{ $communityService?->macroResearchGroup?->name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">2.2 File Substansi</h3>
                </div>
                <div class="card-body">
                    @php $communityService = $proposal->detailable; @endphp
                    <div class="mb-3">
                        <label class="form-label">File Substansi</label>
                        @if ($communityService?->substance_file)
                            <p>
                                <a href="{{ Storage::url($communityService->substance_file) }}" target="_blank"
                                    class="btn-outline-primary btn btn-sm">
                                    <x-lucide-download class="icon" />
                                    Download File
                                </a>
                            </p>
                        @else
                            <p class="text-muted text-reset">Tidak ada file</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Section 2.3.1: Luaran Wajib --}}
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">2.3.1 Luaran Wajib</h3>
                </div>
                @php
                    $requiredOutputs = $proposal->outputs->where('category', 'Wajib');
                @endphp
                @if ($requiredOutputs->isEmpty())
                    <div class="card-body">
                        <p class="text-muted">Belum ada luaran wajib</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Tahun</th>
                                    <th>Kelompok</th>
                                    <th>Luaran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requiredOutputs as $output)
                                    <tr>
                                        <td>{{ $output->output_year }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $output->group)) }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $output->type)) }}</td>
                                        <td>{{ $output->target_status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Section 2.3.2: Luaran Tambahan --}}
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">2.3.2 Luaran Tambahan</h3>
                </div>
                @php
                    $additionalOutputs = $proposal->outputs->where('category', 'Tambahan');
                @endphp
                @if ($additionalOutputs->isEmpty())
                    <div class="card-body">
                        <p class="text-muted">Belum ada luaran tambahan</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Tahun</th>
                                    <th>Kelompok</th>
                                    <th>Luaran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($additionalOutputs as $output)
                                    <tr>
                                        <td>{{ $output->output_year }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $output->group)) }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $output->type)) }}</td>
                                        <td>{{ $output->target_status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 3: RAB -->
        <div id="section-rab" x-show="currentStep === 3">
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">3.1 Rencana Anggaran Biaya (RAB)</h3>
                </div>
                @if ($proposal->budgetItems->isEmpty())
                    <p class="text-muted">Belum ada item anggaran</p>
                @else
                    <div class="table-responsive">
                        <table class="card-table table table-bordered">
                            <thead>
                                <tr>
                                    <th>Kelompok</th>
                                    <th>Komponen</th>
                                    {{-- <th>Item</th> --}}
                                    <th>Volume</th>
                                    <th>Unit</th>
                                    <th>Harga Satuan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proposal->budgetItems as $item)
                                    <tr>
                                        <td>{{ $item->budgetGroup?->name ?? ($item->group ?? '-') }}</td>
                                        <td>{{ $item->budgetComponent?->name ?? ($item->component ?? '-') }}</td>
                                        {{-- <td>{{ $item->item_description }}</td> --}}
                                        <td>{{ $item->volume }}</td>
                                        <td><x-tabler.badge>{{ $item->budgetComponent?->unit ?? '-' }}</x-tabler.badge>
                                        </td>
                                        <td>Rp {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-end">Total Anggaran:</th>
                                    <th>Rp
                                        {{ number_format($proposal->budgetItems->sum('total_price'), 2, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 4: Dokumen Pendukung -->
        <div id="section-dokumen" x-show="currentStep === 4">
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">4.1 Mitra Kerjasama</h3>
                </div>
                <div class="card-body">
                    @if ($proposal->partners->isEmpty())
                        <p class="text-muted">Belum ada mitra yang ditambahkan</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Nama Mitra</th>
                                        <th>Institusi</th>
                                        <th>Email</th>
                                        <th>Negara</th>
                                        <th>Alamat</th>
                                        <th>Tipe</th>
                                        <th>Surat Kesanggupan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proposal->partners as $partner)
                                        <tr>
                                            <td>
                                                <div class="font-weight-medium">{{ $partner->name }}</div>
                                            </td>
                                            <td>
                                                @if ($partner->institution)
                                                    <div class="d-flex align-items-center">
                                                        <x-lucide-building class="icon me-1 text-muted" />
                                                        {{ $partner->institution }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($partner->email)
                                                    <a href="mailto:{{ $partner->email }}" class="text-reset">
                                                        <div class="d-flex align-items-center">
                                                            <x-lucide-mail class="icon me-1 text-muted" />
                                                            {{ $partner->email }}
                                                        </div>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($partner->country)
                                                    <div class="d-flex align-items-center">
                                                        <x-lucide-map-pin class="icon me-1 text-muted" />
                                                        {{ $partner->country }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($partner->address)
                                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $partner->address }}">
                                                        {{ $partner->address }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-blue-lt">
                                                    {{ $partner->type ?? 'External' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($partner->commitment_letter_file)
                                                    <a href="{{ Storage::url($partner->commitment_letter_file) }}" 
                                                       target="_blank" 
                                                       onclick="setTimeout(() => window.location.reload(), 100)"
                                                       class="btn btn-sm btn-primary">
                                                        <x-lucide-download class="icon" />
                                                        Unduh
                                                    </a>
                                                @else
                                                    <span class="badge bg-yellow-lt text-yellow-fg">
                                                        <x-lucide-file-x class="icon me-1" />
                                                        Tidak Ada
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section 5: Workflow & Aksi -->
        <div id="section-workflow" x-show="currentStep === 5">
            <!-- Reviewer Assignment (Admin Only) -->
            @if (auth()->user()->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita']) &&
                    $proposal->status->value === 'under_review')
                <div class="mb-3">
                    <livewire:community-service.proposal.reviewer-assignment :proposalId="$proposal->id" :key="'reviewer-assignment-' . $proposal->id" />
                </div>
            @endif

            <!-- Reviewer Form -->
            <div class="mb-3">
                <livewire:community-service.proposal.reviewer-form :proposalId="$proposal->id" :key="'reviewer-form-' . $proposal->id" />
            </div>

            <!-- Dekan Approval (Status: SUBMITTED) -->
            @if (auth()->user()->hasRole(['dekan', 'rektor']) && $proposal->status->value === 'submitted')
                <div class="mb-3 card">
                    <div class="card-header">
                        <h3 class="card-title">Persetujuan Dekan</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-3 text-secondary">
                            Silakan tinjau proposal ini dan berikan keputusan Anda sebagai Dekan.
                        </p>
                        <div class="gap-2 btn-list">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#approvalModal" wire:click="$set('approvalDecision', 'approved')">
                                <x-lucide-check class="icon" />
                                Setujui Proposal
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Kepala LPPM Initial Approval -->
            @if (auth()->user()->hasRole(['kepala lppm', 'rektor']) && $proposal->status->value === 'approved')
                <div class="mb-3">
                    <livewire:community-service.proposal.kepala-lppm-initial-approval :proposalId="$proposal->id"
                        :key="'initial-approval-' . $proposal->id" />
                </div>
            @endif

            <!-- Kepala LPPM Final Decision -->
            @if (auth()->user()->hasRole(['kepala lppm', 'rektor']) && $proposal->status->value === 'reviewed')
                <div class="mb-3">
                    <livewire:community-service.proposal.kepala-lppm-final-decision :proposalId="$proposal->id"
                        :key="'final-decision-' . $proposal->id" />
                </div>
            @endif


            <div class="row g-3">
                <div class="col-md-4">
                    <!-- Status & Actions Card -->
                    <div class="mb-3 h-100 card">
                        <div class="card-header">
                            <h3 class="card-title">Status & Aksi</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status Saat Ini</label>
                                <p>
                                    <x-tabler.badge :color="$proposal->status->color()" class="fw-normal">
                                        {{ $proposal->status->label() }}
                                    </x-tabler.badge>
                                </p>
                            </div>

                            @if ($proposal->status->value === 'draft')
                                <livewire:community-service.proposal.submit-button :proposalId="$proposal->id"
                                    :key="'submit-button-' . $proposal->id" />
                            @endif

                            {{-- Accept/Reject for team members --}}
                            @php
                                $currentMember = $proposal->teamMembers->firstWhere('id', auth()->id());
                            @endphp
                            @if ($currentMember && $currentMember->pivot->status === 'pending')
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#acceptMemberModal">
                                        <x-lucide-check class="icon" />
                                        Terima Undangan
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectMemberModal">
                                        <x-lucide-x class="icon" />
                                        Tolak Undangan
                                    </button>
                                </div>
                            @endif

                            @if ($proposal->status->value !== 'completed' && $proposal->submitter_id === auth()->id())
                                <button type="button" class="btn-outline-danger btn" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                    <x-lucide-trash-2 class="icon" />
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Timeline Card -->
                    <div class="mb-3 h-100 card">
                        <div class="card-header">
                            <h4 class="mb-0 card-title">Status Proposal</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Dibuat Pada</label>
                                <p>{{ $proposal->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Terakhir Diperbarui</label>
                                <p>{{ $proposal->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Review Status Card -->
                    <div class="mb-3 h-100 card">
                        <div class="card-header">
                            <h4 class="mb-0 card-title">Review Status</h4>
                        </div>
                        @php $reviewers = $proposal->reviewers; @endphp
                        @if ($reviewers->isEmpty())
                            <p class="text-muted">Belum ada reviewer yang ditugaskan</p>
                        @else
                            <div class="table-responsive">
                                <table class="card-table table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Reviewer</th>
                                            <th>Status</th>
                                            <th>Tanggal Review</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($reviewers as $reviewer)
                                            <tr>
                                                <td>{{ $reviewer->user?->name ?? '-' }}</td>
                                                <td>
                                                    <x-tabler.badge :color="$reviewer->status === 'completed' ? 'success' : 'warning'">
                                                        {{ ucfirst($reviewer->status) }}
                                                    </x-tabler.badge>
                                                </td>
                                                <td>{{ $reviewer->updated_at?->format('d M Y H:i') ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status History Section -->
            <div class="mt-4 mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Status</h3>
                </div>
                <div class="card-body">
                    @if ($proposal->statusLogs->isEmpty())
                        <p class="text-muted">Belum ada riwayat perubahan status</p>
                    @else
                        <div class="timeline">
                            @foreach ($proposal->statusLogs as $log)
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong>{{ $log->status_before?->label() ?? '—' }}</strong>
                                                <x-lucide-arrow-right class="mx-2 icon" style="width: 1rem; height: 1rem;" />
                                                <strong>{{ $log->status_after->label() }}</strong>
                                            </div>
                                            <small class="text-muted">{{ $log->at->format('d M Y H:i') }}</small>
                                        </div>
                                        <p class="text-secondary mb-1">
                                            Oleh: <strong>{{ $log->user?->name ?? '—' }}</strong>
                                        </p>
                                        @if ($log->notes)
                                            <p class="text-secondary mb-0">
                                                Catatan: {{ $log->notes }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="mt-4">
        <div class="d-flex justify-content-between">
            <button type="button" class="btn" @click="currentStep--" x-show="currentStep > 1">
                <x-lucide-arrow-left class="icon" />
                Kembali
            </button>
            <div x-show="currentStep === 1"></div>
            <button type="button" class="btn btn-primary" @click="currentStep++" x-show="currentStep < 5">
                Selanjutnya
                <x-lucide-arrow-right class="icon" />
            </button>
        </div>
    </div>


    <!-- Delete Modal -->
    @if ($proposal->status->value !== 'completed' && $proposal->submitter_id === auth()->id())
        @teleport('body')
            <x-tabler.modal id="deleteModal" title="Hapus Proposal?" wire:ignore.self>
                <x-slot:body>
                    <div class="py-1 text-center">
                        <x-lucide-alert-circle class="mb-2 text-danger icon" style="width: 3rem; height: 3rem;" />
                        <h3>Hapus Proposal?</h3>
                        <div class="text-secondary">
                            Apakah Anda yakin ingin menghapus proposal ini? Tindakan ini tidak dapat dibatalkan.
                        </div>
                    </div>
                </x-slot:body>

                <x-slot:footer>
                    <button type="button" class="btn-outline-secondary btn" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" wire:click="delete" class="btn btn-danger" data-bs-dismiss="modal">
                        Ya, Hapus Proposal
                    </button>
                </x-slot:footer>
            </x-tabler.modal>
        @endteleport
    @endif

    <!-- Accept Member Confirmation Modal -->
    @teleport('body')
        <x-tabler.modal id="acceptMemberModal" title="Terima Undangan?" wire:ignore.self>
            <x-slot:body>
                <div class="py-1 text-center">
                    <x-lucide-check-circle class="mb-2 text-success icon" style="width: 3rem; height: 3rem;" />
                    <h3>Terima Undangan?</h3>
                    <div class="text-secondary">
                        Apakah Anda yakin ingin menerima undangan sebagai anggota tim proposal ini?
                    </div>
                </div>
            </x-slot:body>

            <x-slot:footer>
                <button type="button" class="btn-outline-secondary btn" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" wire:click="acceptMember" class="btn btn-success" data-bs-dismiss="modal" onclick="setTimeout(() => window.location.reload(), 3000)">
                    Ya, Terima
                </button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport

    <!-- Reject Member Confirmation Modal -->
    @teleport('body')
        <x-tabler.modal id="rejectMemberModal" title="Tolak Undangan?" wire:ignore.self>
            <x-slot:body>
                <div class="py-1 text-center">
                    <x-lucide-x-circle class="mb-2 text-danger icon" style="width: 3rem; height: 3rem;" />
                    <h3>Tolak Undangan?</h3>
                    <div class="text-secondary">
                        Apakah Anda yakin ingin menolak undangan sebagai anggota tim proposal ini?
                    </div>
                </div>
            </x-slot:body>

            <x-slot:footer>
                <button type="button" class="btn-outline-secondary btn" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" wire:click="rejectMember" class="btn btn-danger" data-bs-dismiss="modal" onclick="setTimeout(() => window.location.reload(), 3000)">
                    Ya, Tolak
                </button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport

    <!-- Approval Modal (Dekan Decision) -->
    @teleport('body')
        <x-tabler.modal id="approvalModal" title="Keputusan Dekan" wire:ignore.self>
            <x-slot:body>
                <div class="py-1 text-center">
                    <x-lucide-check-circle class="mb-2 text-primary icon" style="width: 3rem; height: 3rem;" />
                    <h3>Konfirmasi Keputusan</h3>
                    <div class="text-secondary">
                        Apakah Anda yakin dengan keputusan ini?
                    </div>
                </div>
            </x-slot:body>

            <x-slot:footer>
                <button type="button" class="btn-outline-secondary btn" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" wire:click="submitDekanDecision" class="btn btn-primary" data-bs-dismiss="modal">
                    Ya, Konfirmasi
                </button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport
</div>
