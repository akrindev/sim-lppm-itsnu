<x-slot:title>Riwayat Review</x-slot:title>
<x-slot:pageTitle>Riwayat Review</x-slot:pageTitle>
<x-slot:pageSubtitle>Daftar proposal yang telah Anda selesai review.</x-slot:pageSubtitle>

<div>
    <div class="card">
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Tanggal Selesai</th>
                        <th>Proposal</th>
                        <th>Jenis</th>
                        <th>Rekomendasi</th>
                        <th class="w-1">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->history as $review)
                        <tr>
                            <td>{{ $review->updated_at->format('d M Y H:i') }}</td>
                            <td>
                                <div class="fw-bold">{{ $review->proposal?->title }}</div>
                                <div class="small text-secondary">{{ $review->proposal?->submitter?->name }}</div>
                            </td>
                            <td>
                                @if($review->proposal?->detailable_type === 'App\Models\Research')
                                    <span class="badge bg-blue-lt">Penelitian</span>
                                @else
                                    <span class="badge bg-green-lt">Pengabdian</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $color = match($review->recommendation) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'revision_needed' => 'warning',
                                        default => 'secondary'
                                    };
                                    $label = match($review->recommendation) {
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'revision_needed' => 'Revisi',
                                        default => $review->recommendation
                                    };
                                @endphp
                                <span class="badge bg-{{ $color }}-lt">{{ $label }}</span>
                            </td>
                            <td>
                                @if($review->proposal)
                                    <a href="{{ $review->proposal->detailable_type === 'App\Models\Research' ? route('research.proposal.show', $review->proposal) : route('community-service.proposal.show', $review->proposal) }}" 
                                        class="btn btn-sm btn-ghost-primary" wire:navigate>
                                        Lihat
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Belum ada riwayat review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($this->history->hasPages())
            <div class="card-footer">
                {{ $this->history->links() }}
            </div>
        @endif
    </div>
</div>
