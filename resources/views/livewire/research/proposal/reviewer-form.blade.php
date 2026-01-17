<div>
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible mb-3" role="alert">
            <div class="d-flex">
                <x-lucide-check-circle class="alert-icon me-2" />
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    @if ($this->allReviews->isNotEmpty() || $this->canReview)
        {{-- Card 1: Status Reviewer Saat Ini --}}
        <div class="card card-md mb-3 shadow-sm border-0">
            <div class="card-header">
                <h3 class="card-title">
                    <x-lucide-users class="icon me-2" />
                    Status Reviewer Saat Ini
                </h3>
                <div class="card-actions">
                    <span class="badge bg-blue-lt">{{ $this->allReviews->count() }} Reviewer</span>
                </div>
            </div>
            <div class="card-body p-2">
                @if ($this->allReviews->isNotEmpty())
                    <div class="divide-y">
                        @foreach ($this->allReviews as $review)
                            <div class="py-2 px-2">
                                <div class="row align-items-start g-3">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm bg-blue-lt fw-bold">{{ substr($review->user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div>
                                                <div class="fw-bold">{{ $review->user->name }}</div>
                                                <div class="text-secondary small">{{ $review->user->email }}</div>
                                            </div>
                                            <div class="text-end">
                                                <x-tabler.badge :color="$review->status->color()" class="mb-1">
                                                    {{ $review->status->label() }}
                                                </x-tabler.badge>
                                                @if ($review->round > 1)
                                                    <span class="badge bg-purple-lt" data-bs-toggle="tooltip" data-bs-placement="top" title="Siklus review ke-{{ $review->round }}. #1 = review awal, #2+ = review ulang setelah revisi">#{{ $review->round }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($review->recommendation)
                                            <div class="rounded-2 {{ $review->recommendation === 'approved'
                                                    ? 'bg-success-lt'
                                                    : ($review->recommendation === 'rejected'
                                                        ? 'bg-danger-lt'
                                                        : 'bg-warning-lt') }} my-2 p-2">
                                                <div class="d-flex align-items-center small fw-bold mb-1">
                                                    @if ($review->recommendation === 'approved')
                                                        <x-lucide-check-circle class="icon text-success me-1" />
                                                        Rekomendasi: Disetujui
                                                    @elseif($review->recommendation === 'rejected')
                                                        <x-lucide-x-circle class="icon text-danger me-1" />
                                                        Rekomendasi: Ditolak
                                                    @else
                                                        <x-lucide-refresh-cw class="icon text-warning me-1" />
                                                        Rekomendasi: Perlu Revisi
                                                    @endif
                                                </div>
                                                @if ($review->review_notes)
                                                    <p class="text-body small mb-0" style="white-space: pre-line;">
                                                        {{ $review->review_notes }}</p>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="d-flex align-items-center justify-content-between mt-2">
                                            <small class="text-secondary">
                                                <x-lucide-clock class="icon icon-inline me-1" />
                                                {{ $review->updated_at?->diffForHumans() ?? '-' }}
                                            </small>
                                            @if ($review->completed_at)
                                                <small class="text-muted italic">
                                                    Diselesaikan pada: {{ $review->completed_at->format('d M Y H:i') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-surface-secondary rounded-3 py-5 text-center">
                        <x-lucide-users class="icon icon-lg text-muted mb-2" />
                        <div class="text-secondary">Belum ada reviewer yang ditugaskan.</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 2: Riwayat & Daftar Review --}}
        @if ($this->allReviewLogs->isNotEmpty())
            <div class="card card-md mb-3 shadow-sm border-0">
                <div class="card-header">
                    <h3 class="card-title">
                        <x-lucide-history class="icon me-2" />
                        Riwayat & Daftar Review
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="accordion" id="reviewHistoryAccordion">
                        @foreach ($this->allReviewLogs as $round => $logs)
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#historyRound{{ $round }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                        <x-lucide-clipboard-list class="icon me-2" />
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Siklus review. #1 = review awal, #2+ = review ulang setelah revisi">#{{ $round }}</span>
                                        <span class="badge bg-secondary-lt ms-2">{{ $logs->count() }} review</span>
                                        @if ($round == $this->reviewRound)
                                            <span class="badge bg-primary-lt ms-1">Saat ini</span>
                                        @endif
                                    </button>
                                </h2>
                                <div id="historyRound{{ $round }}" 
                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                    data-bs-parent="#reviewHistoryAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="divide-y">
                                            @foreach ($logs as $log)
                                                <div class="p-3">
                                                    <div class="row align-items-start g-3">
                                                        <div class="col-auto">
                                                            <span class="avatar avatar-sm bg-blue-lt fw-bold">
                                                                {{ substr($log->user?->name ?? 'R', 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div class="col">
                                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                                <div>
                                                                    <div class="fw-bold">{{ $log->user?->name ?? 'Reviewer' }}</div>
                                                                    <div class="text-secondary small">{{ $log->user?->email }}</div>
                                                                </div>
                                                                <div class="text-end">
                                                                    <span class="badge bg-{{ $log->recommendation_color }}-lt">
                                                                        {{ $log->recommendation_label }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            @if ($log->review_notes)
                                                                <div class="rounded-2 bg-body-tertiary my-2 p-2">
                                                                    <p class="text-body small mb-0" style="white-space: pre-line;">
                                                                        {{ $log->review_notes }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                            <div class="d-flex align-items-center justify-content-between mt-2">
                                                                <small class="text-secondary">
                                                                    <x-lucide-clock class="icon icon-inline me-1" />
                                                                    {{ $log->completed_at?->diffForHumans() ?? '-' }}
                                                                </small>
                                                                @if ($log->completed_at)
                                                                    <small class="text-muted italic">
                                                                        {{ $log->completed_at->format('d M Y H:i') }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Card 3: Panel Reviewer (Form) - At the bottom --}}
        @if ($this->canReview)
            <div class="card card-md mb-3 shadow-sm border-0" id="review-section">
                <div class="card-status-top bg-primary"></div>
                <div class="card-header bg-primary-lt">
                    <div>
                        <h3 class="card-title text-primary">
                            <x-lucide-edit-3 class="icon me-2" />
                            Panel Reviewer
                            @if ($this->reviewRound > 1)
                                <span class="badge bg-purple-lt ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Siklus review ke-{{ $this->reviewRound }}. #1 = review awal, #2+ = review ulang setelah revisi">#{{ $this->reviewRound }}</span>
                            @endif
                        </h3>
                        <div class="text-secondary small mt-1">Silakan berikan penilaian dan rekomendasi Anda untuk proposal ini.</div>
                    </div>

                    <div class="card-actions">
                        @if ($this->needsAction)
                            <button type="button"
                                class="btn {{ $this->showForm ? 'btn-secondary' : 'btn-primary' }} btn-pill shadow-sm"
                                wire:click="toggleForm">
                                @if ($this->showForm)
                                    <x-lucide-x class="icon me-1" />
                                    Tutup Form
                                @else
                                    <x-lucide-play-circle class="icon me-1" />
                                    Mulai Review
                                @endif
                            </button>
                        @elseif ($this->canEditReview)
                            <button type="button"
                                class="btn {{ $this->showForm ? 'btn-outline-secondary' : 'btn-outline-primary' }} btn-sm"
                                wire:click="toggleForm">
                                <x-lucide-edit-3 class="icon me-1" />
                                {{ $this->showForm ? 'Tutup Form' : 'Ubah Review' }}
                            </button>
                        @endif
                    </div>
                </div>

                @if ($this->myReview)
                    <div class="card-body bg-surface-secondary py-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <div class="small text-secondary">Status Anda:</div>
                                <x-tabler.badge :color="$this->myReview->status->color()">
                                    <x-dynamic-component :component="'lucide-' . $this->myReview->status->icon()" class="icon icon-inline me-1" />
                                    {{ $this->myReview->status->label() }}
                                </x-tabler.badge>
                            </div>
                            @if ($this->deadline)
                                <div class="col-auto ps-3" style="border-left: 1px solid var(--tblr-border-color);">
                                    <div class="small text-secondary">Batas Waktu:</div>
                                    <div class="fw-bold {{ $this->isOverdue ? 'text-danger' : 'text-body' }}">
                                        <x-lucide-calendar class="icon me-1" />
                                        {{ $this->deadline->format('d M Y') }}
                                        @if ($this->isOverdue)
                                            <span class="badge bg-danger-lt ms-1">Terlambat!</span>
                                        @elseif($this->daysRemaining !== null)
                                            <span class="small text-muted ms-1 font-normal">({{ $this->daysRemaining }} hari lagi)</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($this->showForm)
                    <div class="card-body">
                        <form wire:submit="submitReview">
                            @if ($this->needsReReview)
                                <div class="alert alert-important alert-warning mb-4 shadow-sm" role="alert">
                                    <div class="d-flex">
                                        <div><x-lucide-refresh-cw class="alert-icon me-2" /></div>
                                        <div>
                                            <h4 class="alert-title">Review Ulang Dibutuhkan</h4>
                                            <div class="text-secondary">Proposal ini telah direvisi oleh pengusul. Silakan
                                                periksa perubahan dan berikan penilaian baru.</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Show previous round reviews --}}
                                @if ($this->previousRoundLogs->isNotEmpty())
                                    <div class="mb-4">
                                        <div class="accordion" id="previousReviewsAccordion">
                                            @foreach ($this->previousRoundLogs as $index => $log)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#round{{ $log->round }}"
                                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                                            <x-lucide-history class="icon me-2" />
                                                            Review #{{ $log->round }}
                                                            <span class="badge bg-{{ $log->recommendation_color }}-lt ms-2">
                                                                {{ $log->recommendation_label }}
                                                            </span>
                                                            <span class="text-muted small ms-2">
                                                                {{ $log->completed_at?->format('d M Y H:i') }}
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="round{{ $log->round }}" 
                                                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                                        data-bs-parent="#previousReviewsAccordion">
                                                        <div class="accordion-body">
                                                            <div class="mb-2">
                                                                <strong class="small text-muted">Catatan Review Anda:</strong>
                                                            </div>
                                                            <p class="mb-0" style="white-space: pre-line;">{{ $log->review_notes }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <div class="mb-4">
                                <label class="form-label h4 fw-bold mb-2" for="reviewNotes">
                                    Catatan Review <span class="text-danger">*</span>
                                </label>
                                <div class="text-secondary small mb-2">Berikan feedback yang konstruktif dan jelas untuk
                                    pengusul. Minimal 10 karakter.</div>
                                <textarea wire:model="reviewNotes" id="reviewNotes"
                                    class="form-control @error('reviewNotes') is-invalid @enderror shadow-sm" rows="8"
                                    placeholder="Masukkan catatan detail review proposal..." required
                                    {{ $this->hasReviewed && $this->myReview->recommendation === 'approved' ? 'disabled' : '' }}></textarea>
                                @error('reviewNotes')
                                    <div class="d-block invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label h4 fw-bold mb-2" for="recommendation">
                                    Rekomendasi Keputusan <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    @foreach ([
                                        'approved' => ['label' => 'Disetujui', 'color' => 'success', 'icon' => 'check-circle'],
                                        'revision_needed' => ['label' => 'Butuh Revisi', 'color' => 'warning', 'icon' => 'refresh-cw'],
                                        'rejected' => ['label' => 'Ditolak', 'color' => 'danger', 'icon' => 'x-circle'],
                                    ] as $value => $meta)
                                        <div class="col-md-4">
                                            <label class="form-selectgroup-item w-100">
                                                <input type="radio" wire:model="recommendation"
                                                    value="{{ $value }}" class="form-selectgroup-input"
                                                    {{ $this->hasReviewed && $this->myReview->recommendation === 'approved' ? 'disabled' : '' }}>
                                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <x-dynamic-component :component="'lucide-' . $meta['icon']"
                                                        class="icon text-{{ $meta['color'] }} me-3" />
                                                    <div class="text-start">
                                                        <div class="font-weight-medium">{{ $meta['label'] }}</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('recommendation')
                                    <div class="d-block invalid-feedback mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-4">
                                <div class="text-muted small">
                                    <x-lucide-info class="icon me-1" />
                                    Review Anda akan dapat dilihat oleh Admin dan Kepala LPPM.
                                </div>
                                <div class="btn-list">
                                    <button type="button" class="btn btn-link link-secondary"
                                        wire:click="toggleForm">Batal</button>
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm"
                                        wire:loading.attr="disabled"
                                        {{ $this->hasReviewed && $this->myReview->recommendation === 'approved' ? 'disabled' : '' }}>
                                        <span wire:loading class="spinner-border spinner-border-sm me-2"></span>
                                        <x-lucide-send class="icon me-1" wire:loading.remove />
                                        {{ $this->hasReviewed ? 'Simpan Perubahan' : 'Kirim Review Sekarang' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>
