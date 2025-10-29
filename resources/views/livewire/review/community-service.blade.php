<x-slot:title>Review Pengabdian Masyarakat</x-slot:title>
<x-slot:pageTitle>Review Pengabdian Masyarakat</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola tugas review proposal pengabdian masyarakat yang ditugaskan kepada Anda.</x-slot:pageSubtitle>

<div>
    <x-tabler.alert />
    <!-- Proposals Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Author</th>
                        <th>Bidang Fokus</th>
                        <th>Status Review</th>
                        <th>Rekomendasi</th>
                        <th>Tanggal Dibuat</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->proposals as $proposal)
                        @php
                            $review = $proposal->reviewers->firstWhere('user_id', auth()->id());
                        @endphp
                        <tr wire:key="proposal-{{ $proposal->id }}">
                            <td class="text-wrap">
                                <div class="text-reset fw-bold">{{ $proposal->title }}</div>
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
                                @if ($review?->isCompleted())
                                    <x-tabler.badge color="green" class="fw-normal">
                                        Selesai
                                    </x-tabler.badge>
                                @else
                                    <x-tabler.badge color="yellow" class="fw-normal">
                                        Pending
                                    </x-tabler.badge>
                                @endif
                            </td>
                            <td>
                                @if ($review?->isCompleted())
                                    <span class="badge-outline badge">
                                        @if ($review->recommendation === 'approved')
                                            <span class="text-success">✓ Disetujui</span>
                                        @elseif ($review->recommendation === 'rejected')
                                            <span class="text-danger">✗ Ditolak</span>
                                        @else
                                            <span class="text-warning">↺ Revisi</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-secondary">
                                    {{ $proposal->created_at?->format('d M Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-list">
                                    <a href="{{ route('community-service.proposal.show', $proposal) }}"
                                        class="btn btn-sm btn-outline-primary" title="Lihat Detail" wire:navigate>
                                        <x-lucide-eye class="icon" />
                                        Lihat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center">
                                <div class="mb-3">
                                    <x-lucide-inbox class="text-secondary icon icon-lg" />
                                </div>
                                <p class="text-secondary">Tidak ada proposal yang perlu direview saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
