<!-- Section: Dokumen Pendukung (Mitra) -->
<div class="mb-3 card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <x-lucide-handshake class="me-3 icon" />
                <h3 class="mb-0 card-title">Mitra Kerjasama</h3>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-partner">
                <x-lucide-plus class="icon" />
                Tambah Mitra
            </button>
        </div>

        @if (empty($form->partner_ids))
            <div class="alert alert-info">
                <x-lucide-info class="icon me-2" />
                Belum ada mitra yang ditambahkan.
            </div>
        @else
            <div class="list-group">
                @foreach ($form->partner_ids as $partnerId)
                    @php
                        $partner = $this->partners->find($partnerId);
                    @endphp
                    @if ($partner)
                        <div class="list-group-item" wire:key="partner-{{ $partnerId }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">{{ $partner->name }}</h5>
                                    <p class="mb-1 text-muted">
                                        @if ($partner->institution)
                                            <x-lucide-building class="icon icon-inline" /> {{ $partner->institution }}
                                        @endif
                                        @if ($partner->country)
                                            <x-lucide-map-pin class="icon icon-inline ms-2" /> {{ $partner->country }}
                                        @endif
                                    </p>
                                    @if ($partner->email)
                                        <small class="text-muted">
                                            <x-lucide-mail class="icon icon-inline" /> {{ $partner->email }}
                                        </small>
                                    @endif
                                </div>
                                <button type="button"
                                    wire:click="$set('form.partner_ids', {{ json_encode(array_values(array_diff($form->partner_ids, [$partnerId]))) }})"
                                    class="btn btn-sm btn-danger">
                                    <x-lucide-x class="icon" />
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal: Tambah Mitra -->
<div class="modal fade" id="modal-partner" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Mitra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-existing-partner"
                            type="button" role="tab">
                            Pilih Mitra Existing
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-new-partner"
                            type="button" role="tab">
                            Buat Mitra Baru
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab: Pilih Existing -->
                    <div class="tab-pane fade show active" id="tab-existing-partner" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Pilih Mitra</label>
                            <div wire:ignore>
                                <select class="form-select" x-data="tomSelect" multiple>
                                    <option value="">-- Pilih Mitra --</option>
                                    @foreach ($this->partners as $partner)
                                        <option value="{{ $partner->id }}"
                                            wire:click="$set('form.partner_ids', [...$wire.form.partner_ids, {{ $partner->id }}])">
                                            {{ $partner->name }}
                                            @if ($partner->institution)
                                                ({{ $partner->institution }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Buat Baru -->
                    <div class="tab-pane fade" id="tab-new-partner" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Nama Mitra <span class="text-danger">*</span></label>
                            <input type="text" wire:model="form.new_partner.name"
                                class="form-control @error('form.new_partner.name') is-invalid @enderror"
                                placeholder="Nama lengkap mitra">
                            @error('form.new_partner.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Surel</label>
                            <input type="email" wire:model="form.new_partner.email"
                                class="form-control @error('form.new_partner.email') is-invalid @enderror"
                                placeholder="email@example.com">
                            @error('form.new_partner.email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Institusi</label>
                            <input type="text" wire:model="form.new_partner.institution"
                                class="form-control @error('form.new_partner.institution') is-invalid @enderror"
                                placeholder="Nama institusi">
                            @error('form.new_partner.institution')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Negara</label>
                            <input type="text" wire:model="form.new_partner.country"
                                class="form-control @error('form.new_partner.country') is-invalid @enderror"
                                placeholder="Negara">
                            @error('form.new_partner.country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea wire:model="form.new_partner.address"
                                class="form-control @error('form.new_partner.address') is-invalid @enderror"
                                rows="3" placeholder="Alamat lengkap"></textarea>
                            @error('form.new_partner.address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File Surat Kesanggupan Mitra (PDF)</label>
                            <input type="file" wire:model="form.new_partner_commitment_file"
                                class="form-control @error('form.new_partner_commitment_file') is-invalid @enderror"
                                accept=".pdf">
                            @error('form.new_partner_commitment_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maksimal 5MB, format PDF</small>

                            @if ($form->new_partner_commitment_file)
                                <div class="mt-2">
                                    <x-lucide-file-check class="icon text-success" />
                                    File terpilih: {{ $form->new_partner_commitment_file->getClientOriginalName() }}
                                </div>
                            @endif
                        </div>

                        <button type="button" wire:click="saveNewPartner" class="btn btn-primary">
                            <x-lucide-save class="icon" />
                            Simpan Mitra Baru
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
