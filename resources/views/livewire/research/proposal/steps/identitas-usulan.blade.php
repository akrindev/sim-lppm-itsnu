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

            <div class="col-md-4">
                <div class="">
                    <label class="form-label" for="research_scheme">Skema Penelitian <span
                            class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="research_scheme"
                            class="form-select @error('form.research_scheme_id') is-invalid @enderror"
                            wire:model="form.research_scheme_id" x-data="tomSelect"
                            placeholder="Pilih skema penelitian" required>
                            <option value="">-- Pilih Skema Penelitian --</option>
                            @foreach ($this->schemes as $scheme)
                                <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.research_scheme_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="">
                    <label class="form-label" for="duration_in_years">Durasi (Tahun) <span
                            class="text-danger">*</span></label>
                    <input id="duration_in_years" type="number"
                        class="form-control @error('form.duration_in_years') is-invalid @enderror"
                        wire:model="form.duration_in_years" min="1" max="10" value="1" required>
                    @error('form.duration_in_years')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="">
                    <label class="form-label" for="focus_area">Bidang Fokus <span class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="focus_area" class="form-select @error('form.focus_area_id') is-invalid @enderror"
                            wire:model="form.focus_area_id" x-data="tomSelect" placeholder="Pilih bidang fokus"
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
                <div class="">
                    <label class="form-label" for="theme">Tema <span class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="theme" class="form-select @error('form.theme_id') is-invalid @enderror"
                            wire:model="form.theme_id" x-data="tomSelect" placeholder="Pilih tema" required>
                            <option value="">-- Pilih Tema --</option>
                            @foreach ($this->themes as $theme)
                                <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.theme_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="">
                    <label class="form-label" for="topic">Topik <span class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="topic" class="form-select @error('form.topic_id') is-invalid @enderror"
                            wire:model="form.topic_id" x-data="tomSelect" placeholder="Pilih topik" required>
                            <option value="">-- Pilih Topik --</option>
                            @foreach ($this->topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.topic_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section: Rumpun Ilmu -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <x-lucide-dna class="icon me-3" />
            <h3 class="card-title mb-0">1.2 Rumpun Ilmu</h3>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="cluster_level1">Level 1 <span class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="cluster_level1"
                            class="form-select @error('form.cluster_level1_id') is-invalid @enderror"
                            wire:model="form.cluster_level1_id" x-data="tomSelect" placeholder="Pilih level 1"
                            required>
                            <option value="">-- Pilih Level 1 --</option>
                            @foreach ($this->scienceClusters->where('level', 1) as $cluster)
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
                    <div wire:ignore>
                        <select id="cluster_level2"
                            class="form-select @error('form.cluster_level2_id') is-invalid @enderror"
                            wire:model="form.cluster_level2_id" x-data="tomSelect"
                            placeholder="Pilih level 2 (opsional)">
                            <option value="">-- Pilih Level 2 (Opsional) --</option>
                            @foreach ($this->scienceClusters->where('level', 2) as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.cluster_level2_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="cluster_level3">Level 3</label>
                    <div wire:ignore>
                        <select id="cluster_level3"
                            class="form-select @error('form.cluster_level3_id') is-invalid @enderror"
                            wire:model="form.cluster_level3_id" x-data="tomSelect"
                            placeholder="Pilih level 3 (opsional)">
                            <option value="">-- Pilih Level 3 (Opsional) --</option>
                            @foreach ($this->scienceClusters->where('level', 3) as $cluster)
                                <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.cluster_level3_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section: Ringkasan -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <x-lucide-file-text class="icon me-3" />
            <h3 class="card-title mb-0">1.3 Ringkasan Proposal</h3>
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
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <x-lucide-microscope class="icon me-3" />
            <h3 class="card-title mb-0">Detail Penelitian</h3>
        </div>

        <div class="mb-3">
            <label class="form-label" for="tkt_type">Kategori TKT <span class="text-danger">*</span></label>
            <select id="tkt_type" class="form-select @error('form.tkt_type') is-invalid @enderror"
                wire:model.live="form.tkt_type" required>
                <option value="">-- Pilih Kategori TKT --</option>
                @foreach ($this->tktTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
            @error('form.tkt_type')
                <div class="d-block invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Pengukuran TKT</label>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#tkt-measurement-modal"
                    wire:click="$dispatch('tkt-type-selected', { tktType: '{{ $form->tkt_type }}', existingScores: @js($form->tkt_indicator_scores) })"
                    @if (!$form->tkt_type) disabled @endif>
                    <x-lucide-ruler class="icon me-2" />
                    Ukur TKT Saat Ini
                </button>

                @if (!empty($form->tkt_results))
                    @php
                        $currentTkt = 0;
                        foreach ($form->tkt_results as $levelId => $data) {
                            if ($data['percentage'] >= 80) {
                                $currentTkt = max($currentTkt, $data['level']);
                            }
                        }
                    @endphp
                    <div class="badge bg-success fs-3">
                        TKT Saat Ini: Level {{ $currentTkt }}
                    </div>
                @endif
            </div>
            <small class="text-muted d-block mt-1">Pilih kategori terlebih dahulu, lalu klik tombol untuk mengukur
                TKT.</small>
        </div>

        {{-- <div class="mb-3">
            <label class="form-label" for="background">Latar Belakang</label>
            <textarea id="background" class="form-control @error('form.background') is-invalid @enderror"
                wire:model="form.background" rows="5"
                placeholder="Jelaskan latar belakang penelitian (minimal 200 karakter)"></textarea>
            @error('form.background')
                <div class="d-block invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Minimum 200 karakter</small>
        </div>

        <div class="mb-3">
            <label class="form-label" for="methodology">Metodologi</label>
            <textarea id="methodology" class="form-control @error('form.methodology') is-invalid @enderror"
                wire:model="form.methodology" rows="5" placeholder="Jelaskan metodologi penelitian (minimal 200 karakter)"></textarea>
            @error('form.methodology')
                <div class="d-block invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Minimum 200 karakter</small>
        </div> --}}
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
            member-label="Anggota Peneliti" />
    </div>
</div>

<livewire:research.proposal.components.tkt-measurement />
