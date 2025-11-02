<x-slot:title>{{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>{{ $proposal->title }}</x-slot:pageTitle>
<x-slot:pageSubtitle>Detail Proposal Penelitian</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        @if (auth()->user()->hasRole('reviewer'))
            <a href="{{ route('review.research') }}" class="btn-outline-secondary btn" wire:navigate>
                <x-lucide-arrow-left class="icon" />
                Kembali
            </a>
        @else
            <a href="{{ route('research.proposal.index') }}" class="btn-outline-secondary btn" wire:navigate>
                <x-lucide-arrow-left class="icon" />
                Kembali
            </a>
        @endif
        @if ($proposal->status->value === 'draft' && $proposal->submitter_id === auth()->id())
            <a href="{{ route('research.proposal.edit', $proposal) }}" wire:navigate class="btn btn-primary">
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
                        <a href="#" @click.prevent="currentStep = 1" class="text-decoration-none">Identitas Usulan</a>
                    </li>
                    <li class="step-item" :class="{ 'active': currentStep === 2 }">
                        <a href="#" @click.prevent="currentStep = 2" class="text-decoration-none">Substansi Usulan</a>
                    </li>
                    <li class="step-item" :class="{ 'active': currentStep === 3 }">
                        <a href="#" @click.prevent="currentStep = 3" class="text-decoration-none">RAB</a>
                    </li>
                    <li class="step-item" :class="{ 'active': currentStep === 4 }">
                        <a href="#" @click.prevent="currentStep = 4" class="text-decoration-none">Dokumen Pendukung</a>
                    </li>
                    <li class="step-item" :class="{ 'active': currentStep === 5 }">
                        <a href="#" @click.prevent="currentStep = 5" class="text-decoration-none">Workflow & Aksi</a>
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
                    <h4 class="mb-0 card-title">Informasi Dasar Proposal</h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Judul Proposal</label>
                            <p>{{ $proposal->title }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Skema Penelitian</label>
                            <p>{{ $proposal->researchScheme?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Durasi</label>
                            <p>{{ $proposal->duration_in_years }} Tahun</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Bidang Fokus</label>
                            <p>{{ $proposal->focusArea?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tema</label>
                            <p>{{ $proposal->theme?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Topik</label>
                            <p>{{ $proposal->topic?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Ringkasan</label>
                            <p>{{ $proposal->summary }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 card">
                <div class="card-header">
                    <h4 class="mb-0 card-title">Klasifikasi Ilmu (Klaster Sains)</h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Level 1</label>
                            <p>{{ $proposal->clusterLevel1?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Level 2</label>
                            <p>{{ $proposal->clusterLevel2?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Level 3</label>
                            <p>{{ $proposal->clusterLevel3?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 card">
                <div class="card-header">
                    <h4 class="mb-0 card-title">Detail Penelitian</h4>
                </div>
                <div class="card-body">
                    @php $research = $proposal->detailable; @endphp
                    <div class="mb-3">
                        <label class="form-label fw-bold">Target TKT Final</label>
                        <p>{{ $research?->final_tkt_target ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Latar Belakang</label>
                        <p>{{ $research?->background ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">State of the Art</label>
                        <p>{{ $research?->state_of_the_art ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Metodologi</label>
                        <p>{{ $research?->methodology ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-3 card">
                <div class="card-header">
                    <h4 class="mb-0 card-title">Tim Peneliti</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIDN</th>
                                    <th>Peran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proposal->teamMembers as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->identity?->identity_id ?? '-' }}</td>
                                        <td>
                                            <span class="bg-info badge">{{ ucfirst($member->pivot->role) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $member->pivot->status === 'accepted' ? 'success' : 'warning' }}">
                                                {{ ucfirst($member->pivot->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Substansi Usulan -->
        <div id="section-substansi" x-show="currentStep === 2">
            <div class="mb-3 card">
                <div class="card-header">
                    <h4 class="mb-0 card-title">Substansi Usulan</h4>
                </div>
                <div class="card-body">
                    @php $research = $proposal->detailable; @endphp
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kelompok Makro Riset</label>
                        <p>{{ $research?->macroResearchGroup?->name ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">File Substansi</label>
                        @if ($research?->substance_file)
                            <p>
                                <a href="{{ Storage::url($research->substance_file) }}" target="_blank"
                                    class="btn-outline-primary btn btn-sm">
                                    <x-lucide-download class="icon" />
                                    Download File
                                </a>
                            </p>
                        @else
                            <p class="text-muted">Tidak ada file</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-3 card">
                <div class="card-header">
                    <h4 class="mb-0 card-title">Luaran Target Capaian</h4>
                </div>
                <div class="card-body">
                    @if ($proposal->outputs->isEmpty())
                        <p class="text-muted">Belum ada luaran target</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Tahun</th>
                                        <th>Kategori</th>
                                        <th>Luaran</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proposal->outputs as $output)
                                        <tr>
                                            <td>{{ $output->output_year }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $output->category)) }}</td>
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
        </div>

        <!-- Section 3: RAB -->
        <div id="section-rab" x-show="currentStep === 3">
            <div class="mb-3 card">
                <div class="card-header">
                    <h4 class="mb-0 card-title">Rencana Anggaran Biaya (RAB)</h4>
                </div>
                <div class="card-body">
                    @if ($proposal->budgetItems->isEmpty())
                        <p class="text-muted">Belum ada item anggaran</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Kelompok</th>
                                        <th>Komponen</th>
                                        <th>Item</th>
                                        <th>Volume</th>
                                        <th>Harga Satuan</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proposal->budgetItems as $item)
                                        <tr>
                                            <td>{{ $item->budgetGroup?->name ?? ($item->group ?? '-') }}</td>
                                            <td>{{ $item->budgetComponent?->name ?? ($item->component ?? '-') }}</td>
                                            <td>{{ $item->item_description }}</td>
                                            <td>{{ $item->volume }}</td>
                                            <td>Rp {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">Total Anggaran:</th>
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
        </div>

        <!-- Section 4: Dokumen Pendukung -->
        <div id="section-dokumen" x-show="currentStep === 4">
            <div class="mb-3 card">
                <div class="card-header">
                    <h4 class="mb-0 card-title">Mitra Kerjasama</h4>
                </div>
                <div class="card-body">
                    @if ($proposal->partners->isEmpty())
                        <p class="text-muted">Belum ada mitra yang ditambahkan</p>
                    @else
                        <div class="list-group">
                            @foreach ($proposal->partners as $partner)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <h5 class="mb-1">{{ $partner->name }}</h5>
                                            <p class="mb-1 text-muted">
                                                @if ($partner->institution)
                                                    <x-lucide-building class="icon-inline icon" />
                                                    {{ $partner->institution }}
                                                @endif
                                                @if ($partner->country)
                                                    <x-lucide-map-pin class="icon-inline ms-2 icon" />
                                                    {{ $partner->country }}
                                                @endif
                                            </p>
                                            @if ($partner->email)
                                                <small class="text-muted">
                                                    <x-lucide-mail class="icon-inline icon" /> {{ $partner->email }}
                                                </small>
                                            @endif
                                            @if ($partner->commitment_letter_file)
                                                <div class="mt-2">
                                                    <a href="{{ Storage::url($partner->commitment_letter_file) }}"
                                                        target="_blank" class="btn-outline-primary btn btn-sm">
                                                        <x-lucide-download class="icon" />
                                                        Surat Kesanggupan
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section 5: Workflow & Aksi -->
        <div id="section-workflow" x-show="currentStep === 5">
            <!-- Team Members Management -->
            <div class="mb-3">
                <livewire:research.proposal.team-member-form :proposalId="$proposal->id" :key="'team-form-' . $proposal->id" />
            </div>

            <!-- Reviewer Assignment (Admin Only) -->
            @if (auth()->user()->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']) &&
                    $proposal->status->value === 'under_review')
                <div class="mb-3">
                    <livewire:research.proposal.reviewer-assignment :proposalId="$proposal->id" :key="'reviewer-assignment-' . $proposal->id" />
                </div>
            @endif

            <!-- Reviewer Form -->
            <div class="mb-3">
                <livewire:research.proposal.reviewer-form :proposalId="$proposal->id" :key="'reviewer-form-' . $proposal->id" />
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
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#approvalModal"
                                wire:click="$set('approvalDecision', 'need_assignment')">
                                <x-lucide-alert-triangle class="icon" />
                                Perlu Perbaikan Anggota
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Kepala LPPM Initial Approval -->
            @if (auth()->user()->hasRole(['kepala lppm', 'rektor']) && $proposal->status->value === 'approved')
                <div class="mb-3">
                    <livewire:research.proposal.kepala-lppm-initial-approval :proposalId="$proposal->id" :key="'initial-approval-' . $proposal->id" />
                </div>
            @endif

            <!-- Kepala LPPM Final Decision -->
            @if (auth()->user()->hasRole(['kepala lppm', 'rektor']) && $proposal->status->value === 'reviewed')
                <div class="mb-3">
                    <livewire:research.proposal.kepala-lppm-final-decision :proposalId="$proposal->id" :key="'final-decision-' . $proposal->id" />
                </div>
            @endif

            <!-- Status & Actions Card -->
            <div class="mb-3 card">
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
                        <livewire:research.proposal.submit-button :proposalId="$proposal->id" :key="'submit-button-' . $proposal->id" />
                    @endif

                    {{-- Accept/Reject for team members --}}
                    @php
                        $currentMember = $proposal->teamMembers->firstWhere('id', auth()->id());
                    @endphp
                    @if ($currentMember && $currentMember->pivot->status === 'pending')
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-success" wire:click="acceptMember">
                                <x-lucide-check class="icon" />
                                Terima Undangan
                            </button>
                            <button type="button" class="btn btn-danger" wire:click="rejectMember">
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

            <!-- Timeline Card -->
            <div class="mb-3 card">
                <div class="card-header">
                    <h4 class="mb-0 card-title">Status Proposal</h4>
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

            <div class="mb-3 card">
                <div class="card-header">
                    <h4 class="mb-0 card-title">Review Status</h4>
                </div>
                <div class="card-body">
                    @php $reviewers = $proposal->reviewers; @endphp
                    @if ($reviewers->isEmpty())
                        <p class="text-muted">Belum ada reviewer yang ditugaskan</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
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
                                            <td>{{ $reviewer->reviewer?->name ?? '-' }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $reviewer->status === 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($reviewer->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $reviewer->reviewed_at?->format('d M Y H:i') ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
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
</div>
