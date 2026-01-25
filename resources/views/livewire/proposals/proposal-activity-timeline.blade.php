<div>
    @if ($activities->isEmpty())
        <p class="text-muted text-center py-4">Belum ada riwayat perubahan data.</p>
    @else
        <div class="timeline">
            @foreach ($activities as $activity)
                <div class="timeline-item">
                    <div class="timeline-badge {{ $activity->activity_type === 'created' ? 'bg-success' : ($activity->activity_type === 'deleted' ? 'bg-danger' : 'bg-primary') }}">
                        @if($activity->activity_type === 'created')
                            <x-lucide-plus class="icon-inline" style="width: 12px; height: 12px;"/>
                        @elseif($activity->activity_type === 'updated')
                            <x-lucide-pencil class="icon-inline" style="width: 12px; height: 12px;"/>
                        @elseif($activity->activity_type === 'deleted')
                            <x-lucide-trash class="icon-inline" style="width: 12px; height: 12px;"/>
                        @else
                            <x-lucide-history class="icon-inline" style="width: 12px; height: 12px;"/>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>{{ $activity->description }}</strong>
                                <span class="text-muted ms-2">oleh {{ $activity->user?->name ?? 'Sistem' }}</span>
                            </div>
                            <small class="text-muted">{{ $activity->created_at->format('d M Y H:i') }}</small>
                        </div>

                        @if($activity->changes && count($activity->changes) > 0)
                            <div class="mt-2 table-responsive">
                                <table class="table table-sm table-bordered mb-0" style="font-size: 0.85rem;">
                                    <thead>
                                        <tr class="bg-light">
                                            <th style="width: 25%;">Field</th>
                                            <th style="width: 37.5%;">Sebelumnya</th>
                                            <th style="width: 37.5%;">Sesudah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activity->changes as $field => $change)
                                            <tr>
                                                <td class="fw-bold">{{ Str::title(str_replace('_', ' ', $field)) }}</td>
                                                <td class="text-danger text-wrap">{{ is_array($change['old']) ? json_encode($change['old']) : (Str::limit($change['old'] ?? '-', 100)) }}</td>
                                                <td class="text-success text-wrap">{{ is_array($change['new']) ? json_encode($change['new']) : (Str::limit($change['new'] ?? '-', 100)) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
