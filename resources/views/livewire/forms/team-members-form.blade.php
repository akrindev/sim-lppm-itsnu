<div>
    <!-- Members List -->
    @if (!empty($members))
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
                        @foreach ($members as $index => $member)
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
                                    <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#modal-confirm-delete-{{ $index }}"
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
        <x-tabler.modal id="modal-add-member" :title="$modalTitle" on-show="resetMemberForm">
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

                        <div class="alert-icon">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/check -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon alert-icon icon-2">
                                <path d="M5 12l5 5l10 -10"></path>
                            </svg>
                        </div>

                        <dl class="mb-0 row g-2 small">
                            <div class="mb-2 col-12"><strong>Anggota Ditemukan:</strong></div> <br>
                            <dt class="text-bold col-12 col-sm-4">Nama</dt>
                            <dd class="col-12 col-sm-8">{{ $foundMember['name'] }}</dd>

                            <dt class="text-bold col-12 col-sm-4">NUPTK/NIDN</dt>
                            <dd class="col-12 col-sm-8">
                                {{ $foundMember['nidn'] }}
                            </dd>

                            @if (!empty($foundMember['institution']))
                                <dt class="text-bold col-12 col-sm-4">Institusi</dt>
                                <dd class="col-12 col-sm-8">{{ $foundMember['institution'] }}</dd>
                            @endif

                            @if (!empty($foundMember['study_program']))
                                <dt class="text-bold col-12 col-sm-4">Program Studi</dt>
                                <dd class="col-12 col-sm-8">{{ $foundMember['study_program'] }}</dd>
                            @endif

                            <dt class="text-bold col-12 col-sm-4">Tipe Identitas</dt>
                            <dd class="col-12 col-sm-8">{{ $foundMember['identity_type'] }}</dd>
                        </dl>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label" for="member_tugas">Tugas <span class="text-danger">*</span></label>
                    <textarea id="member_tugas" @class([
                        'form-control',
                        'is-invalid' => $errors->has('member_tugas'),
                        'disabled' => !$memberFound,
                    ]) wire:model.live="member_tugas" rows="3"
                        placeholder="Jelaskan tugas anggota dalam penelitian ini" {{ !$memberFound ? 'disabled' : '' }} required></textarea>
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

    <!-- Delete Confirmation Modals -->
    @if (!empty($members))
        @foreach ($members as $index => $member)
            @teleport('body')
                <x-tabler.modal id="modal-confirm-delete-{{ $index }}" title="Konfirmasi Hapus">
                    <x-slot:body>
                        <div class="text-center">
                            <div class="mb-3">
                                <x-lucide-alert-triangle class="text-danger" style="width: 64px; height: 64px;" />
                            </div>
                            <h3 class="mb-2">Hapus Anggota?</h3>
                            <p class="text-muted">
                                Apakah Anda yakin ingin menghapus <strong>{{ $member['name'] }}</strong> dari daftar
                                {{ strtolower($memberLabel) }}?
                            </p>
                            <p class="mb-0 text-danger small">
                                Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </x-slot:body>

                    <x-slot:footer>
                        <button type="button" class="btn-outline-secondary btn" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="button" wire:click="removeMember({{ $index }})" class="btn btn-danger"
                            data-bs-dismiss="modal">
                            <x-lucide-trash-2 class="icon" />
                            Ya, Hapus
                        </button>
                    </x-slot:footer>
                </x-tabler.modal>
            @endteleport
        @endforeach
    @endif
</div>
