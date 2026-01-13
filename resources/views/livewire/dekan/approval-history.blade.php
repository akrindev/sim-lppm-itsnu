<x-slot:title>Riwayat Persetujuan</x-slot:title>
<x-slot:pageTitle>Riwayat Persetujuan</x-slot:pageTitle>
<x-slot:pageSubtitle>Daftar proposal yang telah Anda tinjau dan berikan keputusan.</x-slot:pageSubtitle>

<div>
    <div class="card">
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Proposal</th>
                        <th>Keputusan</th>
                        <th>Catatan</th>
                        <th class="w-1">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->history as $log)
                        <tr>
                            <td class="text-nowrap">{{ $log->at->format('d M Y H:i') }}</td>
                            <td>
                                <div class="fw-bold">{{ $log->proposal?->title }}</div>
                                <div class="small text-secondary">
                                    {{ $log->proposal?->submitter?->name }} &middot; {{ $log->proposal?->focusArea?->name ?? '—' }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $color = match($log->status_after) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'need_assignment' => 'warning',
                                        default => 'secondary'
                                    };
                                    $label = match($log->status_after) {
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'need_assignment' => 'Perlu Perbaikan',
                                        default => $log->status_after
                                    };
                                @endphp
                                <span class="badge bg-{{ $color }}-lt">{{ $label }}</span>
                            </td>
                            <td>
                                <div class="small text-secondary text-wrap" style="max-width: 300px;">
                                    {{ $log->notes ?: '—' }}
                                </div>
                            </td>
                            <td>
                                @if($log->proposal)
                                    <a href="{{ $log->proposal->detailable_type === 'App\Models\Research' ? route('research.proposal.show', $log->proposal) : route('community-service.proposal.show', $log->proposal) }}" 
                                        class="btn btn-sm btn-ghost-primary" wire:navigate>
                                        Lihat
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Belum ada riwayat persetujuan.</td>
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
