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
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>Nama Mitra</th>
                            <th>Institusi</th>
                            <th>Email</th>
                            <th>Negara</th>
                            <th>Alamat</th>
                            <th>Surat Kesanggupan</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($form->partner_ids as $partnerId)
                            @php
                                $partner = $this->partners->find($partnerId);
                            @endphp
                            @if ($partner)
                                <tr wire:key="partner-{{ $partnerId }}">
                                    <td>
                                        <div class="font-weight-medium">{{ $partner->name }}</div>
                                    </td>
                                    <td>
                                        @if ($partner->institution)
                                            <div class="d-flex align-items-center">
                                                <x-lucide-building class="icon me-1 text-muted" />
                                                {{ $partner->institution }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($partner->email)
                                            <a href="mailto:{{ $partner->email }}" class="text-reset">
                                                <div class="d-flex align-items-center">
                                                    <x-lucide-mail class="icon me-1 text-muted" />
                                                    {{ $partner->email }}
                                                </div>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($partner->country)
                                            <div class="d-flex align-items-center">
                                                <x-lucide-map-pin class="icon me-1 text-muted" />
                                                {{ $partner->country }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($partner->address)
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $partner->address }}">
                                                {{ $partner->address }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($partner->hasMedia('commitment_letter'))
                                            <a href="{{ $partner->getFirstMediaUrl('commitment_letter') }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-primary">
                                                <x-lucide-file-text class="icon" />
                                                Lihat
                                            </a>
                                        @else
                                            <span class="badge bg-yellow-lt text-yellow-fg">
                                                <x-lucide-file-x class="icon me-1" />
                                                Tidak Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <button type="button"
                                                wire:click="$set('form.partner_ids', {{ json_encode(array_values(array_diff($form->partner_ids, [$partnerId]))) }})"
                                                class="btn btn-sm btn-danger">
                                                <x-lucide-trash class="icon" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal: Tambah Mitra -->
<x-tabler.modal id="modal-partner" title="Tambah Mitra Baru" size="lg">
    <div class="mb-3">
        <label class="form-label">Nama Mitra <span class="text-danger">*</span></label>
        <input type="text" wire:model="form.new_partner.name"
            class="form-control @error('form.new_partner.name') is-invalid @enderror"
            placeholder="Nama lengkap mitra" required>
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
        <label class="form-label">Institusi <span class="text-danger">*</span></label>
        <input type="text" wire:model="form.new_partner.institution"
            class="form-control @error('form.new_partner.institution') is-invalid @enderror"
            placeholder="Nama institusi" required>
        @error('form.new_partner.institution')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Negara <span class="text-danger">*</span></label>
        <input type="text" wire:model="form.new_partner.country"
            class="form-control @error('form.new_partner.country') is-invalid @enderror"
            placeholder="Negara" required>
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

    <x-slot:footer>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" wire:click="saveNewPartner" class="btn btn-primary">
            <span wire:loading.remove>
                <x-lucide-save class="icon" />
                Simpan Mitra Baru
            </span>
            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
            <span wire:loading>Menyimpan...</span>
        </button>
    </x-slot:footer>
</x-tabler.modal>
