<x-slot:title>Catatan Harian PKM: {{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>Catatan Harian (Logbook)</x-slot:pageTitle>
<x-slot:pageSubtitle>{{ $proposal->title }}</x-slot:pageSubtitle>
<x-slot:pageActions>
    <a href="{{ route('community-service.daily-note.index') }}" class="btn btn-outline-secondary" wire:navigate>
        <x-lucide-arrow-left class="icon me-2" />
        Kembali
    </a>
</x-slot:pageActions>

<div>
    <x-tabler.alert />

    <div class="row g-3">
        <!-- Form Section -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $editingId ? 'Edit Catatan' : 'Tambah Catatan Baru' }}</h3>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
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
                                placeholder="Jelaskan aktivitas pengabdian yang dilakukan..."></textarea>
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
                                wire:model="notes" placeholder="Misal: Kendala, respon mitra, dll.">
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bukti Dukung (Foto/Dokumen)</label>
                            <input type="file" class="form-control @error('evidence') is-invalid @enderror" 
                                wire:model="evidence">
                            @error('evidence') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Maksimal 5MB</small>
                            
                            <div wire:loading wire:target="evidence" class="mt-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="small ms-1">Uploading...</span>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <x-lucide-save class="icon me-2" />
                                Simpan
                            </button>
                            @if($editingId)
                                <button type="button" wire:click="cancelEdit" class="btn btn-outline-secondary w-100">
                                    Batal
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Aktivitas Pengabdian</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th width="15%">Tanggal</th>
                                <th>Aktivitas</th>
                                <th width="15%">Progres</th>
                                <th width="10%">Bukti</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notes_list as $note)
                                <tr wire:key="note-{{ $note->id }}">
                                    <td class="text-secondary">{{ $note->activity_date->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $note->activity_description }}</div>
                                        @if($note->notes)
                                            <small class="text-muted d-block mt-1 italic">
                                                <x-lucide-info class="icon icon-inline me-1" style="width: 12px; height: 12px;" />
                                                {{ $note->notes }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress progress-xs w-100">
                                                <div class="progress-bar bg-blue" style="width: {{ $note->progress_percentage }}%"></div>
                                            </div>
                                            <span class="small">{{ $note->progress_percentage }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($note->hasMedia('evidence'))
                                            <a href="{{ $note->getFirstMediaUrl('evidence') }}" target="_blank" 
                                                class="btn btn-icon btn-sm btn-outline-primary" title="Unduh Bukti">
                                                <x-lucide-download class="icon" />
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <button wire:click="edit('{{ $note->id }}')" class="btn btn-icon btn-sm btn-outline-info">
                                                <x-lucide-pencil class="icon" />
                                            </button>
                                            <button wire:confirm="Apakah Anda yakin ingin menghapus catatan ini?" 
                                                wire:click="delete('{{ $note->id }}')" 
                                                class="btn btn-icon btn-sm btn-outline-danger">
                                                <x-lucide-trash-2 class="icon" />
                                            </button>
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
        </div>
    </div>
</div>
