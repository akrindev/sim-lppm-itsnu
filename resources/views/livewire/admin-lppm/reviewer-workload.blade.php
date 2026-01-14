<x-slot:title>Beban Kerja Reviewer</x-slot:title>
<x-slot:pageTitle>Beban Kerja Reviewer</x-slot:pageTitle>
<x-slot:pageSubtitle>Pantau beban kerja dan progres review dari setiap reviewer yang terdaftar.</x-slot:pageSubtitle>

<div>
    <div class="card">
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama Reviewer</th>
                        <th>Fakultas</th>
                        <th class="text-center">Total Tugas</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Selesai</th>
                        <th>Progres</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->reviewers as $reviewer)
                        @php
                            $total = (int) $reviewer->total_assigned;
                            $completed = (int) $reviewer->completed_count;
                            $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
                        @endphp
                        <tr wire:key="reviewer-{{ $reviewer->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2">{{ $reviewer->initials() }}</span>
                                    <div>
                                        <div class="fw-bold">{{ $reviewer->name }}</div>
                                        <div class="text-secondary small">{{ $reviewer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $reviewer->identity?->faculty?->name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-blue-lt">{{ $total }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning-lt">{{ $reviewer->pending_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-lt">{{ $completed }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-xs w-100 me-2">
                                        <div class="progress-bar bg-primary" x-data :style="'width: ' + {{ $percentage }} + '%'"></div>
                                    </div>
                                    <span class="small">{{ $percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada reviewer terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
