<x-slot:title>Catatan Harian: {{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>Catatan Harian (Logbook)</x-slot:pageTitle>
<x-slot:pageSubtitle>{{ $proposal->title }}</x-slot:pageSubtitle>
<x-slot:pageActions>
    <a href="{{ route('research.daily-note.index') }}" class="btn btn-outline-secondary" wire:navigate>
        <x-lucide-arrow-left class="icon me-2" />
        Kembali
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
                    Gunakan fitur ini untuk mencatat aktivitas harian penelitian Anda. Catatan ini akan menjadi bukti pelaksanaan kegiatan dan digunakan dalam pemantauan kemajuan penelitian. Lampirkan foto atau dokumen sebagai bukti dukung kegiatan.
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Aktivitas</h3>
            <div class="card-actions">
                <button wire:click="create" class="btn btn-primary">
                    <x-lucide-plus class="icon me-2" />
                    Tambah Catatan
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th width="15%">Tanggal</th>
                        <th>Aktivitas</th>
                        <th width="15%">Progres</th>
                        <th width="15%">Bukti</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes_list as $note)
                        <tr wire:key="note-{{ $note->id }}">
                            <td class="text-secondary align-top">{{ $note->activity_date->format('d/m/Y') }}</td>
                            <td class="align-top">
                                <div class="fw-bold">{{ $note->activity_description }}</div>
                                @if($note->notes)
                                    <small class="text-muted d-block mt-1 italic">
                                        <x-lucide-info class="icon icon-inline me-1" style="width: 12px; height: 12px;" />
                                        {{ $note->notes }}
                                    </small>
                                @endif
                            </td>
                            <td class="align-top">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress progress-xs w-100">
                                        <div class="progress-bar bg-blue" style="width: {{ $note->progress_percentage }}%"></div>
                                    </div>
                                    <span class="small">{{ $note->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td class="align-top">
                                @if($note->media->isNotEmpty())
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($note->media as $media)
                                            <div class="d-flex align-items-center gap-1">
                                                <a href="{{ $media->getUrl() }}" target="_blank" 
                                                    class="text-decoration-none text-truncate" style="max-width: 150px;" title="{{ $media->file_name }}">
                                                    <x-lucide-file-text class="icon icon-inline me-1 text-muted" />
                                                    <small>{{ $media->file_name }}</small>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="align-top">
                                <div class="dropdown">
                                    <button class="btn btn-sm dropdown-toggle align-text-top" data-bs-toggle="dropdown">
                                        Aksi
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#" wire:click.prevent="edit('{{ $note->id }}')">
                                            <x-lucide-pencil class="icon me-2" />
                                            Edit
                                        </a>
                                        <a class="dropdown-item text-danger" href="#" 
                                            wire:confirm="Apakah Anda yakin ingin menghapus catatan ini?" 
                                            wire:click.prevent="delete('{{ $note->id }}')">
                                            <x-lucide-trash-2 class="icon me-2" />
                                            Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
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
                        @error('activity_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Deskripsi Aktivitas</label>
                        <textarea class="form-control @error('activity_description') is-invalid @enderror" 
                            wire:model="activity_description" rows="4" 
                            placeholder="Jelaskan aktivitas yang dilakukan..."></textarea>
                        @error('activity_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Progres (%)</label>
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <input type="range" class="form-range" min="0" max="100" step="5" 
                                    wire:model.live="progress_percentage">
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-blue-lt">{{ $progress_percentage }}%</span>
                            </div>
                        </div>
                        @error('progress_percentage') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan (Opsional)</label>
                        <input type="text" class="form-control @error('notes') is-invalid @enderror" 
                            wire:model="notes" placeholder="Misal: Kendala, cuaca, dll.">
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bukti Dukung (Foto/Dokumen)</label>
                        <input type="file" class="form-control @error('evidence.*') is-invalid @enderror" 
                            wire:model="evidence" multiple>
                        @error('evidence.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">Bisa upload lebih dari satu file (Max 5MB/file)</small>
                        
                        <div wire:loading wire:target="evidence" class="mt-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="small ms-1">Uploading...</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 border-start">
                    <label class="form-label mb-3">Preview File</label>
                    
                    <div class="d-flex flex-column gap-3">
                        {{-- New Uploads Preview --}}
                        @if($evidence)
                            <div class="card bg-muted-lt">
                                <div class="card-body p-2">
                                    <div class="small fw-bold mb-2">File Baru:</div>
                                    <div class="row g-2">
                                        @foreach($evidence as $file)
                                            <div class="col-12">
                                                <div class="d-flex align-items-center gap-2 p-2 border rounded bg-white">
                                                    @if(str_starts_with($file->getMimeType(), 'image/'))
                                                        <img src="{{ $file->temporaryUrl() }}" class="rounded object-cover" style="width: 40px; height: 40px;">
                                                    @else
                                                        <x-lucide-file class="icon text-muted" />
                                                    @endif
                                                    <div class="text-truncate flex-fill small" title="{{ $file->getClientOriginalName() }}">
                                                        {{ $file->getClientOriginalName() }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Existing Files --}}
                        @if($editingId)
                            @php
                                $editingNote = $notes_list->firstWhere('id', $editingId);
                            @endphp
                            @if($editingNote && $editingNote->media->isNotEmpty())
                                <div>
                                    <div class="small fw-bold mb-2">File Tersimpan:</div>
                                    <div class="row g-2">
                                        @foreach($editingNote->media as $media)
                                            <div class="col-12">
                                                <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                        @if(str_starts_with($media->mime_type, 'image/'))
                                                            <a href="{{ $media->getUrl() }}" target="_blank">
                                                                <img src="{{ $media->getUrl() }}" class="rounded object-cover" style="width: 40px; height: 40px;">
                                                            </a>
                                                        @else
                                                            <a href="{{ $media->getUrl() }}" target="_blank">
                                                                <x-lucide-file class="icon text-muted" />
                                                            </a>
                                                        @endif
                                                        <div class="text-truncate small" title="{{ $media->file_name }}">
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

                        @if(!$evidence && (!$editingId || ($editingNote && $editingNote->media->isEmpty())))
                            <div class="text-center py-5 text-muted">
                                <x-lucide-file-up class="icon icon-lg mb-2 opacity-50" />
                                <div class="small">Belum ada file yang dipilih/diupload</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal" wire:click="cancelEdit">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary ms-auto">
                    <x-lucide-save class="icon me-2" />
                    Simpan
                </button>
            </div>
        </form>
    </x-tabler.modal>
</div>
