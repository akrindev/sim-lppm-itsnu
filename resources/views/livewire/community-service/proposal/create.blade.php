<x-slot:title>Usulan Pengabdian Masyarakat Baru</x-slot:title>
<x-slot:pageTitle>Usulan Pengabdian Masyarakat Baru</x-slot:pageTitle>
<x-slot:pageSubtitle>Buat proposal pengabdian masyarakat baru dengan mengisi form di bawah ini.</x-slot:pageSubtitle>
<x-slot:pageActions>
    <a href="{{ route('community-service.proposal.index') }}" class="btn-outline-secondary btn">
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
                                class="form-control @error('form.title') is-invalid @enderror"
                                wire:model.live="form.title" placeholder="Masukkan judul proposal pengabdian" required>
                            @error('form.title')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="duration_in_years">Durasi Pengabdian (Tahun) <span
                                    class="text-danger">*</span></label>
                            <input id="duration_in_years" type="number"
                                class="form-control @error('form.duration_in_years') is-invalid @enderror"
                                wire:model.live="form.duration_in_years" min="1" max="10" value="1"
                                required>
                            @error('form.duration_in_years')
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
                                wire:model.live="form.sbk_value" placeholder="0.00" required>
                            @error('form.sbk_value')
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
                                wire:model.live="form.focus_area_id" placeholder="Pilih bidang fokus" required>
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
                                wire:model.live="form.theme_id" placeholder="Pilih tema" required>
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
                                wire:model.live="form.topic_id" placeholder="Pilih topik" required>
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
                                wire:model.live="form.national_priority_id"
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
                </div>
            </div>
        </div>

        <!-- Section: Anggota -->
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <x-lucide-users class="me-3 icon" />
                    <h3 class="mb-0 card-title">Anggota Pengabdi</h3>
                </div>

                <livewire:forms.team-members-form :members="$this->form->members" 
                    modal-title="Tambah Anggota Pengabdi" 
                    member-label="Anggota Pengabdi"
                    wire:model="form.members" />
            </div>
        </div>

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
                                wire:model.live="form.cluster_level1_id" placeholder="Pilih level 1" required>
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
                                wire:model.live="form.cluster_level2_id" placeholder="Pilih level 2 (opsional)">
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
                                wire:model.live="form.cluster_level3_id" placeholder="Pilih level 3 (opsional)">
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
                        wire:model.live="form.summary" rows="4" placeholder="Masukkan ringkasan proposal (minimal 100 karakter)"
                        required></textarea>
                    @error('form.summary')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 100 karakter</small>
                </div>
            </div>
        </div>

        <!-- Section: Detail Pengabdian -->
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <x-lucide-hand-heart class="me-3 icon" />
                    <h3 class="mb-0 card-title">Detail Pengabdian</h3>
                </div>

                <!-- Partner Selection -->
                <div class="mb-3">
                    <label class="form-label" for="partner_id">Mitra Pengabdian</label>
                    <select id="partner_id"
                        class="form-select tom-select @error('form.partner_id') is-invalid @enderror"
                        wire:model.live="form.partner_id" placeholder="Pilih mitra pengabdian (opsional)">
                        <option value="">-- Pilih Mitra (Opsional) --</option>
                        @foreach ($this->partners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                    </select>
                    @error('form.partner_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Pilih mitra yang akan dihadapi dalam pengabdian ini (opsional)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="partner_issue_summary">Ringkasan Masalah Mitra <span
                            class="text-danger">*</span></label>
                    <textarea id="partner_issue_summary" class="form-control @error('form.partner_issue_summary') is-invalid @enderror"
                        wire:model.live="form.partner_issue_summary" rows="5"
                        placeholder="Jelaskan ringkasan masalah yang dihadapi oleh mitra (minimal 50 karakter)" required></textarea>
                    @error('form.partner_issue_summary')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 50 karakter</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="solution_offered">Solusi yang Ditawarkan <span
                            class="text-danger">*</span></label>
                    <textarea id="solution_offered" class="form-control @error('form.solution_offered') is-invalid @enderror"
                        wire:model.live="form.solution_offered" rows="5"
                        placeholder="Jelaskan solusi yang akan ditawarkan untuk mengatasi masalah mitra (minimal 50 karakter)" required></textarea>
                    @error('form.solution_offered')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 50 karakter</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="background">Latar Belakang <span
                            class="text-danger">*</span></label>
                    <textarea id="background" class="form-control @error('form.background') is-invalid @enderror"
                        wire:model.live="form.background" rows="5"
                        placeholder="Jelaskan latar belakang pengabdian (minimal 200 karakter)" required></textarea>
                    @error('form.background')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 200 karakter</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="methodology">Metodologi <span class="text-danger">*</span></label>
                    <textarea id="methodology" class="form-control @error('form.methodology') is-invalid @enderror"
                        wire:model.live="form.methodology" rows="5"
                        placeholder="Jelaskan metodologi pengabdian (minimal 200 karakter)" required></textarea>
                    @error('form.methodology')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 200 karakter</small>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('community-service.proposal.index') }}" class="btn-outline-secondary btn">
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
