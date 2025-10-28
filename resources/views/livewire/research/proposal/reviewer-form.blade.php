<div>
    @if ($this->canReview)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Formulir Review Proposal</h3>
            </div>
            <form wire:submit="submitReview">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="reviewNotes">Catatan Review <span
                                class="text-danger">*</span></label>
                        <textarea wire:model="reviewNotes" id="reviewNotes" class="form-control @error('reviewNotes') is-invalid @enderror"
                            rows="6" placeholder="Masukkan catatan review proposal..." required></textarea>
                        @error('reviewNotes')
                            <div class="d-block invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="recommendation">Rekomendasi <span
                                class="text-danger">*</span></label>
                        <select wire:model="recommendation" id="recommendation"
                            class="form-select @error('recommendation') is-invalid @enderror" required>
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
                    <button type="submit" class="btn btn-primary">
                        <x-lucide-send class="icon" />
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    @elseif ($this->myReview)
        <div class="alert alert-info" role="alert">
            <strong>✓ Review Selesai</strong><br>
            Anda sudah menyelesaikan review untuk proposal ini.
            @if ($this->myReview->recommendation)
                <br>Rekomendasi: <strong>{{ ucfirst(str_replace('_', ' ', $this->myReview->recommendation)) }}</strong>
            @endif
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            Anda bukan reviewer untuk proposal ini
        </div>
    @endif
</div>
