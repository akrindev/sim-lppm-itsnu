<!-- Section: Substansi Usulan -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <x-lucide-book-open class="icon me-3" />
            <h3 class="card-title mb-0">2.1 Substansi Usulan</h3>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="macro_research_group">Kelompok Makro Riset</label>
                    <select id="macro_research_group"
                        class="form-select @error('form.macro_research_group_id') is-invalid @enderror"
                        wire:model="form.macro_research_group_id" x-data="tomSelect"
                        placeholder="Pilih kelompok makro riset">
                        <option value="">-- Pilih Kelompok Makro Riset --</option>
                        @foreach ($this->macroResearchGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    @error('form.macro_research_group_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="substance_file">Unggah Substansi Laporan (PDF)</label>
                    <input id="substance_file" type="file"
                        class="form-control @error('form.substance_file') is-invalid @enderror"
                        wire:model="form.substance_file" accept=".pdf">
                    @error('form.substance_file')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Maksimal 10MB, format PDF</small>

                    @if ($form->substance_file)
                        <div class="mt-2">
                            <x-lucide-file-check class="text-success icon" />
                            File terpilih: {{ $form->substance_file->getClientOriginalName() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section: Luaran Target Capaian -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <x-lucide-target class="icon me-3" />
                <h3 class="card-title mb-0">2.2 Luaran Target Capaian</h3>
            </div>
            <button type="button" wire:click="addOutput" class="btn btn-primary btn-sm">
                <x-lucide-plus class="icon" />
                Tambah Luaran
            </button>
        </div>

        @error('form.outputs')
            <div class="alert alert-danger mb-3">
                <div class="d-flex">
                    <x-lucide-alert-circle class="icon me-2" />
                    <div>{{ $message }}</div>
                </div>
            </div>
        @enderror

        @if (empty($form->outputs))
            <div class="alert alert-info">
                <x-lucide-info class="icon me-2" />
                Belum ada luaran target. Klik tombol "Tambah Luaran" untuk menambahkan.
            </div>
        @else
            <div class="table-responsive">
                <table class="table-bordered table">
                    <thead>
                        <tr>
                            {{-- <th width="20%">Tahun</th> --}}
                            <th width="20%">Jenis</th>
                            <th width="20%">Kategori Luaran</th>
                            <th width="20%">Luaran</th>
                            <th width="15%">Status</th>
                            <th width="20%">Keterangan</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($form->outputs as $index => $output)
                            <tr wire:key="output-{{ $index }}" x-data="{
                                group: $wire.entangle('form.outputs.{{ $index }}.group'),
                                types: {
                                    'jurnal': ['Jurnal Nasional Terakreditasi', 'Jurnal Internasional', 'Jurnal Internasional Bereputasi'],
                                    'prosiding': ['Prosiding Seminar Nasional', 'Prosiding Seminar Internasional'],
                                    'buku': ['Buku Ajar', 'Buku Referensi', 'Monograf'],
                                    'hki': ['Paten', 'Paten Sederhana', 'Hak Cipta', 'Desain Industri', 'DTLST', 'PVT'],
                                    'media': ['Media Massa Nasional', 'Media Massa Internasional', 'Media Massa Lokal'],
                                    'video': ['Video Kegiatan'],
                                    'produk': ['Produk', 'Model', 'Purwarupa', 'TTG']
                                }
                            }">
                                {{-- <td>
                                    <input type="number" wire:model="form.outputs.{{ $index }}.year"
                                        class="form-control form-control-sm" placeholder="2025" min="2020"
                                        max="2040">
                                </td> --}}
                                <td>
                                    <select wire:model="form.outputs.{{ $index }}.category"
                                        class="form-select-sm form-select">
                                        <option value="Wajib">Wajib</option>
                                        <option value="Tambahan">Tambahan</option>
                                    </select>
                                </td>

                                <td>
                                    <select x-model="group" class="form-select-sm form-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="jurnal">Jurnal</option>
                                        <option value="prosiding">Prosiding</option>
                                        <option value="buku">Buku</option>
                                        <option value="hki">HKI</option>
                                        <option value="media">Media Massa</option>
                                        <option value="video">Video</option>
                                        <option value="produk">Produk/TTG</option>
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="form.outputs.{{ $index }}.type"
                                        class="form-select-sm form-select">
                                        <option value="">-- Pilih --</option>
                                        <template x-for="type in (types[group] || [])">
                                            <option x-text="type" :value="type"></option>
                                        </template>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" wire:model="form.outputs.{{ $index }}.status"
                                        class="form-control form-control-sm" placeholder="Status">
                                </td>
                                <td>
                                    <input type="text" wire:model="form.outputs.{{ $index }}.description"
                                        class="form-control form-control-sm" placeholder="Keterangan">
                                </td>
                                <td>
                                    <button type="button" wire:click="removeOutput({{ $index }})"
                                        class="btn btn-sm btn-danger">
                                        <x-lucide-trash-2 class="icon" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
