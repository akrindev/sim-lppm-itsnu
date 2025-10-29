<x-slot:title>{{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>{{ $proposal->title }}</x-slot:pageTitle>
<x-slot:pageSubtitle>Detail Proposal Penelitian</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        <a href="{{ route('research.proposal.index') }}" class="btn-outline-secondary btn">
            <x-lucide-arrow-left class="icon" />
            Kembali
        </a>
        @if ($proposal->status === 'draft' && $proposal->submitter_id === auth()->id())
            <a href="{{ route('research.proposal.edit', $proposal) }}" wire:navigate class="btn btn-primary">
                <x-lucide-pencil class="icon" />
                Edit
            </a>
        @endif
    </div>
</x-slot:pageActions>


<div class="row">
    <div class="col-md-12">
        <x-tabler.alert />
    </div>
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="mb-3 card">
            <div class="card-header">
                <h3 class="card-title">1.1 Informasi Dasar</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Judul</label>
                        <p class="text-reset">{{ $proposal->title }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <p>
                            <x-tabler.badge :color="$proposal->status" class="fw-normal">
                                {{ ucfirst($proposal->status) }}
                            </x-tabler.badge>
                        </p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Author</label>
                        <p class="text-reset">{{ $proposal->submitter?->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <p class="text-reset">{{ $proposal->submitter?->email }}</p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Skema Penelitian</label>
                        <p class="text-reset">{{ $proposal->researchScheme?->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Durasi (Tahun)</label>
                        <p class="text-reset">{{ $proposal->duration_in_years ?? '—' }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ringkasan</label>
                    <p class="text-reset">{{ $proposal->summary ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Classification -->
        <div class="mb-3 card">
            <div class="card-header">
                <h3 class="card-title">1.2 Klasifikasi</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Bidang Fokus</label>
                        <p class="text-reset">{{ $proposal->focusArea?->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tema</label>
                        <p class="text-reset">{{ $proposal->theme?->name ?? '—' }}</p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Topik</label>
                        <p class="text-reset">{{ $proposal->topic?->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Prioritas Nasional</label>
                        <p class="text-reset">{{ $proposal->nationalPriority?->name ?? '—' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Nilai SBK</label>
                        <p class="text-reset">{{ number_format($proposal->sbk_value, 2) ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- klasifikasi ilmu --}}
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


        <!-- Team Members Management -->
        <div class="mb-3">
            <livewire:research.proposal.team-member-form :proposalId="$proposal->id" :key="'team-form-' . $proposal->id" />
        </div>

        <!-- Team Member Invitations Status -->
        {{-- <div class="mb-3">
            <livewire:research.proposal.team-member-invitations :proposalId="$proposal->id" :key="'team-invitations-' . $proposal->id" />
        </div> --}}

        <!-- Reviewer Assignment (Admin Only - Submitted Status) -->
        @if (auth()->user()->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']))
            <div class="mb-3">
                <livewire:research.proposal.reviewer-assignment :proposalId="$proposal->id" :key="'reviewer-assignment-' . $proposal->id" />
            </div>
        @endif

        <!-- Reviewer Form (For Reviewers - Under Review Status) -->
        @if ($proposal->status === 'reviewed' || $proposal->status === 'under_review')
            <div class="mb-3">
                <livewire:research.proposal.reviewer-form :proposalId="$proposal->id" :key="'reviewer-form-' . $proposal->id" />
            </div>
        @endif

        <!-- Approval Button (Admin Only - Reviewed Status) -->
        @if (auth()->user()->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']) &&
                in_array($proposal->status, ['reviewed', 'under_review']))
            <div class="mb-3">
                <livewire:research.proposal.approval-button :proposalId="$proposal->id" :key="'approval-button-' . $proposal->id" />
            </div>
        @endif

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timeline</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Dibuat</label>
                        <p class="text-reset">{{ $proposal->created_at?->format('d M Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Diubah</label>
                        <p class="text-reset">{{ $proposal->updated_at?->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="mb-3 card">
            <div class="card-header">
                <h3 class="card-title">Status Proposal</h3>
            </div>
            <div class="text-center card-body">
                <p class="text-secondary text-sm">
                    @switch($proposal->status)
                        @case('draft')
                            Proposal masih dalam tahap penyusunan. Anda dapat mengedit atau mengirimkan proposal ini.
                        @break

                        @case('submitted')
                            Proposal telah diajukan dan sedang menunggu review dari tim LPPM.
                        @break

                        @case('under_review')
                            Proposal sedang dalam proses review. Silahkan tunggu hasil evaluasi.
                        @break

                        @case('approved')
                            Selamat! Proposal Anda telah disetujui. Silahkan mulai melaksanakan kegiatan.
                        @break

                        @case('rejected')
                            Sayangnya proposal Anda ditolak. Silahkan perbaiki dan ajukan kembali.
                        @break

                        @case('completed')
                            Proposal ini telah selesai dilaksanakan.
                        @break

                        @default
                            Status proposal tidak diketahui.
                    @endswitch
                </p>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi</h3>
            </div>
            <div class="gap-2 d-grid card-body">
                @if ($proposal->status === 'draft')
                    <livewire:research.proposal.submit-button :proposalId="$proposal->id" :key="'submit-button-' . $proposal->id" />
                @endif

                @if (in_array($proposal->status, ['submitted', 'under_review', 'rejected']))
                    <a href="#" class="btn btn-info">
                        <x-lucide-eye class="icon" />
                        Lihat Review
                    </a>
                @endif

                @if ($proposal->status === 'approved')
                    <a href="#" class="btn btn-success">
                        <x-lucide-file-text class="icon" />
                        Laporan Progress
                    </a>
                @endif

                {{-- Accept/Reject for team members --}}
                @php
                    $currentMember = $proposal->teamMembers->firstWhere('id', auth()->id());
                @endphp
                @if ($currentMember && $currentMember->pivot->status === 'pending')
                    <div class="d-flex gap-2">
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

                @if (
                    ($proposal->status === 'draft' && $proposal->submitter_id === auth()->id()) ||
                        auth()->user()->hasRole(['admin lppm', 'superadmin']))
                    <button type="button" class="btn-outline-danger btn" data-bs-toggle="modal"
                        data-bs-target="#deleteModal">
                        <x-lucide-trash-2 class="icon" />
                        Hapus
                    </button>

                    <!-- Delete Confirmation Modal -->
                    @teleport('body')
                        <x-tabler.modal id="deleteModal" title="Hapus Proposal?" wire:ignore.self>
                            <x-slot:body>
                                <div class="py-1 text-center">
                                    <x-lucide-alert-circle class="mb-2 text-danger icon"
                                        style="width: 3rem; height: 3rem;" />
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
                                <button type="button" wire:click="delete" class="btn btn-danger"
                                    data-bs-dismiss="modal">
                                    Ya, Hapus Proposal
                                </button>
                            </x-slot:footer>
                        </x-tabler.modal>
                    @endteleport
                @endif

            </div>
        </div>
    </div>
</div>
