<div>
    @if ($this->canDecide)
        <div class="alert alert-info" role="alert">
            <x-lucide-clipboard-check class="icon" />
            <div>

                <h4 class="alert-heading">
                    Keputusan Akhir Kepala LPPM
                </h4>
                <div class="alert-description">
                    Semua reviewer telah menyelesaikan review. Silakan berikan keputusan akhir untuk proposal ini.
                </div>
            </div>
        </div>

        <div class="btn-list">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#finalDecisionModal"
                wire:click="$set('decision', 'completed')">
                <x-lucide-check class="icon" />
                Setujui Proposal
            </button>
            {{-- <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#finalDecisionModal"
                wire:click="$set('decision', 'revision_needed')">
                <x-lucide-file-edit class="icon" />
                Minta Perbaikan Usulan
            </button> --}}
        </div>
    @elseif ($this->pendingReviewers->count() > 0)
        <div class="alert alert-warning" role="alert">
            <strong>Menunggu Review:</strong> {{ $this->pendingReviewers->count() }} reviewer belum menyelesaikan review
        </div>
    @else
        {{-- <div class="alert alert-info" role="alert">
            Proposal tidak dapat diputuskan saat ini
        </div> --}}
    @endif

    <!-- Decision Confirmation Modal -->
    @teleport('body')
        <x-tabler.modal id="finalDecisionModal" title="Konfirmasi Keputusan Akhir">
            <x-slot:body>
                <div class="py-3">
                    @if ($decision === 'completed')
                        <div class="mb-3 text-center">
                            <x-lucide-check-circle class="mb-2 text-success icon" style="width: 3rem; height: 3rem;" />
                            <h3>Setujui Proposal?</h3>
                            <div class="text-secondary">
                                Proposal akan disetujui dan statusnya akan berubah menjadi <strong>Selesai</strong>.
                            </div>
                        </div>
                    @else
                        <div class="mb-3 text-center">
                            <x-lucide-file-edit class="mb-2 text-warning icon" style="width: 3rem; height: 3rem;" />
                            <h3>Minta Perbaikan Usulan?</h3>
                            <div class="text-secondary">
                                Proposal akan dikembalikan ke pengusul untuk melakukan perbaikan sesuai dengan catatan
                                yang Anda berikan.
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">
                            Catatan {{ $decision === 'revision_needed' ? '(Wajib)' : '(Opsional)' }}
                        </label>
                        <textarea class="form-control" rows="4" wire:model="notes" placeholder="Tambahkan catatan atau komentar..."></textarea>
                        @if ($decision === 'revision_needed')
                            <small class="form-hint">
                                Jelaskan perbaikan yang diperlukan agar pengusul dapat melakukan revisi dengan tepat.
                            </small>
                        @endif
                    </div>
                </div>
            </x-slot:body>

            <x-slot:footer>
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="w-100 btn btn-white" data-bs-dismiss="modal">
                                Batal
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" wire:click="processDecision"
                                class="w-100 btn {{ $decision === 'completed' ? 'btn-success' : 'btn-warning' }}"
                                data-bs-dismiss="modal">
                                @if ($decision === 'completed')
                                    <x-lucide-check class="icon" />
                                    Ya, Setujui
                                @else
                                    <x-lucide-file-edit class="icon" />
                                    Ya, Minta Perbaikan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport
</div>
