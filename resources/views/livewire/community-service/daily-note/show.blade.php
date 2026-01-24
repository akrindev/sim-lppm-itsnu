<x-slot:title>Catatan Harian PKM: {{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>Catatan Harian (Logbook)</x-slot:pageTitle>
<x-slot:pageSubtitle>{{ $proposal->title }}</x-slot:pageSubtitle>
<x-slot:pageActions>
    <a href="{{ route('community-service.daily-note.index') }}" class="btn-outline-secondary btn" wire:navigate>
        <x-lucide-arrow-left class="me-2 icon" />
        Kembali
    </a>
    <a href="{{ route('daily-notes.export-pdf', $proposal) }}" target="_blank" class="btn-outline-primary btn">
        <x-lucide-download class="me-2 icon" />
        Unduh Catatan (PDF)
    </a>
</x-slot:pageActions>

<div>
    <x-tabler.alert />

    <div class="alert alert-info" role="alert">
        <div class="d-flex">
            <div>
                <x-lucide-info class="icon alert-icon" />
            </div>
            <div>
                <h4 class="alert-title">Informasi Catatan Harian</h4>
                <div class="text-secondary">
                    Gunakan fitur ini untuk mencatat aktivitas harian pengabdian masyarakat Anda. Catatan ini akan
                    menjadi bukti pelaksanaan kegiatan dan digunakan dalam pemantauan kemajuan pengabdian. Lampirkan
                    foto atau dokumen sebagai bukti dukung kegiatan.
                </div>
            </div>
        </div>
    </div>

    @if ($notes_list->count() > 0)
        <div class="mt-3 mb-3 row g-2">
            <div class="col-md-3">
                <div class="bg-primary text-primary-fg card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="bg-white-lt me-3 stamp">
                                <x-lucide-calculator class="icon" />
                            </span>
                            <div>
                                <div class="text-white-50 small">Total Digunakan</div>
                                <div class="mb-0 h2 fw-bold">Rp
                                    {{ number_format($notes_list->sum('amount'), 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($notes_list->groupBy('budget_group_id') as $groupId => $items)
                <div class="col-md-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <span class="bg-blue-lt me-3 stamp">
                                    <x-lucide-tag class="icon" />
                                </span>
                                <div class="text-truncate">
                                    <div class="text-secondary text-truncate small">
                                        {{ $items->first()->budgetGroup->name ?? 'Tanpa Kelompok' }}</div>
                                    <div class="mb-0 h3">Rp {{ number_format($items->sum('amount'), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Aktivitas Pengabdian</h3>
            @if ($this->canManage($proposal))
                <div class="card-actions">
                    <button wire:click="create" class="btn btn-primary">
                        <x-lucide-plus class="me-2 icon" />
                        Tambah Catatan
                    </button>
                </div>
            @endif
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th width="15%">Tanggal</th>
                        <th>Aktivitas</th>
                        <th width="12%">Kelompok RAB</th>
                        <th width="12%">Nominal</th>
                        <th width="12%">Progres</th>
                        <th width="12%">Bukti</th>
                        @if ($this->canManage($proposal))
                            <th width="10%">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes_list as $note)
                        <tr wire:key="note-{{ $note->id }}">
                            <td class="text-secondary align-top">{{ $note->activity_date->format('d/m/Y') }}</td>
                            <td class="align-top">
                                <div class="fw-bold">{{ $note->activity_description }}</div>
                                @if ($note->notes)
                                    <small class="d-block mt-1 text-muted italic">
                                        <x-lucide-info class="icon-inline me-1 icon"
                                            style="width: 12px; height: 12px;" />
                                        {{ $note->notes }}
                                    </small>
                                @endif
                            </td>
                            <td class="align-top">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="w-100 progress progress-xs">
                                        <div class="bg-blue progress-bar"
                                            style="width: {{ $note->progress_percentage }}%"></div>
                                    </div>
                                    <span class="small">{{ $note->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td class="align-top">
                                @if ($note->budgetGroup)
                                    <span class="bg-blue-lt badge">{{ $note->budgetGroup->name }}</span>
                                @else
                                    <span class="text-muted small">Tanpa RAB</span>
                                @endif
                            </td>
                            <td class="align-top">
                                @if ($note->amount)
                                    <div class="fw-bold">Rp {{ number_format($note->amount, 0, ',', '.') }}</div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="align-top">
                                @if ($note->media->isNotEmpty())
                                    <div class="d-flex flex-column gap-1">
                                        @foreach ($note->media as $media)
                                            <div class="d-flex align-items-center gap-1">
                                                @if (str_starts_with($media->mime_type, 'image/'))
                                                    <a href="#"
                                                        @click.prevent="window.ImagePreviewModal.show('image-preview-modal', '{{ $media->getUrl() }}')"
                                                        class="text-decoration-none text-truncate"
                                                        style="max-width: 150px;" title="{{ $media->file_name }}">
                                                        <x-lucide-image class="icon-inline me-1 text-muted icon" />
                                                        <small>{{ $media->file_name }}</small>
                                                    </a>
                                                @else
                                                    <a href="{{ $media->getUrl() }}" target="_blank"
                                                        class="text-decoration-none text-truncate"
                                                        style="max-width: 150px;" title="{{ $media->file_name }}">
                                                        <x-lucide-file-text class="icon-inline me-1 text-muted icon" />
                                                        <small>{{ $media->file_name }}</small>
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            @if ($this->canManage($proposal))
                                <td class="align-top">
                                    <div class="dropdown">
                                        <button class="align-text-top btn btn-sm dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            Aksi
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="edit('{{ $note->id }}')">
                                                <x-lucide-pencil class="me-2 icon" />
                                                Edit
                                            </a>
                                            <a class="text-danger dropdown-item" href="#"
                                                wire:confirm="Apakah Anda yakin ingin menghapus catatan ini?"
                                                wire:click.prevent="delete('{{ $note->id }}')">
                                                <x-lucide-trash-2 class="me-2 icon" />
                                                Hapus
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-muted text-center">
                                Belum ada catatan aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <x-tabler.modal id="daily-note-modal" :title="$editingId ? 'Edit Catatan' : 'Tambah Catatan Baru'" wire:ignore.self size="xl">
        <form wire:submit="save">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label required">Tanggal Kegiatan</label>
                        <input type="date" class="form-control @error('activity_date') is-invalid @enderror"
                            wire:model="activity_date">
                        @error('activity_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label">Kelompok RAB (Opsional)</label>
                                <select class="form-select @error('budget_group_id') is-invalid @enderror"
                                    wire:model="budget_group_id">
                                    <option value="">-- Pilih Kelompok RAB --</option>
                                    @foreach ($budget_groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                                @error('budget_group_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih jika aktivitas ini menggunakan anggaran RAB</small>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Nominal Digunakan (Rp)</label>
                                <div class="input-group" x-data="moneyInputSingle('amount')">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" x-model="display" x-ref="input" @focus="handleFocus"
                                        @input="handleInput"
                                        class="form-control @error('amount') is-invalid @enderror" placeholder="0">
                                </div>
                                @error('amount')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Deskripsi Aktivitas</label>
                        <textarea class="form-control @error('activity_description') is-invalid @enderror" wire:model="activity_description"
                            rows="4" placeholder="Jelaskan aktivitas pengabdian yang dilakukan..."></textarea>
                        @error('activity_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Progres (%)</label>
                        <div class="align-items-center row g-2">
                            <div class="col">
                                <input type="range" class="form-range" min="0" max="100"
                                    step="5" wire:model.live="progress_percentage">
                            </div>
                            <div class="col-auto">
                                <span class="bg-blue-lt badge">{{ $progress_percentage }}%</span>
                            </div>
                        </div>
                        @error('progress_percentage')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan (Opsional)</label>
                        <input type="text" class="form-control @error('notes') is-invalid @enderror"
                            wire:model="notes" placeholder="Misal: Kendala, respon mitra, dll.">
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bukti Dukung (Foto/Dokumen)</label>
                        <input type="file" class="form-control @error('evidence.*') is-invalid @enderror"
                            wire:model="evidence" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        @error('evidence.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Bisa upload lebih dari satu file (Max 5MB/file). Format: PDF, DOC,
                            DOCX, JPG, PNG</small>

                        <div wire:loading wire:target="evidence" class="mt-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-1 small">Uploading...</span>
                        </div>
                    </div>
                </div>

                <div class="border-start col-md-6">
                    <label class="mb-3 form-label">Preview File</label>

                    <div class="d-flex flex-column gap-3">
                        {{-- New Uploads Preview --}}
                        @if ($evidence)
                            <div class="bg-muted-lt card">
                                <div class="p-2 card-body">
                                    <div class="mb-2 small fw-bold">File Baru:</div>
                                    <div class="row g-2">
                                        @foreach ($evidence as $index => $file)
                                            <div class="col-12">
                                                <div
                                                    class="d-flex align-items-center justify-content-between bg-body-tertiary p-2 border rounded">
                                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                        @if (str_starts_with($file->getMimeType(), 'image/'))
                                                            <img src="{{ $file->temporaryUrl() }}"
                                                                class="rounded object-cover"
                                                                style="width: 40px; height: 40px;">
                                                        @else
                                                            <x-lucide-file class="text-muted icon" />
                                                        @endif
                                                        <div class="text-truncate small"
                                                            title="{{ $file->getClientOriginalName() }}">
                                                            {{ $file->getClientOriginalName() }}
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        wire:click="removeEvidence({{ $index }})"
                                                        wire:confirm="Batalkan upload file ini?"
                                                        class="btn btn-icon btn-sm btn-ghost-danger"
                                                        title="Hapus file">
                                                        <x-lucide-x class="icon" />
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Existing Files --}}
                        @if ($editingId)
                            @php
                                $editingNote = $notes_list->firstWhere('id', $editingId);
                            @endphp
                            @if ($editingNote && $editingNote->media->isNotEmpty())
                                <div>
                                    <div class="mb-2 small fw-bold">File Tersimpan:</div>
                                    <div class="row g-2">
                                        @foreach ($editingNote->media as $media)
                                            <div class="col-12">
                                                <div
                                                    class="d-flex align-items-center justify-content-between p-2 border rounded">
                                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                        @if (str_starts_with($media->mime_type, 'image/'))
                                                            <a href="{{ $media->getUrl() }}" target="_blank">
                                                                <img src="{{ $media->getUrl() }}"
                                                                    class="rounded object-cover"
                                                                    style="width: 40px; height: 40px;">
                                                            </a>
                                                        @else
                                                            <a href="{{ $media->getUrl() }}" target="_blank">
                                                                <x-lucide-file class="text-muted icon" />
                                                            </a>
                                                        @endif
                                                        <div class="text-truncate small"
                                                            title="{{ $media->file_name }}">
                                                            {{ $media->file_name }}
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        wire:click="deleteEvidence('{{ $media->id }}')"
                                                        wire:confirm="Hapus file ini?"
                                                        class="btn btn-icon btn-sm btn-ghost-danger"
                                                        title="Hapus file">
                                                        <x-lucide-x class="icon" />
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if (!$evidence && (!$editingId || ($editingNote && $editingNote->media->isEmpty())))
                            <div class="py-5 text-muted text-center">
                                <x-lucide-file-up class="opacity-50 mb-2 icon icon-lg" />
                                <div class="small">Belum ada file yang dipilih/diupload</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal"
                    wire:click="cancelEdit">
                    Batal
                </button>
                <button type="submit" class="ms-auto btn btn-primary">
                    <x-lucide-save class="me-2 icon" />
                    Simpan
                </button>
            </div>
        </form>
    </x-tabler.modal>

    <x-tabler.modal-image id="image-preview-modal" title="Preview Bukti" downloadable="true" />
</div>
