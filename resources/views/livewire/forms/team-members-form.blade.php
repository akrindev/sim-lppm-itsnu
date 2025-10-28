<div>
    <!-- Members List -->
    @if (!empty($membersList))
        <div class="mb-4">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>NAMA / NIDN</th>
                            <th>Tugas</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($membersList as $index => $member)
                            <tr wire:key="member-{{ $index }}">
                                <td class="align-middle">
                                    {{ $member['name'] }}<br />
                                    <small class="text-muted"><code>{{ $member['nidn'] }}</code></small>
                                </td>
                                <td class="align-middle">{{ $member['tugas'] }}</td>
                                <td class="align-middle">
                                    @if (($member['status'] ?? 'pending') === 'accepted')
                                        <x-tabler.badge color="success">Diterima</x-tabler.badge>
                                    @elseif (($member['status'] ?? 'pending') === 'rejected')
                                        <x-tabler.badge color="danger">Ditolak</x-tabler.badge>
                                    @else
                                        <x-tabler.badge color="warning">Menunggu</x-tabler.badge>
                                    @endif
                                </td>
                                <td class="text-end align-middle">
                                    <button type="button" wire:click="removeMember({{ $index }})"
                                        class="btn-outline-danger btn btn-sm" title="Hapus">
                                        <x-lucide-trash-2 class="icon" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Add Button -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-member">
        <x-lucide-plus class="icon" />
        Tambah Anggota
    </button>

    @error('members')
        <div class="d-block mt-2 text-danger">{{ $message }}</div>
    @enderror

    <!-- Add Member Modal -->
    @teleport('body')
        <x-tabler.modal id="modal-add-member" :title="$modalTitle" :component-id="$this->getId()" on-show="resetMemberForm">
            <x-slot:body>
                <div class="mb-3">
                    <label class="form-label" for="member_nidn">NIDN / NIP <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="member_nidn" type="text"
                            class="form-control @error('member_nidn') is-invalid @enderror" wire:model.live="member_nidn"
                            placeholder="Masukkan NIDN atau NIP anggota">
                        <button class="btn-outline-primary btn" type="button" wire:click="checkMember" id="button-addon2">
                            <x-lucide-search class="icon" />
                            Cek
                        </button>
                    </div>
                    @error('member_nidn')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if ($memberFound && $foundMember)
                    <div class="mb-3 alert alert-success">
                        <div class="mb-2">
                            <strong>Anggota Ditemukan:</strong>
                        </div>
                        <div class="small">
                            <div><strong>Nama:</strong> {{ $foundMember['name'] }}</div>
                            <div><strong>Email:</strong> {{ $foundMember['email'] }}</div>
                            @if ($foundMember['institution'])
                                <div><strong>Institusi:</strong> {{ $foundMember['institution'] }}</div>
                            @endif
                            @if ($foundMember['study_program'])
                                <div><strong>Program Studi:</strong> {{ $foundMember['study_program'] }}</div>
                            @endif
                            <div><strong>Tipe Identitas:</strong> {{ $foundMember['identity_type'] }}</div>
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label" for="member_tugas">Tugas <span class="text-danger">*</span></label>
                    <textarea id="member_tugas" class="form-control @error('member_tugas') is-invalid @enderror"
                        wire:model.live="member_tugas" rows="3" placeholder="Jelaskan tugas anggota dalam penelitian ini"
                        {{ !$memberFound ? 'disabled' : '' }} required></textarea>
                    @error('member_tugas')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if (!$memberFound)
                        <small class="text-muted">Cek NIDN/NIP terlebih dahulu untuk mengisi tugas</small>
                    @endif
                </div>
            </x-slot:body>

            <x-slot:footer>
                <button type="button" class="btn-outline-secondary btn" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" wire:click="addMember" class="btn btn-primary"
                    {{ !$memberFound ? 'disabled' : '' }}>
                    <x-lucide-plus class="icon" />
                    Tambah
                </button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport
</div>
