<div>
    @if ($this->canSubmit)
        <button type="button" class="w-full btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmSubmitModal">
            <x-lucide-send class="icon" />
            Submit Proposal
        </button>

        @teleport('body')
            <x-tabler.modal id="confirmSubmitModal" title="Konfirmasi Pengajuan Proposal" wire:ignore.self>
                <x-slot:body>
                    <div class="py-4 text-center">
                        <x-lucide-send class="mb-2 text-primary icon" style="width: 3rem; height: 3rem;" />
                        <h3>Konfirmasi Pengajuan Proposal</h3>
                        <div class="text-secondary">
                            Apakah Anda yakin ingin mengajukan proposal ini? Setelah diajukan, proposal tidak dapat diubah.
                        </div>
                    </div>
                </x-slot:body>

                <x-slot:footer>
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="w-100 btn btn-light" data-bs-dismiss="modal">
                                    Batalkan
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" wire:click="submit" class="w-100 btn btn-success"
                                    data-bs-dismiss="modal">
                                    Ya, Ajukan Proposal
                                </button>
                            </div>
                        </div>
                    </div>
                </x-slot:footer>
            </x-tabler.modal>
        @endteleport
    @elseif ($this->pendingMembers->count() > 0)
        <div class="d-inline-block alert alert-warning" role="alert">
            <strong>Menunggu Persetujuan:</strong> {{ $this->pendingMembers->count() }} anggota belum menerima undangan
        </div>
    @elseif ($this->rejectedMembers->count() > 0)
        <div class="d-inline-block alert alert-danger" role="alert">
            <strong>Ada yang Menolak:</strong> {{ $this->rejectedMembers->count() }} anggota menolak undangan. Silahkan
            hapus dan tambah anggota baru.
        </div>
    @else
        <div class="d-inline-block alert alert-info" role="alert">
            Proposal dapat disubmit oleh Author setelah semua anggota menerima undangan.
        </div>
    @endif
</div>
