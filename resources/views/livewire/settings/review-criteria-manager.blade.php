<div>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">Kriteria Penilaian</h2>
            <p class="text-muted">Kelola kriteria dan bobot penilaian untuk proposal penelitian dan pengabdian masyarakat.</p>
        </div>
    </div>

    <div class="row">
        {{-- Penelitian --}}
        <div class="mb-4 col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Penelitian</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th class="w-1">No</th>
                                <th>Kriteria</th>
                                <th>Acuan / Deskripsi</th>
                                <th class="text-center">Bobot (%)</th>
                                <th class="text-center">Status</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->researchCriterias as $criteria)
                                <tr>
                                    <td>{{ $criteria->order }}</td>
                                    <td>{{ $criteria->criteria }}</td>
                                    <td class="text-wrap small text-muted">{{ $criteria->description }}</td>
                                    <td class="text-center">{{ number_format($criteria->weight, 0) }}%</td>
                                    <td class="text-center">
                                        <label class="form-check form-switch form-check-inline m-0">
                                            <input class="form-check-input" type="checkbox" 
                                                wire:click="toggleActive({{ $criteria->id }})"
                                                @if($criteria->is_active) checked @endif>
                                        </label>
                                    </td>
                                    <td>
                                        <button class="btn btn-icon btn-ghost-primary" wire:click="edit({{ $criteria->id }})">
                                            <x-lucide-edit class="icon" />
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-light">
                                <td colspan="3" class="text-end fw-bold">Total Bobot:</td>
                                <td class="text-center fw-bold">{{ number_format($this->researchCriterias->sum('weight'), 0) }}%</td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pengabdian --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pengabdian Masyarakat (PkM)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th class="w-1">No</th>
                                <th>Kriteria</th>
                                <th>Acuan / Deskripsi</th>
                                <th class="text-center">Bobot (%)</th>
                                <th class="text-center">Status</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->pkmCriterias as $criteria)
                                <tr>
                                    <td>{{ $criteria->order }}</td>
                                    <td>{{ $criteria->criteria }}</td>
                                    <td class="text-wrap small text-muted">{{ $criteria->description }}</td>
                                    <td class="text-center">{{ number_format($criteria->weight, 0) }}%</td>
                                    <td class="text-center">
                                        <label class="form-check form-switch form-check-inline m-0">
                                            <input class="form-check-input" type="checkbox" 
                                                wire:click="toggleActive({{ $criteria->id }})"
                                                @if($criteria->is_active) checked @endif>
                                        </label>
                                    </td>
                                    <td>
                                        <button class="btn btn-icon btn-ghost-primary" wire:click="edit({{ $criteria->id }})">
                                            <x-lucide-edit class="icon" />
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-light">
                                <td colspan="3" class="text-end fw-bold">Total Bobot:</td>
                                <td class="text-center fw-bold">{{ number_format($this->pkmCriterias->sum('weight'), 0) }}%</td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    @if($editing)
        <div class="modal modal-blur fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kriteria Penilaian</h5>
                        <button type="button" class="btn-close" wire:click="cancelEdit"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Nama Kriteria</label>
                            <input type="text" class="form-control @error('editing.criteria') is-invalid @enderror" 
                                wire:model="editing.criteria">
                            @error('editing.criteria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Deskripsi / Acuan Penilaian</label>
                            <textarea class="form-control @error('editing.description') is-invalid @enderror" 
                                rows="3" wire:model="editing.description"></textarea>
                            @error('editing.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Bobot (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control @error('editing.weight') is-invalid @enderror" 
                                    wire:model="editing.weight">
                                <span class="input-group-text">%</span>
                                @error('editing.weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" wire:click="cancelEdit">
                            Batal
                        </button>
                        <button type="button" class="btn btn-primary ms-auto" wire:click="save">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
