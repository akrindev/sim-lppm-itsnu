<x-slot:title>{{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>{{ $proposal->title }}</x-slot:pageTitle>
<x-slot:pageSubtitle>{{ __('Detail Proposal Penelitian') }}</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        <a href="{{ route('research.proposal.index') }}" class="btn-outline-secondary btn">
            <x-lucide-arrow-left class="icon" />
            {{ __('Kembali') }}
        </a>
        @if ($proposal->status === 'draft')
            <a href="#" class="btn btn-primary">
                <x-lucide-pencil class="icon" />
                {{ __('Edit') }}
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
                <h3 class="card-title">{{ __('Informasi Dasar') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Judul') }}</label>
                        <p class="text-reset">{{ $proposal->title }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Status') }}</label>
                        <p>
                            <x-tabler.badge :color="$proposal->status" class="fw-normal">
                                {{ __('Status: :status', ['status' => ucfirst($proposal->status)]) }}
                            </x-tabler.badge>
                        </p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Peneliti') }}</label>
                        <p class="text-reset">{{ $proposal->submitter?->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Email') }}</label>
                        <p class="text-reset">{{ $proposal->submitter?->email }}</p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Skema Penelitian') }}</label>
                        <p class="text-reset">{{ $proposal->researchScheme?->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Durasi (Tahun)') }}</label>
                        <p class="text-reset">{{ $proposal->duration_in_years ?? '—' }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Ringkasan') }}</label>
                    <p class="text-reset">{{ $proposal->summary ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Classification -->
        <div class="mb-3 card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Klasifikasi') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Bidang Fokus') }}</label>
                        <p class="text-reset">{{ $proposal->focusArea?->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Tema') }}</label>
                        <p class="text-reset">{{ $proposal->theme?->name ?? '—' }}</p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Topik') }}</label>
                        <p class="text-reset">{{ $proposal->topic?->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Prioritas Nasional') }}</label>
                        <p class="text-reset">{{ $proposal->nationalPriority?->name ?? '—' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Nilai SBK') }}</label>
                        <p class="text-reset">{{ number_format($proposal->sbk_value, 2) ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Timeline') }}</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Dibuat') }}</label>
                        <p class="text-reset">{{ $proposal->created_at?->format('d M Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Diubah') }}</label>
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
                <h3 class="card-title">{{ __('Status Proposal') }}</h3>
            </div>
            <div class="text-center card-body">
                <div class="mb-3">
                    <x-tabler.badge :color="$proposal->status" class="fw-normal">
                        {{ __('Status: :status', ['status' => ucfirst($proposal->status)]) }}
                    </x-tabler.badge>
                </div>
                <p class="text-secondary text-sm">
                    @switch($proposal->status)
                        @case('draft')
                            {{ __('Proposal masih dalam tahap penyusunan. Anda dapat mengedit atau mengirimkan proposal ini.') }}
                        @break

                        @case('submitted')
                            {{ __('Proposal telah diajukan dan sedang menunggu review dari tim LPPM.') }}
                        @break

                        @case('under_review')
                            {{ __('Proposal sedang dalam proses review. Silahkan tunggu hasil evaluasi.') }}
                        @break

                        @case('approved')
                            {{ __('Selamat! Proposal Anda telah disetujui. Silahkan mulai melaksanakan kegiatan.') }}
                        @break

                        @case('rejected')
                            {{ __('Sayangnya proposal Anda ditolak. Silahkan perbaiki dan ajukan kembali.') }}
                        @break

                        @case('completed')
                            {{ __('Proposal ini telah selesai dilaksanakan.') }}
                        @break

                        @default
                            {{ __('Status proposal tidak diketahui.') }}
                    @endswitch
                </p>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Aksi') }}</h3>
            </div>
            <div class="gap-2 d-grid card-body">
                @if ($proposal->status === 'draft')
                    <button type="button" class="btn btn-primary">
                        <x-lucide-send class="icon" />
                        {{ __('Kirim Proposal') }}
                    </button>
                @endif

                @if (in_array($proposal->status, ['submitted', 'under_review', 'rejected']))
                    <a href="#" class="btn btn-info">
                        <x-lucide-eye class="icon" />
                        {{ __('Lihat Review') }}
                    </a>
                @endif

                @if ($proposal->status === 'approved')
                    <a href="#" class="btn btn-success">
                        <x-lucide-file-text class="icon" />
                        {{ __('Laporan Progress') }}
                    </a>
                @endif

                <button type="button" class="btn-outline-danger btn">
                    <x-lucide-trash-2 class="icon" />
                    {{ __('Hapus') }}
                </button>
            </div>
        </div>
    </div>
</div>
