<x-slot:title>Edit Proposal Penelitian</x-slot:title>
<x-slot:pageTitle>Edit Proposal Penelitian</x-slot:pageTitle>
<x-slot:pageSubtitle>Ubah data proposal penelitian Anda.</x-slot:pageSubtitle>
<x-slot:pageActions>
    <a href="{{ route('research.proposal.index') }}" class="btn-outline-secondary btn">
        <x-lucide-arrow-left class="icon" />
        Kembali ke Daftar
    </a>
</x-slot:pageActions>

<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit.prevent="save" novalidate>
        <!-- Section: Informasi Dasar -->
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <x-lucide-file-text class="me-3 icon" />
                    <h3 class="mb-0 card-title">Informasi Dasar Proposal</h3>
                </div>

                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="title">Judul Proposal <span
                                    class="text-danger">*</span></label>
                            <input id="title" type="text"
                                class="form-control @error('title') is-invalid @enderror" wire:model.defer="form.title"
                                placeholder="Masukkan judul proposal penelitian" required>
                            @error('form.title')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="research_scheme">Skema Penelitian <span
                                    class="text-danger">*</span></label>
                            <select id="research_scheme"
                                class="form-select tom-select @error('form.research_scheme_id') is-invalid @enderror"
                                wire:model.defer="form.research_scheme_id" placeholder="Pilih skema penelitian"
                                required>
                                <option value="">-- Pilih Skema Penelitian --</option>
                                @foreach ($this->schemes as $scheme)
                                    <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                                @endforeach
                            </select>
                            @error('form.research_scheme_id')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="duration_in_years">Durasi Penelitian (Tahun) <span
                                    class="text-danger">*</span></label>
                            <input id="duration_in_years" type="number"
                                class="form-control @error('form.duration_in_years') is-invalid @enderror"
                                wire:model.defer="form.duration_in_years" min="1" max="10" required>
                            @error('form.duration_in_years')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="focus_area">Bidang Fokus <span
                                    class="text-danger">*</span></label>
                            <select id="focus_area"
                                class="form-select tom-select @error('form.focus_area_id') is-invalid @enderror"
                                wire:model.defer="form.focus_area_id" placeholder="Pilih bidang fokus" required>
                                <option value="">-- Pilih Bidang Fokus --</option>
                                @foreach ($this->focusAreas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            @error('form.focus_area_id')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="theme">Tema <span class="text-danger">*</span></label>
                            <select id="theme"
                                class="form-select tom-select @error('form.theme_id') is-invalid @enderror"
                                wire:model.defer="form.theme_id" placeholder="Pilih tema" required>
                                <option value="">-- Pilih Tema --</option>
                                @foreach ($this->themes as $theme)
                                    <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                @endforeach
                            </select>
                            @error('form.theme_id')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="topic">Topik <span class="text-danger">*</span></label>
                            <select id="topic"
                                class="form-select tom-select @error('form.topic_id') is-invalid @enderror"
                                wire:model.defer="form.topic_id" placeholder="Pilih topik" required>
                                <option value="">-- Pilih Topik --</option>
                                @foreach ($this->topics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                            @error('form.topic_id')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="national_priority">Prioritas Nasional</label>
                            <select id="national_priority"
                                class="form-select tom-select @error('form.national_priority_id') is-invalid @enderror"
                                wire:model.defer="form.national_priority_id"
                                placeholder="Pilih prioritas nasional (opsional)">
                                <option value="">-- Pilih Prioritas Nasional (Opsional) --</option>
                                @foreach ($this->nationalPriorities as $priority)
                                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                @endforeach
                            </select>
                            @error('form.national_priority_id')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="sbk_value">Nilai SKB <span
                                    class="text-danger">*</span></label>
                            <input id="sbk_value" type="number" step="0.01"
                                class="form-control @error('form.sbk_value') is-invalid @enderror"
                                wire:model.defer="form.sbk_value" placeholder="0.00" required>
                            @error('form.sbk_value')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Anggota -->
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <x-lucide-users class="me-3 icon" />
                    <h3 class="mb-0 card-title">Anggota Peneliti</h3>
                </div>

                <!-- Members List -->
                @if (!empty($form->members))
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
                                    @foreach ($form->members as $index => $member)
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
                                                <button type="button"
                                                    wire:click="confirmRemoveMember({{ $index }})"
                                                    class="btn-outline-danger btn btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteMemberModal" title="Hapus">
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#modal-add-member">
                    <x-lucide-plus class="icon" />
                    Tambah Anggota
                </button>

                @error('form.members')
                    <div class="d-block mt-2 text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Add Member Modal -->
        @teleport('body')
            <x-tabler.modal id="modal-add-member" title="Tambah Anggota Peneliti" :component-id="$componentId"
                on-show="resetMemberForm">
                <x-slot:body>
                    <div class="mb-3">
                        <label class="form-label" for="member_nidn">NIDN / NIP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input id="member_nidn" type="text"
                                class="form-control @error('member_nidn') is-invalid @enderror"
                                wire:model.live="member_nidn" placeholder="Masukkan NIDN atau NIP anggota">
                            <button class="btn-outline-primary btn" type="button" wire:click="checkMember"
                                id="button-addon2">
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

        @script
            <script>
                $wire.on('close-modal', (modalId) => {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }
                });
            </script>
        @endscript

        <!-- Section: Klasifikasi Ilmu -->
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <x-lucide-dna class="me-3 icon" />
                    <h3 class="mb-0 card-title">Klasifikasi Ilmu (Klaster Sains)</h3>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="cluster_level1">Level 1 <span
                                    class="text-danger">*</span></label>
                            <select id="cluster_level1"
                                class="form-select tom-select @error('form.cluster_level1_id') is-invalid @enderror"
                                wire:model.defer="form.cluster_level1_id" placeholder="Pilih level 1" required>
                                <option value="">-- Pilih Level 1 --</option>
                                @foreach ($this->scienceClusters->where('level', 1) as $cluster)
                                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                @endforeach
                            </select>
                            @error('form.cluster_level1_id')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="cluster_level2">Level 2</label>
                            <select id="cluster_level2"
                                class="form-select tom-select @error('form.cluster_level2_id') is-invalid @enderror"
                                wire:model.defer="form.cluster_level2_id" placeholder="Pilih level 2 (opsional)">
                                <option value="">-- Pilih Level 2 (Opsional) --</option>
                                @foreach ($this->scienceClusters->where('level', 2) as $cluster)
                                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                @endforeach
                            </select>
                            @error('form.cluster_level2_id')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="cluster_level3">Level 3</label>
                            <select id="cluster_level3"
                                class="form-select tom-select @error('form.cluster_level3_id') is-invalid @enderror"
                                wire:model.defer="form.cluster_level3_id" placeholder="Pilih level 3 (opsional)">
                                <option value="">-- Pilih Level 3 (Opsional) --</option>
                                @foreach ($this->scienceClusters->where('level', 3) as $cluster)
                                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                @endforeach
                            </select>
                            @error('form.cluster_level3_id')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Ringkasan -->
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <x-lucide-file-text class="me-3 icon" />
                    <h3 class="mb-0 card-title">Ringkasan Proposal</h3>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="summary">Ringkasan <span class="text-danger">*</span></label>
                    <textarea id="summary" class="form-control @error('form.summary') is-invalid @enderror"
                        wire:model.defer="form.summary" rows="4" placeholder="Masukkan ringkasan proposal (minimal 100 karakter)"
                        required></textarea>
                    @error('form.summary')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 100 karakter</small>
                </div>
            </div>
        </div>

        <!-- Section: Detail Penelitian -->
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <x-lucide-microscope class="me-3 icon" />
                    <h3 class="mb-0 card-title">Detail Penelitian</h3>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="final_tkt_target">Target TKT Final <span
                            class="text-danger">*</span></label>
                    <input id="final_tkt_target" type="text"
                        class="form-control @error('form.final_tkt_target') is-invalid @enderror"
                        wire:model.defer="form.final_tkt_target" placeholder="Contoh: TKT 5, TKT 6, dsb" required>
                    @error('form.final_tkt_target')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Tingkat Kesiapan Teknologi (TKT) yang ditargetkan</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="background">Latar Belakang <span
                            class="text-danger">*</span></label>
                    <textarea id="background" class="form-control @error('form.background') is-invalid @enderror"
                        wire:model.defer="form.background" rows="5"
                        placeholder="Jelaskan latar belakang penelitian (minimal 200 karakter)" required></textarea>
                    @error('form.background')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 200 karakter</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="state_of_the_art">State of the Art <span
                            class="text-danger">*</span></label>
                    <textarea id="state_of_the_art" class="form-control @error('form.state_of_the_art') is-invalid @enderror"
                        wire:model.defer="form.state_of_the_art" rows="5"
                        placeholder="Jelaskan state of the art penelitian (minimal 200 karakter)" required></textarea>
                    @error('form.state_of_the_art')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 200 karakter</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="methodology">Metodologi <span class="text-danger">*</span></label>
                    <textarea id="methodology" class="form-control @error('form.methodology') is-invalid @enderror"
                        wire:model.defer="form.methodology" rows="5"
                        placeholder="Jelaskan metodologi penelitian (minimal 200 karakter)" required></textarea>
                    @error('form.methodology')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 200 karakter</small>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('research.proposal.index') }}" class="btn-outline-secondary btn">
                <x-lucide-x class="icon" />
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <span class="me-2 spinner-border spinner-border-sm" wire:loading role="status"
                    aria-hidden="true"></span>
                <x-lucide-save class="icon" />
                <span wire:loading.remove>Simpan Perubahan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>


    <!-- Delete Member Confirmation Modal -->
    @teleport('body')
        <x-tabler.modal id="deleteMemberModal" title="Hapus Anggota?" wire:ignore.self>
            <x-slot:body>
                <div class="py-4 text-center">
                    <x-lucide-alert-circle class="mb-2 text-danger icon" style="width: 3rem; height: 3rem;" />
                    <h3>Hapus Anggota?</h3>
                    <div class="text-secondary">
                        Apakah Anda yakin ingin menghapus anggota ini dari proposal?
                    </div>
                </div>
            </x-slot:body>

            <x-slot:footer>
                <div class="w-100">
                    <div class="row">
                        <div class="col"><button type="button" class="w-100 btn btn-white" data-bs-dismiss="modal"
                                wire:click="cancelRemoveMember">
                                Batal
                            </button></div>
                        <div class="col"><button type="button"
                                wire:click="removeMember({{ $confirmingRemoveMemberIndex }})"
                                class="w-100 btn btn-danger" data-bs-dismiss="modal">
                                Ya, Hapus Anggota
                            </button></div>
                    </div>
                </div>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport
</div>
