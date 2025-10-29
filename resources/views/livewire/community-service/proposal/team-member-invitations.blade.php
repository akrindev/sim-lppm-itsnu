<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            Status Anggota Tim
            @if ($this->allAccepted)
                <span class="bg-success ms-2 badge">Semua Diterima</span>
            @else
                <span class="bg-warning ms-2 badge">{{ $this->pendingInvitations->count() }} Menunggu</span>
            @endif
        </h3>
    </div>

    @if ($this->acceptedMembers->count() > 0)
        <div class="card-header">
            <h4 class="text-success card-title">Anggota yang Diterima</h4>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <tbody>
                    @foreach ($this->acceptedMembers as $member)
                        <tr>
                            <td class="w-1">
                                <span class="bg-success badge"></span>
                            </td>
                            <td>
                                <strong>{{ $member->name }}</strong><br>
                                <small class="text-muted">{{ $member->email }}</small>
                            </td>
                            <td class="text-end">
                                <span class="bg-success badge">✓ Diterima</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($this->pendingInvitations->count() > 0)
        <div class="card-header">
            <h4 class="text-warning card-title">Anggota yang Menunggu Persetujuan</h4>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <tbody>
                    @foreach ($this->pendingInvitations as $member)
                        <tr>
                            <td class="w-1">
                                <span class="bg-warning badge"></span>
                            </td>
                            <td>
                                <strong>{{ $member->name }}</strong><br>
                                <small class="text-muted">{{ $member->email }}</small>
                            </td>
                            <td class="text-end">
                                <span class="bg-warning badge">⏳ Menunggu</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Show action buttons only for current user who is invited --}}
        @php
            $currentUserInvited = $this->pendingInvitations->where('id', auth()->id())->first();
        @endphp

        @if ($currentUserInvited)
            <div class="card-footer">
                <div class="btn-list">
                    <button wire:click="acceptInvitation" class="btn btn-success">
                        <x-lucide-check class="icon" />
                        Terima Undangan
                    </button>
                    <button wire:click="rejectInvitation" class="btn btn-danger">
                        <x-lucide-x class="icon" />
                        Tolak Undangan
                    </button>
                </div>
            </div>
        @endif
    @endif

    @if ($this->rejectedMembers->count() > 0)
        <div class="card-header">
            <h4 class="text-danger card-title">Anggota yang Menolak</h4>
        </div>
        <div class="alert alert-danger" role="alert">
            <strong>Perhatian:</strong> Beberapa anggota telah menolak undangan. Silahkan hapus dan tambah anggota baru.
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <tbody>
                    @foreach ($this->rejectedMembers as $member)
                        <tr>
                            <td class="w-1">
                                <span class="bg-danger badge"></span>
                            </td>
                            <td>
                                <strong>{{ $member->name }}</strong><br>
                                <small class="text-muted">{{ $member->email }}</small>
                            </td>
                            <td class="text-end">
                                <span class="bg-danger badge">✗ Ditolak</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
