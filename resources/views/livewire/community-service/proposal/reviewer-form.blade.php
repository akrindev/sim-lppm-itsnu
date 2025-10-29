<div>
    @if ($this->allReviews->isNotEmpty() || $this->canReview)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Review Proposal</h3>
            </div>

            @if ($this->canReview && $this->canEditReview)
                <div class="card-options">
                    <button type="button"
                        class="btn btn-sm {{ $this->showForm ? 'btn-outline-secondary' : 'btn-outline-primary' }}"
                        wire:click="toggleForm">
                        <x-lucide-edit-3 class="icon" />
                        {{ $this->showForm ? 'Tutup Form' : 'Edit Review' }}
                    </button>
                </div>
            @elseif ($this->canReview && !$this->canEditReview && !$this->hasReviewed)
                <div class="card-options">
                    <button type="button"
                        class="btn btn-sm {{ $this->showForm ? 'btn-outline-secondary' : 'btn-outline-primary' }}"
                        wire:click="toggleForm">
                        <x-lucide-plus class="icon" />
                        {{ $this->showForm ? 'Tutup Form' : 'Tambah Review' }}
                    </button>
                </div>
            @endif

            @if ($this->canReview && $this->showForm)
                <form wire:submit="submitReview">
                    <div class="card-body">
                        @if ($this->canEditReview)
                            <div class="alert alert-info mb-3" role="alert">
                                <strong>Note:</strong> Review sudah disubmit sebelumnya dan dapat diedit.
                            </div>
                        @endif

                        @if ($this->hasReviewed && $this->myReview->recommendation === 'approved')
                            <div class="alert alert-warning mb-3" role="alert">
                                <strong>Warning:</strong> Review dengan rekomendasi "Disetujui" tidak dapat diedit lagi.
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label" for="reviewNotes">Catatan Review <span
                                    class="text-danger">*</span></label>
                            <textarea wire:model="reviewNotes" id="reviewNotes" class="form-control @error('reviewNotes') is-invalid @enderror"
                                rows="6" placeholder="Masukkan catatan review proposal..." required
                                {{ $this->hasReviewed && $this->myReview->recommendation === 'approved' ? 'disabled' : '' }}></textarea>
                            @error('reviewNotes')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="recommendation">Rekomendasi <span
                                    class="text-danger">*</span></label>
                            <select wire:model="recommendation" id="recommendation"
                                class="form-select @error('recommendation') is-invalid @enderror" required
                                {{ $this->hasReviewed && $this->myReview->recommendation === 'approved' ? 'disabled' : '' }}>
                                <option value="">-- Pilih Rekomendasi --</option>
                                <option value="approved">✓ Disetujui</option>
                                <option value="revision_needed">↻ Butuh Revisi</option>
                                <option value="rejected">✗ Ditolak</option>
                            </select>
                            @error('recommendation')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"
                            {{ $this->hasReviewed && $this->myReview->recommendation === 'approved' ? 'disabled' : '' }}>
                            <x-lucide-send class="icon" />
                            {{ $this->hasReviewed ? 'Update Review' : 'Submit Review' }}
                        </button>
                    </div>
                </form>
                <div class="px-4 pb-4">
                    <hr>
                </div>
            @endif

            <div class="card-body">
                @if ($this->allReviews->isNotEmpty())
                    @foreach ($this->allReviews as $review)
                        <div class="{{ !$loop->last ? 'border-bottom pb-4' : '' }} mb-4">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div>
                                    <strong>{{ $review->user->name }}</strong>
                                    <small class="d-block text-secondary">{{ $review->user->email }}</small>
                                </div>
                                <div>
                                    @if ($review->status === 'completed')
                                        <x-tabler.badge color="green" class="fw-normal">
                                            Selesai
                                        </x-tabler.badge>
                                    @else
                                        <x-tabler.badge color="yellow" class="fw-normal">
                                            Pending
                                        </x-tabler.badge>
                                    @endif
                                </div>
                            </div>

                            @if ($review->recommendation)
                                <div class="mb-2">
                                    <strong>Rekomendasi:</strong>
                                    @if ($review->recommendation === 'approved')
                                        <span class="badge-outline text-success badge">✓ Disetujui</span>
                                        @if (auth()->id() === $review->user_id)
                                            <small class="text-success d-block"><x-lucide-lock class="icon" /> Tidak dapat diedit</small>
                                        @endif
                                    @elseif ($review->recommendation === 'rejected')
                                        <span class="badge-outline text-danger badge">✗ Ditolak</span>
                                    @else
                                        <span class="badge-outline text-warning badge">↻ Butuh Revisi</span>
                                    @endif
                                </div>
                            @endif

                            @if ($review->review_notes)
                                <div>
                                    <strong>Catatan Review:</strong>
                                    <p class="mb-0 text-secondary">{{ $review->review_notes }}</p>
                                </div>
                            @endif

                            <small class="text-secondary">
                                {{ $review->updated_at?->format('d M Y H:i') }}
                            </small>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4 text-secondary">
                        Belum ada review untuk proposal ini.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
