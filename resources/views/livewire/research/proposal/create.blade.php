<x-slot:title>Usulan Penelitian Baru</x-slot:title>
<x-slot:pageTitle>Usulan Penelitian Baru</x-slot:pageTitle>
<x-slot:pageSubtitle>Buat proposal penelitian baru dengan mengisi form di bawah ini.</x-slot:pageSubtitle>
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
                    <h3 class="mb-0 card-title">1.1 Informasi Dasar Proposal</h3>
                </div>

                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="title">Judul Proposal <span
                                    class="text-danger">*</span></label>
                            <input id="title" type="text"
                                class="form-control @error('form.title') is-invalid @enderror" wire:model="form.title"
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
                                wire:model="form.research_scheme_id" placeholder="Pilih skema penelitian" required>
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
                            <label class="form-label" for="duration_in_years">Durasi (Tahun) <span
                                    class="text-danger">*</span></label>
                            <input id="duration_in_years" type="number"
                                class="form-control @error('form.duration_in_years') is-invalid @enderror"
                                wire:model="form.duration_in_years" min="1" max="10" value="1"
                                required>
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
                                wire:model="form.focus_area_id" placeholder="Pilih bidang fokus" required>
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
                                wire:model="form.theme_id" placeholder="Pilih tema" required>
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
                                wire:model="form.topic_id" placeholder="Pilih topik" required>
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

                    {{-- <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="national_priority">Prioritas Nasional</label>
                            <select id="national_priority"
                                class="form-select tom-select @error('national_priority_id') is-invalid @enderror"
                                wire:model="national_priority_id" placeholder="Pilih prioritas nasional (opsional)">
                                <option value="">-- Pilih Prioritas Nasional (Opsional) --</option>
                                @foreach ($this->nationalPriorities as $priority)
                                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                @endforeach
                            </select>
                            @error('national_priority_id')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> --}}

                    {{-- <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="sbk_value">Nilai SKB <span
                                    class="text-danger">*</span></label>
                            <input id="sbk_value" type="number" step="0.01"
                                class="form-control @error('sbk_value') is-invalid @enderror" wire:model="sbk_value"
                                placeholder="0.00" required>
                            @error('sbk_value')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

        <!-- Section: Klasifikasi Ilmu -->
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <x-lucide-dna class="me-3 icon" />
                    <h3 class="mb-0 card-title">1.2 Klasifikasi Ilmu (Klaster Sains)</h3>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="cluster_level1">Level 1 <span
                                    class="text-danger">*</span></label>
                            <select id="cluster_level1"
                                class="form-select tom-select @error('cluster_level1_id') is-invalid @enderror"
                                wire:model="form.cluster_level1_id" placeholder="Pilih level 1" required>
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
                                class="form-select tom-select @error('cluster_level2_id') is-invalid @enderror"
                                wire:model="form.cluster_level2_id" placeholder="Pilih level 2 (opsional)">
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
                                class="form-select tom-select @error('cluster_level3_id') is-invalid @enderror"
                                wire:model="form.cluster_level3_id" placeholder="Pilih level 3 (opsional)">
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
                    <h3 class="mb-0 card-title">1.3 Ringkasan Proposal</h3>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="summary">Ringkasan <span class="text-danger">*</span></label>
                    <textarea id="summary" class="form-control @error('form.summary') is-invalid @enderror" wire:model="form.summary"
                        rows="4" placeholder="Masukkan ringkasan proposal (minimal 100 karakter)" required></textarea>
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
                    <label class="form-label" for="final_tkt_target">Target TKT Final</label>
                    <input id="final_tkt_target" type="text"
                        class="form-control @error('form.final_tkt_target') is-invalid @enderror"
                        wire:model="form.final_tkt_target" placeholder="Contoh: TKT 5, TKT 6, dsb" required>
                    @error('form.final_tkt_target')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Tingkat Kesiapan Teknologi (TKT) yang ditargetkan</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="background">Latar Belakang</label>
                    <textarea id="background" class="form-control @error('form.background') is-invalid @enderror"
                        wire:model="form.background" rows="5"
                        placeholder="Jelaskan latar belakang penelitian (minimal 200 karakter)" required></textarea>
                    @error('form.background')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 200 karakter</small>
                </div>
                {{--
                <div class="mb-3">
                    <label class="form-label" for="state_of_the_art">State of the Art <span
                            class="text-danger">*</span></label>
                    <textarea id="state_of_the_art" class="form-control @error('form.state_of_the_art') is-invalid @enderror"
                        wire:model="form.state_of_the_art" rows="5"
                        placeholder="Jelaskan state of the art penelitian (minimal 200 karakter)" required></textarea>
                    @error('form.state_of_the_art')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 200 karakter</small>
                </div> --}}

                <div class="mb-3">
                    <label class="form-label" for="methodology">Metodologi</label>
                    <textarea id="methodology" class="form-control @error('form.methodology') is-invalid @enderror"
                        wire:model="form.methodology" rows="5" placeholder="Jelaskan metodologi penelitian (minimal 200 karakter)"
                        required></textarea>
                    @error('form.methodology')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 200 karakter</small>
                </div>
            </div>
        </div>

        {{-- section: ketua tasks --}}
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <x-lucide-user-check class="me-3 icon" />
                    <h3 class="mb-0 card-title">Tugas Ketua Peneliti</h3>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ketua Peneliti</label>
                    <input type="text" class="form-control @error('author_name') is-invalid @enderror"
                        wire:model="author_name" placeholder="Nama Ketua Peneliti" required disabled />
                    @error('author_name')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="ketua_tasks">Jelaskan tugas ketua peneliti dalam penelitian ini
                        <span class="text-danger">*</span></label>
                    <textarea id="ketua_tasks" class="form-control @error('form.author_tasks') is-invalid @enderror"
                        wire:model="form.author_tasks" rows="3" placeholder="Jelaskan tugas ketua peneliti dalam penelitian ini"
                        required></textarea>
                    @error('form.author_tasks')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
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

                <livewire:forms.team-members-form :members="$form->members" modal-title="Tambah Anggota Peneliti"
                    member-label="Anggota Peneliti" />
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
                <span wire:loading.remove>Simpan Proposal</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
