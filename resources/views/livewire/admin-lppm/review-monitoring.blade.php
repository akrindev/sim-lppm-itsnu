<x-slot:title>Monitoring Progres Review</x-slot:title>
<x-slot:pageTitle>Monitoring Progres Review</x-slot:pageTitle>
<x-slot:pageSubtitle>Pantau status penyelesaian review untuk setiap usulan yang sedang dalam tahap evaluasi.</x-slot:pageSubtitle>

<div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Cari judul proposal..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-4">
                    <select class="form-select" wire:model.live="typeFilter">
                        <option value="all">Semua Jenis</option>
                        <option value="research">Penelitian</option>
                        <option value="community_service">Pengabdian</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Proposal</th>
                        <th>Reviewer & Status</th>
                        <th>Progres</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->proposals as $proposal)
                        <tr wire:key="prop-{{ $proposal->id }}">
                            <td class="text-wrap">
                                <div class="fw-bold">{{ $proposal->title }}</div>
                                <div class="small text-secondary">{{ $proposal->submitter?->name }}</div>
                            </td>
                            <td>
                                @if ($proposal->reviewers->isEmpty())
                                    <span class="text-danger small">Belum ada reviewer ditugaskan</span>
                                @else
                                    <div class="avatar-list avatar-list-stacked mb-2">
                                        @foreach ($proposal->reviewers as $reviewer)
                                            <span class="avatar avatar-xs rounded" title="{{ $reviewer->user?->name }}: {{ $reviewer->status->label() }}">
                                                {{ $reviewer->user?->initials() }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="small">
                                        @foreach ($proposal->reviewers as $reviewer)
                                            <div class="d-flex align-items-center mb-1">
                                                @if ($reviewer->isCompleted())
                                                    <x-lucide-check-circle class="icon icon-sm text-success me-1" />
                                                @else
                                                    <x-lucide-clock class="icon icon-sm text-warning me-1" />
                                                @endif
                                                <span>{{ $reviewer->user?->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $totalRev = $proposal->reviewers->count();
                                    $doneRev = $proposal->reviewers->filter(fn($r) => $r->isCompleted())->count();
                                    $percentage = $totalRev > 0 ? round(($doneRev / $totalRev) * 100) : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-xs w-100 me-2">
                                        <div class="progress-bar bg-{{ $percentage == 100 ? 'success' : 'primary' }}" 
                                            x-data :style="'width: ' + {{ $percentage }} + '%'"></div>
                                    </div>
                                    <span class="small">{{ $doneRev }}/{{ $totalRev }}</span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ $proposal->detailable_type === 'App\Models\Research' ? route('research.proposal.show', $proposal) : route('community-service.proposal.show', $proposal) }}" 
                                    class="btn btn-sm btn-outline-primary" wire:navigate.hover>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Tidak ada proposal dalam tahap review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($this->proposals->hasPages())
            <div class="card-footer">
                {{ $this->proposals->links() }}
            </div>
        @endif
    </div>
</div>
