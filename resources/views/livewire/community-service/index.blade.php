<x-slot:title>{{ __('Pengabdian') }}</x-slot:title>
<x-slot:pageTitle>{{ __('Daftar Pengabdian kepada Masyarakat') }}</x-slot:pageTitle>
<x-slot:pageSubtitle>{{ __('Kelola proposal pengabdian Anda dengan fitur lengkap.') }}</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        <a href="{{ route('community-service.proposal.create') }}" class="btn btn-primary">
            <x-lucide-plus class="icon" />
            {{ __('Usulan Pengabdian Baru') }}
        </a>
    </div>
</x-slot:pageActions>

<div class="mb-3 row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search Input -->
                    <div class="col-md-6">
                        <input type="text" class="form-control"
                            placeholder="{{ __('Cari berdasarkan judul atau ringkasan...') }}"
                            wire:model.live.debounce.300ms="search" />
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="all">{{ __('Semua Status') }}</option>
                            <option value="draft">{{ __('Draft') }}</option>
                            <option value="submitted">{{ __('Diajukan') }}</option>
                            <option value="under_review">{{ __('Dalam Review') }}</option>
                            <option value="approved">{{ __('Disetujui') }}</option>
                            <option value="rejected">{{ __('Ditolak') }}</option>
                            <option value="completed">{{ __('Selesai') }}</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-md-3">
                        <button type="button" class="btn-outline-secondary w-100 btn" wire:click="resetFilters">
                            <x-lucide-rotate-ccw class="icon" />
                            {{ __('Reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Stats -->
<div class="mb-3 row row-deck row-cards">
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="text-truncate">
                    <h3 class="card-title">
                        {{ $statusStats['all'] }}
                    </h3>
                    <div class="text-secondary">{{ __('Total') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="text-truncate">
                    <h3 class="card-title">
                        {{ $statusStats['draft'] }}
                    </h3>
                    <div class="text-secondary">{{ __('Draft') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="text-truncate">
                    <h3 class="card-title">
                        {{ $statusStats['submitted'] }}
                    </h3>
                    <div class="text-secondary">{{ __('Diajukan') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="text-truncate">
                    <h3 class="card-title">
                        {{ $statusStats['approved'] }}
                    </h3>
                    <div class="text-secondary">{{ __('Disetujui') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="text-truncate">
                    <h3 class="card-title">
                        {{ $statusStats['rejected'] }}
                    </h3>
                    <div class="text-secondary">{{ __('Ditolak') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="text-truncate">
                    <h3 class="card-title">
                        {{ $statusStats['completed'] }}
                    </h3>
                    <div class="text-secondary">{{ __('Selesai') }}</div>
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
                    <th>
                        <button type="button" class="p-0 btn btn-link" wire:click="setSortBy('title')">
                            {{ __('Judul') }}
                            @if ($sortBy === 'title')
                                <x-lucide-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} class="icon" />
                            @endif
                        </button>
                    </th>
                    <th>{{ __('Pelaksana') }}</th>
                    <th>
                        <button type="button" class="p-0 btn btn-link" wire:click="setSortBy('status')">
                            {{ __('Status') }}
                            @if ($sortBy === 'status')
                                <x-lucide-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} class="icon" />
                            @endif
                        </button>
                    </th>
                    <th>{{ __('Bidang Fokus') }}</th>
                    <th>
                        <button type="button" class="p-0 btn btn-link" wire:click="setSortBy('created_at')">
                            {{ __('Tanggal Dibuat') }}
                            @if ($sortBy === 'created_at')
                                <x-lucide-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} class="icon" />
                            @endif
                        </button>
                    </th>
                    <th class="w-1">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($proposals as $proposal)
                    <tr wire:key="proposal-{{ $proposal->id }}">
                        <td class="text-truncate" style="max-width: 250px;">
                            <div class="text-reset fw-bold">{{ $proposal->title }}</div>
                            <div class="text-secondary text-truncate">{{ Str::limit($proposal->summary, 60) }}
                            </div>
                        </td>
                        <td>
                            <div>{{ $proposal->submitter?->name }}</div>
                            <small class="text-secondary">{{ $proposal->submitter?->email }}</small>
                        </td>
                        <td>
                            <x-tabler.badge :color="$proposal->status" class="fw-normal">
                                {{ __('Status: :status', ['status' => ucfirst($proposal->status)]) }}
                            </x-tabler.badge>
                        </td>
                        <td>
                            <div class="badge-outline badge">
                                {{ $proposal->focusArea?->name ?? '—' }}
                            </div>
                        </td>
                        <td>
                            <small class="text-secondary">
                                {{ $proposal->created_at?->format('d M Y') }}
                            </small>
                        </td>
                        <td>
                            <div class="flex-nowrap btn-list">
                                <a href="{{ route('community-service.proposal.show', $proposal) }}"
                                    class="btn btn-icon btn-ghost-primary" title="{{ __('Lihat') }}">
                                    <x-lucide-eye class="icon" />
                                </a>
                                @if ($proposal->status === 'draft')
                                    <a href="#" class="btn btn-icon btn-ghost-info" title="{{ __('Edit') }}">
                                        <x-lucide-pencil class="icon" />
                                    </a>
                                @endif
                                <button type="button" class="btn btn-icon btn-ghost-danger"
                                    title="{{ __('Hapus') }}" wire:click="deleteProposal({{ $proposal->id }})"
                                    wire:confirm="{{ __('Yakin ingin menghapus proposal ini?') }}">
                                    <x-lucide-trash-2 class="icon" />
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
                            <p class="text-secondary">{{ __('Tidak ada data pengabdian yang ditemukan.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($proposals->hasPages())
        <div class="d-flex align-items-center card-footer">
            {{ $proposals->links() }}
        </div>
    @endif
</div>
