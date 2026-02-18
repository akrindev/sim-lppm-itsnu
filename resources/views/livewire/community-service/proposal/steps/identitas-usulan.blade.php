@if ($this->budgetCapMissingMessage)
    <div class="alert alert-danger mb-3">
        <div class="d-flex">
            <x-lucide-alert-circle class="icon me-2" />
            <div>{{ $this->budgetCapMissingMessage }}</div>
        </div>
    </div>
@endif

<!-- Section: Informasi Dasar -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <x-lucide-file-text class="icon me-3" />
            <h3 class="card-title mb-0">1.1 Informasi Dasar Proposal</h3>
        </div>

        <div class="row g-4">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Judul Proposal <span class="text-danger">*</span></label>
                    <input id="title" type="text" class="form-control @error('form.title') is-invalid @enderror"
                        wire:model="form.title" placeholder="Masukkan judul proposal penelitian" required>
                    @error('form.title')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label" for="start_year">Tahun Mulai <span class="text-danger">*</span></label>
                    <input id="start_year" type="number"
                        class="form-control @error('form.start_year') is-invalid @enderror"
                        wire:model.live="form.start_year" min="{{ date('Y') - 1 }}" max="{{ date('Y') + 5 }}"
                        placeholder="{{ date('Y') }}" required>
                    @error('form.start_year')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label" for="duration_in_years">Durasi (Tahun) <span
                            class="text-danger">*</span></label>
                    <input id="duration_in_years" type="number"
                        class="form-control @error('form.duration_in_years') is-invalid @enderror"
                        wire:model.live="form.duration_in_years" min="1" max="10" value="1" required>
                    @error('form.duration_in_years')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if ($form->start_year && $form->duration_in_years)
                        <small class="text-muted">
                            Periode: {{ $form->start_year }} -
                            {{ (int) $form->start_year + (int) $form->duration_in_years - 1 }}
                        </small>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="focus_area">Bidang Fokus <span class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="focus_area" class="form-select @error('form.focus_area_id') is-invalid @enderror"
                            wire:model.live="form.focus_area_id" x-data="tomSelect" placeholder="Pilih bidang fokus"
                            required>
                            <option value="">-- Pilih Bidang Fokus --</option>
                            @foreach ($this->focusAreas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.focus_area_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="theme">Tema <span class="text-danger">*</span></label>
                    <div wire:key="theme-select-{{ $form->focus_area_id }}">
                        <div wire:ignore>
                            <select id="theme" class="form-select @error('form.theme_id') is-invalid @enderror"
                                wire:model.live="form.theme_id" x-data="tomSelect" placeholder="Pilih tema" required>
                                <option value="">-- Pilih Tema --</option>
                                @foreach ($this->themes as $theme)
                                    <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @error('form.theme_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="topic">Topik <span class="text-danger">*</span></label>
                    <div wire:key="topic-select-{{ $form->theme_id }}">
                        <div wire:ignore>
                            <select id="topic" class="form-select @error('form.topic_id') is-invalid @enderror"
                                wire:model="form.topic_id" x-data="tomSelect" placeholder="Pilih topik" required>
                                <option value="">-- Pilih Topik --</option>
                                @foreach ($this->topics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @error('form.topic_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section: Klasifikasi Ilmu -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <x-lucide-dna class="icon me-3" />
            <h3 class="card-title mb-0">1.2 Klasifikasi Ilmu (Klaster Sains)</h3>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="cluster_level1">Klaster Ilmu Pengetahuan: Level 1 <span
                            class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="cluster_level1"
                            class="form-select @error('form.cluster_level1_id') is-invalid @enderror"
                            wire:model.live="form.cluster_level1_id" x-data="tomSelect" placeholder="Pilih level 1"
                            required>
                            <option value="">-- Pilih Level 1 --</option>
                            @foreach ($this->clusterLevel1Options as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.cluster_level1_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="cluster_level2">Level 2</label>
                    <div wire:key="cluster2-select-{{ $form->cluster_level1_id }}">
                        <div wire:ignore>
                            <select id="cluster_level2"
                                class="form-select @error('form.cluster_level2_id') is-invalid @enderror"
                                wire:model.live="form.cluster_level2_id" x-data="tomSelect"
                                placeholder="Pilih level 2 (opsional)">
                                <option value="">-- Pilih Level 2 (Opsional) --</option>
                                @foreach ($this->clusterLevel2Options as $cluster)
                                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @error('form.cluster_level2_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="cluster_level3">Level 3</label>
                    <div wire:key="cluster3-select-{{ $form->cluster_level2_id }}">
                        <div wire:ignore>
                            <select id="cluster_level3"
                                class="form-select @error('form.cluster_level3_id') is-invalid @enderror"
                                wire:model="form.cluster_level3_id" x-data="tomSelect"
                                placeholder="Pilih level 3 (opsional)">
                                <option value="">-- Pilih Level 3 (Opsional) --</option>
                                @foreach ($this->clusterLevel3Options as $cluster)
                                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @error('form.cluster_level3_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Section: Ringkasan & Masalah -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <x-lucide-file-text class="icon me-3" />
            <h3 class="card-title mb-0">1.3 Ringkasan & Masalah Mitra</h3>
        </div>

        <div class="mb-3">
            <label class="form-label" for="summary">Ringkasan Proposal <span class="text-danger">*</span></label>
            <textarea id="summary" class="form-control @error('form.summary') is-invalid @enderror" wire:model="form.summary"
                rows="4" placeholder="Masukkan ringkasan proposal (minimal 100 karakter)" required></textarea>
            @error('form.summary')
                <div class="d-block invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Minimum 100 karakter</small>
        </div>

        <div class="mb-3">
            <label class="form-label" for="partner_issue_summary">Masalah Mitra <span class="text-danger">*</span></label>
            <textarea id="partner_issue_summary" class="form-control @error('form.partner_issue_summary') is-invalid @enderror"
                wire:model="form.partner_issue_summary" rows="4" placeholder="Jelaskan masalah yang dihadapi mitra (minimal 50 karakter)"
                required></textarea>
            @error('form.partner_issue_summary')
                <div class="d-block invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Minimum 50 karakter</small>
        </div>

        <div class="mb-3">
            <label class="form-label" for="solution_offered">Solusi yang Ditawarkan <span class="text-danger">*</span></label>
            <textarea id="solution_offered" class="form-control @error('form.solution_offered') is-invalid @enderror"
                wire:model="form.solution_offered" rows="4" placeholder="Jelaskan solusi yang ditawarkan (minimal 50 karakter)"
                required></textarea>
            @error('form.solution_offered')
                <div class="d-block invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Minimum 50 karakter</small>
        </div>
    </div>
</div>

<!-- Section: Ketua Tasks -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <x-lucide-user-check class="icon me-3" />
            <h3 class="card-title mb-0">Tugas Ketua Peneliti</h3>
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
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <x-lucide-users class="icon me-3" />
            <h3 class="card-title mb-0">Anggota Peneliti</h3>
        </div>

        <livewire:forms.team-members-form :members="$form->members" modal-title="Tambah Anggota Peneliti"
            member-label="Anggota Peneliti" :key="'team-members-form'" />
    </div>
</div>
