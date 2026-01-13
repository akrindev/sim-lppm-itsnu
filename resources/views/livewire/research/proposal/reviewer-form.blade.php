<div>
    @if ($this->allReviews->isNotEmpty() || $this->canReview)
        <div class="card card-md mb-3 border-primary border-2 shadow-sm" id="review-section">
            <div class="card-status-top bg-primary"></div>
            <div class="card-header bg-primary-lt">
                <div>
                    <h3 class="card-title text-primary">
                        <x-lucide-message-square class="icon me-2" />
                        Panel Reviewer
                        @if($this->reviewRound > 1)
                            <span class="badge bg-purple-lt ms-2">Putaran {{ $this->reviewRound }}</span>
                        @endif
                    </h3>
                    <div class="text-secondary small mt-1">Silakan berikan penilaian dan rekomendasi Anda untuk proposal ini.</div>
                </div>

                <div class="card-actions">
                    @if ($this->canReview)
                        @if ($this->needsAction)
                            <button type="button"
                                class="btn {{ $this->showForm ? 'btn-secondary' : 'btn-primary' }} btn-pill shadow-sm"
                                wire:click="toggleForm">
                                @if($this->showForm)
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
                    @endif
                </div>
            </div>

            @if($this->canReview && $this->myReview)
                <div class="card-body bg-light-lt py-3 border-bottom">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <div class="small text-secondary">Status Anda:</div>
                            <x-tabler.badge :color="$this->myReview->status->color()">
                                <x-dynamic-component :component="'lucide-' . $this->myReview->status->icon()" class="icon icon-inline me-1" />
                                {{ $this->myReview->status->label() }}
                            </x-tabler.badge>
                        </div>
                        @if($this->deadline)
                            <div class="col-auto border-start ps-3">
                                <div class="small text-secondary">Batas Waktu:</div>
                                <div class="fw-bold {{ $this->isOverdue ? 'text-danger' : 'text-dark' }}">
                                    <x-lucide-calendar class="icon me-1" />
                                    {{ $this->deadline->format('d M Y') }}
                                    @if($this->isOverdue)
                                        <span class="badge bg-danger-lt ms-1">Terlambat!</span>
                                    @elseif($this->daysRemaining !== null)
                                        <span class="small text-muted font-normal ms-1">({{ $this->daysRemaining }} hari lagi)</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($this->canReview && $this->showForm)
                <div class="card-body border-bottom">
                    <form wire:submit="submitReview">
                        @if ($this->needsReReview)
                            <div class="alert alert-important alert-warning shadow-sm mb-4" role="alert">
                                <div class="d-flex">
                                    <div><x-lucide-refresh-cw class="alert-icon me-2" /></div>
                                    <div>
                                        <h4 class="alert-title">Review Ulang Dibutuhkan</h4>
                                        <div class="text-secondary">Proposal ini telah direvisi oleh pengusul. Silakan periksa perubahan dan berikan penilaian baru.</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label h4 fw-bold mb-2" for="reviewNotes">
                                Catatan Review <span class="text-danger">*</span>
                            </label>
                            <div class="text-secondary small mb-2">Berikan feedback yang konstruktif dan jelas untuk pengusul. Minimal 10 karakter.</div>
                            <textarea wire:model="reviewNotes" id="reviewNotes" 
                                class="form-control @error('reviewNotes') is-invalid @enderror shadow-sm"
                                rows="8" placeholder="Masukkan catatan detail review proposal..." required
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
                                @foreach([
                                    'approved' => ['label' => 'Disetujui', 'color' => 'success', 'icon' => 'check-circle'],
                                    'revision_needed' => ['label' => 'Butuh Revisi', 'color' => 'warning', 'icon' => 'refresh-cw'],
                                    'rejected' => ['label' => 'Ditolak', 'color' => 'danger', 'icon' => 'x-circle']
                                ] as $value => $meta)
                                    <div class="col-md-4">
                                        <label class="form-selectgroup-item w-100">
                                            <input type="radio" wire:model="recommendation" value="{{ $value }}" 
                                                class="form-selectgroup-input"
                                                {{ $this->hasReviewed && $this->myReview->recommendation === 'approved' ? 'disabled' : '' }}>
                                            <div class="form-selectgroup-label d-flex align-items-center p-3 border-2">
                                                <x-dynamic-component :component="'lucide-' . $meta['icon']" class="icon me-3 text-{{ $meta['color'] }}" />
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
                                <button type="button" class="btn btn-link link-secondary" wire:click="toggleForm">Batal</button>
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

            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Riwayat & Daftar Review</h4>
                    <span class="badge bg-blue-lt">{{ $this->allReviews->count() }} Review</span>
                </div>

                @if ($this->allReviews->isNotEmpty())
                    <div class="divide-y">
                        @foreach ($this->allReviews as $review)
                            <div class="py-3">
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
                                                @if($review->round > 1)
                                                    <div class="small text-muted">Putaran {{ $review->round }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($review->recommendation)
                                            <div class="my-2 p-2 rounded-2 {{ 
                                                $review->recommendation === 'approved' ? 'bg-success-lt' : (
                                                $review->recommendation === 'rejected' ? 'bg-danger-lt' : 'bg-warning-lt') 
                                            }} border">
                                                <div class="d-flex align-items-center small fw-bold mb-1">
                                                    @if($review->recommendation === 'approved')
                                                        <x-lucide-check-circle class="icon me-1 text-success" />
                                                        Rekomendasi: Disetujui
                                                    @elseif($review->recommendation === 'rejected')
                                                        <x-lucide-x-circle class="icon me-1 text-danger" />
                                                        Rekomendasi: Ditolak
                                                    @else
                                                        <x-lucide-refresh-cw class="icon me-1 text-warning" />
                                                        Rekomendasi: Perlu Revisi
                                                    @endif
                                                </div>
                                                @if ($review->review_notes)
                                                    <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $review->review_notes }}</p>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="d-flex align-items-center justify-content-between mt-2">
                                            <small class="text-secondary">
                                                <x-lucide-clock class="icon icon-inline me-1" />
                                                {{ $review->updated_at?->diffForHumans() ?? '-' }}
                                            </small>
                                            @if($review->completed_at)
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
                    <div class="py-5 text-center bg-light-lt rounded-3 border">
                        <x-lucide-message-square class="icon icon-lg text-muted mb-2" />
                        <div class="text-secondary">Belum ada review untuk proposal ini.</div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
