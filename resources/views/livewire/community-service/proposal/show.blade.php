<x-slot:title>{{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>{{ $proposal->title }}</x-slot:pageTitle>
<x-slot:pageSubtitle>Detail Proposal Pengabdian</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        <a href="{{ route('community-service.proposal.index') }}" class="btn-outline-secondary btn">
            <x-lucide-arrow-left class="icon" />
            Kembali
        </a>
        @if ($proposal->status === 'draft')
            <a href="{{ route('community-service.proposal.edit', $proposal) }}" wire:navigate class="btn btn-primary">
                <x-lucide-pencil class="icon" />
                Edit
            </a>
        @endif
    </div>
</x-slot:pageActions>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="mb-3 card">
            <div class="card-header">
                <h3 class="card-title">Informasi Dasar</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Judul</label>
                        <p class="text-reset">{{ $proposal->title }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <p>
                            @switch($proposal->status)
                                @case('draft')
                                    <x-tabler.badge color="warning">Draft</x-tabler.badge>
                                @break

                                @case('submitted')
                                    <x-tabler.badge color="info">Submitted</x-tabler.badge>
                                @break

                                @case('under_review')
                                    <x-tabler.badge color="info">Under Review</x-tabler.badge>
                                @break

                                @case('approved')
                                    <x-tabler.badge color="success">Approved</x-tabler.badge>
                                @break

                                @case('rejected')
                                    <x-tabler.badge color="danger">Rejected</x-tabler.badge>
                                @break

                                @case('completed')
                                    <x-tabler.badge color="secondary">Completed</x-tabler.badge>
                                @break

                                @default
                                    <x-tabler.badge color="secondary">{{ $proposal->status }}</x-tabler.badge>
                            @endswitch
                        </p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Pelaksana</label>
                        <p class="text-reset">{{ $proposal->submitter?->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <p class="text-reset">{{ $proposal->submitter?->email }}</p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Durasi (Tahun)</label>
                        <p class="text-reset">{{ $proposal->duration_in_years ?? '—' }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ringkasan</label>
                    <p class="text-reset">{{ $proposal->summary ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Classification -->
        <div class="mb-3 card">
            <div class="card-header">
                <h3 class="card-title">Klasifikasi</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Bidang Fokus</label>
                        <p class="text-reset">{{ $proposal->focusArea?->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tema</label>
                        <p class="text-reset">{{ $proposal->theme?->name ?? '—' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Topik</label>
                        <p class="text-reset">{{ $proposal->topic?->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Prioritas Nasional</label>
                        <p class="text-reset">{{ $proposal->nationalPriority?->name ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members -->
        <div class="mb-3 card">
            <div class="card-header">
                <h3 class="card-title">Tim Pengabdi</h3>
            </div>
            <div class="p-0 card-body">
                @if ($proposal->teamMembers->isNotEmpty())
                    <div class="table-responsive">
                        <table class="card-table table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Peran</th>
                                    <th>Tugas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proposal->teamMembers as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->email }}</td>
                                        <td>
                                            @if ($member->pivot->role === 'ketua')
                                                <x-tabler.badge color="primary">Ketua</x-tabler.badge>
                                            @else
                                                <x-tabler.badge color="secondary">Anggota</x-tabler.badge>
                                            @endif
                                        </td>
                                        <td>{{ $member->pivot->tasks }}</td>
                                        <td>
                                            @if ($member->pivot->status === 'accepted')
                                                <x-tabler.badge color="success">Diterima</x-tabler.badge>
                                            @elseif ($member->pivot->status === 'rejected')
                                                <x-tabler.badge color="danger">Ditolak</x-tabler.badge>
                                            @else
                                                <x-tabler.badge color="warning">Menunggu</x-tabler.badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-4 text-secondary text-center">
                        Belum ada anggota tim ditambahkan
                    </div>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timeline</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Dibuat</label>
                        <p class="text-reset">{{ $proposal->created_at?->format('d M Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Diubah</label>
                        <p class="text-reset">{{ $proposal->updated_at?->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="mb-3 card">
            <div class="card-header">
                <h3 class="card-title">Status Proposal</h3>
            </div>
            <div class="text-center card-body">
                <div class="mb-3">
                    @switch($proposal->status)
                        @case('draft')
                            <x-tabler.badge color="warning">Draft</x-tabler.badge>
                        @break

                        @case('submitted')
                            <x-tabler.badge color="info">Submitted</x-tabler.badge>
                        @break

                        @case('under_review')
                            <x-tabler.badge color="info">Under Review</x-tabler.badge>
                        @break

                        @case('approved')
                            <x-tabler.badge color="success">Approved</x-tabler.badge>
                        @break

                        @case('rejected')
                            <x-tabler.badge color="danger">Rejected</x-tabler.badge>
                        @break

                        @case('completed')
                            <x-tabler.badge color="secondary">Completed</x-tabler.badge>
                        @break

                        @default
                            <x-tabler.badge color="secondary">{{ $proposal->status }}</x-tabler.badge>
                    @endswitch
                </div>
                <p class="text-secondary text-sm">
                    @switch($proposal->status)
                        @case('draft')
                            Proposal masih dalam tahap penyusunan. Anda dapat mengedit atau mengirimkan proposal ini.
                        @break

                        @case('submitted')
                            Proposal telah diajukan dan sedang menunggu review dari tim LPPM.
                        @break

                        @case('under_review')
                            Proposal sedang dalam proses review. Silahkan tunggu hasil evaluasi.
                        @break

                        @case('approved')
                            Selamat! Proposal Anda telah disetujui. Silahkan mulai melaksanakan kegiatan.
                        @break

                        @case('rejected')
                            Sayangnya proposal Anda ditolak. Silahkan perbaiki dan ajukan kembali.
                        @break

                        @case('completed')
                            Proposal ini telah selesai dilaksanakan.
                        @break

                        @default
                            Status proposal tidak diketahui.
                    @endswitch
                </p>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi</h3>
            </div>
            <div class="gap-2 d-grid card-body">
                @if ($proposal->status === 'draft')
                    <button type="button" class="btn btn-primary">
                        <x-lucide-send class="icon" />
                        Kirim Proposal
                    </button>
                @endif

                @if (in_array($proposal->status, ['submitted', 'under_review', 'rejected']))
                    <a href="#" class="btn btn-info">
                        <x-lucide-eye class="icon" />
                        Lihat Review
                    </a>
                @endif

                @if ($proposal->status === 'approved')
                    <a href="#" class="btn btn-success">
                        <x-lucide-file-text class="icon" />
                        Laporan Progress
                    </a>
                @endif

                <button type="button" class="btn-outline-danger btn" wire:click="delete"
                    wire:confirm="Yakin ingin menghapus proposal ini?">
                    <x-lucide-trash-2 class="icon" />
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
