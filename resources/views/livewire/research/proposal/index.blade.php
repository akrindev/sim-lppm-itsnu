<x-slot:title>Penelitian</x-slot:title>
<x-slot:pageTitle>Daftar Penelitian</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola proposal penelitian Anda dengan fitur lengkap.</x-slot:pageSubtitle>
<x-slot:pageActions>
    @unless (auth()->user()->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']))
        <div class="btn-list">
            <a href="{{ route('research.proposal.create') }}" wire:navigate class="btn btn-primary">
                <x-lucide-plus class="icon" />
                Usulan Penelitian Baru
            </a>
        </div>
    @endunless
</x-slot:pageActions>


<div>
    <x-tabler.alert />
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

                        <!-- Status Filter -->
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="statusFilter">
                                @foreach (\App\Enums\ProposalStatus::filterOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
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
                        <th>Author</th>
                        {{-- <th>Skema</th> --}}
                        <th>Bidang Fokus</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->proposals as $proposal)
                        <tr wire:key="proposal-{{ $proposal->id }}">
                            <td class="text-wrap">
                                <div class="text-reset fw-bold">{{ $proposal->title }}</div>
                            </td>
                            <td>
                                <div>{{ $proposal->submitter?->name }}</div>
                                <small class="text-secondary">{{ $proposal->submitter?->identity->identity_id }}</small>
                            </td>
                            {{-- <td>
                                <div class="bg-blue-lt badge-outline badge">
                                    {{ $proposal->researchScheme?->name ?? '—' }}
                                </div>
                            </td> --}}
                            <td>
                                <x-tabler.badge variant="outline">
                                    {{ $proposal->focusArea?->name ?? '—' }}
                                </x-tabler.badge>
                            </td>
                            <td>
                                <x-tabler.badge :color="$proposal->status->color()" class="fw-normal">
                                    {{ $proposal->status->label() }}
                                </x-tabler.badge>
                            </td>
                            <td>
                                <small class="text-secondary">
                                    {{ $proposal->created_at?->format('d M Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="flex-nowrap btn-list">
                                    <a href="{{ route('research.proposal.show', $proposal) }}"
                                        class="btn btn-icon btn-ghost-primary" title="Lihat" wire:navigate>
                                        <x-lucide-eye class="icon" />
                                    </a>
                                    {{-- @if ($proposal->status === 'draft')
                                        <a href="#" class="btn btn-icon btn-ghost-info" title="Edit">
                                            <x-lucide-pencil class="icon" />
                                        </a>
                                    @endif --}}
                                    @if (auth()->user()->hasRole('admin lppm') && $proposal->status !== 'completed')
                                        <button type="button" class="btn btn-icon btn-ghost-danger" title="Hapus"
                                            data-bs-toggle="modal" data-bs-target="#deleteProposalModal"
                                            wire:click="confirmDeleteProposal('{{ $proposal->id }}')">
                                            <x-lucide-trash-2 class="icon" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center">
                                <div class="mb-3">
                                    <x-lucide-inbox class="text-secondary icon icon-lg" />
                                </div>
                                <p class="text-secondary">Tidak ada data penelitian yang ditemukan.</p>
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


    <!-- Delete Proposal Confirmation Modal -->
    @teleport('body')
        <x-tabler.modal id="deleteProposalModal" title="Hapus Proposal?" wire:ignore.self>
            <x-slot:body>
                <div class="py-4 text-center">
                    <x-lucide-alert-circle class="mb-2 text-danger icon" style="width: 3rem; height: 3rem;" />
                    <h3>Hapus Proposal?</h3>
                    <div class="text-secondary">
                        Apakah Anda yakin ingin menghapus proposal ini? Tindakan ini tidak dapat dibatalkan.
                    </div>
                </div>
            </x-slot:body>

            <x-slot:footer>
                <div class="w-100">
                    <div class="row">
                        <div class="col"><button type="button" class="w-100 btn btn-white" data-bs-dismiss="modal"
                                wire:click="cancelDeleteProposal">
                                Batal
                            </button></div>
                        <div class="col"><button type="button"
                                wire:click="deleteProposal('{{ $confirmingDeleteProposalId }}')"
                                class="w-100 btn btn-danger" data-bs-dismiss="modal">
                                Ya, Hapus Proposal
                            </button></div>
                    </div>
                </div>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport
</div>
