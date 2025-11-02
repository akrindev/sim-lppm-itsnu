<x-slot:title>Persetujuan Dekan</x-slot:title>
<x-slot:pageTitle>Persetujuan Proposal</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola persetujuan proposal penelitian dan pengabdian yang telah diajukan.</x-slot:pageSubtitle>

<div>
    <x-tabler.alert />

    <!-- Statistics Cards -->
    <div class="mb-3 row row-deck row-cards">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Proposal</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="me-2 mb-0 h1">{{ $this->statusStats['all'] }}</div>
                        <div class="me-auto">
                            <span class="d-inline-flex align-items-center text-secondary lh-1">
                                Menunggu persetujuan
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Proposal Penelitian</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="me-2 mb-0 h1">{{ $this->statusStats['research'] }}</div>
                        <div class="me-auto">
                            <span class="d-inline-flex align-items-center text-blue lh-1">
                                <x-lucide-microscope class="icon icon-sm" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Proposal Pengabdian</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="me-2 mb-0 h1">{{ $this->statusStats['community_service'] }}</div>
                        <div class="me-auto">
                            <span class="d-inline-flex align-items-center text-green lh-1">
                                <x-lucide-hand-heart class="icon icon-sm" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-3 row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-4">
                            <input type="text" class="form-control"
                                placeholder="Cari berdasarkan judul atau ringkasan..."
                                wire:model.live.debounce.300ms="search" />
                        </div>

                        <!-- Type Filter -->
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="typeFilter">
                                <option value="all">Semua Jenis</option>
                                <option value="research">Penelitian</option>
                                <option value="community_service">Pengabdian</option>
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="yearFilter">
                                <option value="">Semua Tahun</option>
                                @foreach ($this->availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="col-md-3">
                            <button type="button" class="btn-outline-secondary w-100 btn" wire:click="resetFilters">
                                <x-lucide-rotate-ccw class="icon" />
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
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Pengusul</th>
                        <th>Bidang Fokus</th>
                        <th>Tanggal Diajukan</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->proposals as $proposal)
                        <tr wire:key="proposal-{{ $proposal->id }}">
                            <td class="text-wrap">
                                <div class="text-reset fw-bold">{{ $proposal->title }}</div>
                                @if ($proposal->summary)
                                    <small class="text-secondary">
                                        {{ Str::limit($proposal->summary, 80) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if ($proposal->detailable_type === 'App\Models\Research')
                                    <x-tabler.badge color="blue" variant="light">
                                        <x-lucide-microscope class="me-1 icon icon-sm" />
                                        Penelitian
                                    </x-tabler.badge>
                                @else
                                    <x-tabler.badge color="green" variant="light">
                                        <x-lucide-hand-heart class="me-1 icon icon-sm" />
                                        Pengabdian
                                    </x-tabler.badge>
                                @endif
                            </td>
                            <td>
                                <div>{{ $proposal->submitter?->name }}</div>
                                <small class="text-secondary">{{ $proposal->submitter?->identity->identity_id }}</small>
                            </td>
                            <td>
                                <div class="badge-outline badge">
                                    {{ $proposal->focusArea?->name ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <small class="text-secondary">
                                    {{ $proposal->created_at?->format('d M Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="flex-nowrap btn-list">
                                    @if ($proposal->detailable_type === 'App\Models\Research')
                                        <a href="{{ route('research.proposal.show', $proposal) }}"
                                            class="btn btn-sm btn-ghost-primary" title="Lihat Detail" wire:navigate>
                                            <x-lucide-eye class="icon" />
                                            Lihat
                                        </a>
                                    @else
                                        <a href="{{ route('community-service.proposal.show', $proposal) }}"
                                            class="btn btn-sm btn-ghost-primary" title="Lihat Detail" wire:navigate>
                                            <x-lucide-eye class="icon" />
                                            Lihat
                                        </a>
                                    @endif

                                    <button type="button" class="btn btn-sm btn-success"
                                        wire:click="openApprovalModal('{{ $proposal->id }}', 'approved')"
                                        title="Setujui Proposal">
                                        <x-lucide-check class="icon" />
                                        Setujui
                                    </button>

                                    <button type="button" class="btn btn-sm btn-warning"
                                        wire:click="openApprovalModal('{{ $proposal->id }}', 'need_assignment')"
                                        title="Perlu Persetujuan Anggota">
                                        <x-lucide-user-check class="icon" />
                                        Perlu Persetujuan
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center">
                                <div class="mb-3">
                                    <x-lucide-inbox class="text-secondary icon icon-lg" />
                                </div>
                                <p class="text-secondary">Tidak ada proposal yang menunggu persetujuan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->proposals->hasPages())
            <div class="d-flex align-items-center card-footer">
                {{ $this->proposals->links() }}
            </div>
        @endif
    </div>

    <!-- Approval Confirmation Modal -->
    @teleport('body')
        <x-tabler.modal id="approvalModal" title="Konfirmasi Persetujuan" wire:ignore.self>
            <x-slot:body>
                <div class="py-3">
                    @if ($approvalDecision === 'approved')
                        <div class="mb-3 text-center">
                            <x-lucide-check-circle class="mb-2 text-success icon" style="width: 3rem; height: 3rem;" />
                            <h3>Setujui Proposal?</h3>
                            <div class="text-secondary">
                                Proposal akan diteruskan ke Kepala LPPM untuk persetujuan lebih lanjut.
                            </div>
                        </div>
                    @else
                        <div class="mb-3 text-center">
                            <x-lucide-alert-triangle class="mb-2 text-warning icon"
                                style="width: 3rem; height: 3rem;" />
                            <h3>Perlu Persetujuan Anggota?</h3>
                            <div class="text-secondary">
                                Proposal akan dikembalikan ke pengusul untuk memperbaiki persetujuan anggota tim.
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" rows="3" wire:model="approvalNotes"
                            placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
            </x-slot:body>

            <x-slot:footer>
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="w-100 btn btn-white" data-bs-dismiss="modal"
                                wire:click="cancelApproval">
                                Batal
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" wire:click="processApproval"
                                class="w-100 btn {{ $approvalDecision === 'approved' ? 'btn-success' : 'btn-warning' }}"
                                data-bs-dismiss="modal">
                                @if ($approvalDecision === 'approved')
                                    <x-lucide-check class="icon" />
                                    Ya, Setujui
                                @else
                                    <x-lucide-user-check class="icon" />
                                    Ya, Kembalikan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport>

    @script
        <script>
            $wire.on('open-approval-modal', () => {
                new bootstrap.Modal(document.getElementById('approvalModal')).show();
            });

            $wire.on('close-approval-modal', () => {
                bootstrap.Modal.getInstance(document.getElementById('approvalModal'))?.hide();
            });
        </script>
    @endscript
</div>
