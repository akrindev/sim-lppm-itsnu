<div>
    @if ($this->canSubmit)
        <button wire:click="submit" class="btn btn-success">
            <x-lucide-send class="icon" />
            Submit Proposal
        </button>
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
            Proposal tidak dapat disubmit
        </div>
    @endif
</div>
