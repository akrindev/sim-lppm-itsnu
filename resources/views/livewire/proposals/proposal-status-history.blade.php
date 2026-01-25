<div>
    @if ($logs->isEmpty())
        <p class="text-muted text-center py-4">Belum ada riwayat perubahan status.</p>
    @else
        <div class="timeline">
            @foreach ($logs as $log)
                <div class="timeline-item">
                    <div class="timeline-badge bg-info">
                        <x-lucide-refresh-cw class="icon-inline" style="width: 12px; height: 12px;"/>
                    </div>
                    <div class="timeline-content">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div>
                                <span class="badge bg-secondary-lt">{{ $log->status_before?->label() ?? '—' }}</span>
                                <x-lucide-arrow-right class="mx-2 icon text-muted" style="width: 1rem; height: 1rem;" />
                                <span class="badge bg-primary-lt">{{ $log->status_after->label() }}</span>
                            </div>
                            <small class="text-muted">{{ $log->at->format('d M Y H:i') }}</small>
                        </div>
                        <p class="mb-1 text-secondary">
                            Oleh: <strong>{{ $log->user?->name ?? 'Sistem' }}</strong>
                        </p>
                        @if ($log->notes)
                            <div class="mt-2 p-2 bg-light rounded border-start border-3 border-info">
                                <small class="text-muted d-block mb-1">Catatan:</small>
                                <div class="text-secondary small">{{ $log->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
