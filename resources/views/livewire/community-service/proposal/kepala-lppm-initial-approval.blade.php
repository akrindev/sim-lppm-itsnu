<div>
    @if ($this->canApprove)
        <div class="alert alert-info" role="alert">
            <h4 class="alert-title">
                <x-lucide-info class="icon" />
                Persetujuan Awal Kepala LPPM
            </h4>
            <div class="text-secondary">
                Proposal telah disetujui oleh Dekan. Silakan tinjau dan setujui untuk melanjutkan ke tahap penugasan reviewer.
            </div>
        </div>

        <div class="btn-list">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#initialApprovalModal">
                <x-lucide-check-circle class="icon" />
                Setujui & Lanjutkan ke Reviewer
            </button>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            Proposal belum dapat disetujui. Pastikan Dekan telah menyetujui proposal terlebih dahulu.
        </div>
    @endif

    <!-- Approval Confirmation Modal -->
    <div style="display: none;">
        <x-tabler.modal id="initialApprovalModal" title="Konfirmasi Persetujuan Awal">
            <x-slot:body>
                <div class="py-3 text-center">
                    <x-lucide-check-circle class="mb-3 text-primary icon" style="width: 4rem; height: 4rem;" />
                    <h3>Setujui Proposal?</h3>
                    <div class="mt-2 text-secondary">
                        Dengan menyetujui proposal ini, Admin LPPM akan dapat menugaskan reviewer untuk melakukan penilaian.
                    </div>
                    <div class="mt-3 alert alert-info">
                        <strong>Catatan:</strong> Setelah reviewer selesai melakukan review, Anda akan diminta untuk memberikan keputusan akhir.
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
                            <button type="button" wire:click="approve" class="w-100 btn btn-primary" data-bs-dismiss="modal">
                                <x-lucide-check-circle class="icon" />
                                Ya, Setujui
                            </button>
                        </div>
                    </div>
                </div>
            </x-slot:footer>
        </x-tabler.modal>
    </div>
</div>
