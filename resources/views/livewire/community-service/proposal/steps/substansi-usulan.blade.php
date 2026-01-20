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
                    <label class="form-label" for="macro_research_group">Kelompok Makro Riset <span
                            class="text-danger">*</span></label>
                    <div wire:ignore>
                        <select id="macro_research_group"
                            class="form-select @error('form.macro_research_group_id') is-invalid @enderror"
                            wire:model="form.macro_research_group_id" x-data="tomSelect"
                            placeholder="Pilih kelompok makro riset" required>
                            <option value="">-- Pilih Kelompok Makro Riset --</option>
                            @foreach ($this->macroResearchGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.macro_research_group_id')
                        <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label d-flex justify-content-between align-items-center" for="substance_file">
                        <span>Unggah Substansi Laporan (PDF) <span class="text-danger">*</span></span>
                        @if ($this->templateUrl)
                            <a href="{{ $this->templateUrl }}" target="_blank" class="text-primary text-decoration-none"
                                style="font-size: 0.875rem;">
                                <x-lucide-download class="icon me-1" style="width: 1rem; height: 1rem;" />
                                Unduh Template
                            </a>
                        @endif
                    </label>
                    <input id="substance_file" type="file"
                        wire:key="substance-file-{{ $fileInputIteration }}"
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
                    @elseif ($form->proposal && $form->proposal->detailable && $form->proposal->detailable->getFirstMediaUrl('substance_file'))
                        <div class="mt-2">
                            <x-lucide-file-check class="text-success icon" />
                            <a href="{{ $form->proposal->detailable->getFirstMediaUrl('substance_file') }}" target="_blank"
                                class="text-decoration-none">
                                {{ $form->proposal->detailable->getFirstMedia('substance_file')->name }}
                            </a>
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
                <x-lucide-alert-circle class="icon me-2" />
                {{ $message }}
            </div>
        @enderror

        @if (empty($form->outputs))
            <div class="alert alert-info">
                <x-lucide-info class="icon me-2" />
                Belum ada luaran target. Klik tombol "Tambah Luaran" untuk menambahkan.
            </div>
        @else
            <div class="table-responsive" x-data="{
                typesMap: @js(\App\Constants\ProposalConstants::PKM_OUTPUT_TYPES)
            }">
                <table class="table-bordered table">
                    @php
                        $duration = (int) ($form->duration_in_years ?? 1);
                        $startYear = (int) ($form->start_year ?? date('Y'));
                    @endphp
                    <thead>
                        <tr>
                            <th style="width: 100px;">Tahun Ke- <span class="text-danger">*</span></th>
                            <th width="15%">Jenis <span class="text-danger">*</span></th>
                            <th width="20%">Kategori Luaran <span class="text-danger">*</span></th>
                            <th width="20%">Luaran <span class="text-danger">*</span></th>
                            <th width="15%">Status <span class="text-danger">*</span></th>
                            <th width="20%">Keterangan (URL) <span class="text-danger">*</span></th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($form->outputs as $index => $output)
                            <tr wire:key="output-{{ $index }}" x-data="{
                                group: $wire.entangle('form.outputs.{{ $index }}.group'),
                                type: $wire.entangle('form.outputs.{{ $index }}.type')
                            }">
                                <td>
                                    <select wire:model="form.outputs.{{ $index }}.year"
                                        class="form-select form-select-sm @error('form.outputs.' . $index . '.year') is-invalid @enderror">
                                        @for ($y = 1; $y <= $duration; $y++)
                                            <option value="{{ $y }}">{{ $y }}
                                                ({{ $startYear + $y - 1 }})</option>
                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="form.outputs.{{ $index }}.category"
                                        class="form-select-sm form-select @error('form.outputs.' . $index . '.category') is-invalid @enderror">
                                        <option value="Wajib">Wajib</option>
                                        <option value="Tambahan">Tambahan</option>
                                    </select>
                                </td>
                                <td>
                                    <select x-model="group"
                                        class="form-select-sm form-select @error('form.outputs.' . $index . '.group') is-invalid @enderror">
                                        <option value="">-- Pilih --</option>
                                        <option value="pemberdayaan">Pemberdayaan</option>
                                        <option value="jurnal">Jurnal</option>
                                        <option value="media">Media Massa</option>
                                        <option value="video">Video</option>
                                        <option value="produk">Produk/TTG</option>
                                        <option value="hki_buku">HKI/Buku</option>
                                    </select>
                                </td>
                                <td>
                                    <select x-model="type"
                                        class="form-select-sm form-select @error('form.outputs.' . $index . '.type') is-invalid @enderror">
                                        <option value="">-- Pilih --</option>
                                        <template x-for="typeOption in (typesMap[group] || [])">
                                            <option x-text="typeOption" :value="typeOption"></option>
                                        </template>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" wire:model="form.outputs.{{ $index }}.status"
                                        class="form-control form-control-sm @error('form.outputs.'.$index.'.status') is-invalid @enderror" placeholder="Status">
                                </td>
                                <td>
                                    <input type="text" wire:model="form.outputs.{{ $index }}.description"
                                        class="form-control form-control-sm @error('form.outputs.'.$index.'.description') is-invalid @enderror"
                                        placeholder="Keterangan (URL)">
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
