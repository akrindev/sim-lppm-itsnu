<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Monitoring & Evaluasi (Monev) Internal
                    </h2>
                    <div class="text-muted mt-1">Kelola administrasi hasil monev untuk proposal yang sedang berjalan.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                </span>
                                <input type="text" wire:model.live="search" class="form-control" placeholder="Cari judul atau pengusul...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="typeFilter" class="form-select">
                                <option value="all">Semua Jenis</option>
                                <option value="research">Penelitian</option>
                                <option value="community-service">Pengabdian</option>
                            </select>
                        </div>
                        <div class="col-md-5 text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Unduh Template Kosong
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" wire:click="downloadTemplate('monev_berita_acara_template')">Berita Acara</button>
                                    <button class="dropdown-item" wire:click="downloadTemplate('monev_borang_template')">Borang Monev</button>
                                    <button class="dropdown-item" wire:click="downloadTemplate('monev_rekap_penilaian_template')">Rekap Penilaian</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Pengusul & Judul</th>
                                <th>Jenis</th>
                                <th>Progres</th>
                                <th>Monev Terakhir</th>
                                <th class="text-center">Jumlah Monev</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->proposals as $proposal)
                                @php
                                    $latestMonev = $proposal->monevs->first();
                                    $progress = $latestMonev?->progress_percentage ?? 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $proposal->submitter->name }}</div>
                                        <div class="text-muted text-truncate" style="max-width: 400px;">{{ $proposal->title }}</div>
                                    </td>
                                    <td>
                                        @if ($proposal->detailable_type === \App\Models\Research::class)
                                            <span class="badge bg-blue-lt">Penelitian</span>
                                        @else
                                            <span class="badge bg-green-lt">Pengabdian</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-xs w-100 me-2" style="max-width: 60px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <small>{{ $progress }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $latestMonev?->monev_date->format('d M Y') ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-lt">{{ $proposal->monevs->count() }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-white" wire:click="selectProposal('{{ $proposal->id }}')" wire:loading.attr="disabled" wire:target="selectProposal('{{ $proposal->id }}')">
                                            <span wire:loading.remove wire:target="selectProposal('{{ $proposal->id }}')">Kelola</span>
                                            <span wire:loading wire:target="selectProposal('{{ $proposal->id }}')">
                                                <span class="spinner-border spinner-border-sm me-2"></span>
                                            </span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada proposal dalam tahap pelaksanaan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    {{ $this->proposals->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal List Monev -->
    <div class="modal modal-blur fade @if($showListModal) show @endif" style="@if($showListModal) display: block; @endif" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                @if ($selectedProposal)
                    <div class="modal-header">
                        <h5 class="modal-title">Daftar Monev: {{ $selectedProposal->submitter->name }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showListModal', false)" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Judul Proposal</label>
                                    <div class="text-muted">{{ $selectedProposal->title }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title mb-0">Riwayat Pelaksanaan</h3>
                            <button class="btn btn-primary btn-sm" wire:click="addMonev" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="addMonev">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                    Tambah Monev
                                </span>
                                <span wire:loading wire:target="addMonev">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Memuat...
                                </span>
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-vcenter table-mobile-md card-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th class="text-center">Capaian</th>
                                        <th class="text-center">BA</th>
                                        <th class="text-center">Borang</th>
                                        <th class="text-center">Rekap</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($selectedProposal->monevs as $monev)
                                        <tr>
                                            <td>{{ $monev->monev_date->format('d M Y') }}</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <div class="progress progress-xs w-100 me-2" style="max-width: 60px;">
                                                        <div class="progress-bar bg-primary" style="width: {{ $monev->progress_percentage }}%"></div>
                                                    </div>
                                                    <small>{{ $monev->progress_percentage }}%</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($monev->hasMedia('berita_acara'))
                                                    <a href="{{ $monev->getFirstMediaUrl('berita_acara') }}" target="_blank" class="text-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($monev->hasMedia('borang'))
                                                    <a href="{{ $monev->getFirstMediaUrl('borang') }}" target="_blank" class="text-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($monev->hasMedia('rekap_penilaian'))
                                                    <a href="{{ $monev->getFirstMediaUrl('rekap_penilaian') }}" target="_blank" class="text-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-list flex-nowrap">
                                                    <button class="btn btn-sm btn-white" wire:click="editMonev('{{ $monev->id }}')" wire:loading.attr="disabled" wire:target="editMonev('{{ $monev->id }}')">
                                                        <span wire:loading.remove wire:target="editMonev('{{ $monev->id }}')">Edit</span>
                                                        <span wire:loading wire:target="editMonev('{{ $monev->id }}')">...</span>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="confirm('Yakin ingin menghapus data monev ini?') || event.stopImmediatePropagation()" wire:click="deleteMonev('{{ $monev->id }}')" wire:loading.attr="disabled" wire:target="deleteMonev('{{ $monev->id }}')">
                                                        <span wire:loading.remove wire:target="deleteMonev('{{ $monev->id }}')">Hapus</span>
                                                        <span wire:loading wire:target="deleteMonev('{{ $monev->id }}')">...</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Belum ada data monev.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Form Monev -->
    <div class="modal modal-blur fade @if($showFormModal) show @endif" style="@if($showFormModal) display: block; z-index: 1060; @endif" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content border-primary shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">{{ $selectedMonev ? 'Edit' : 'Tambah' }} Data Monev</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="$set('showFormModal', false)" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Tanggal Pelaksanaan</label>
                                <input type="date" class="form-control" wire:model="monev_date" wire:loading.attr="disabled" wire:target="saveMonev, berita_acara, borang, rekap_penilaian">
                                @error('monev_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Capaian (%)</label>
                                <input type="number" class="form-control" wire:model="progress_percentage" min="0" max="100" wire:loading.attr="disabled" wire:target="saveMonev, berita_acara, borang, rekap_penilaian">
                                @error('progress_percentage') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" wire:model="notes" rows="2"></textarea>
                    </div>
                    <hr>
                    <!-- Berita Acara -->
                    <div class="mb-3" x-data="{ uploading: false, progress: 0 }" 
                         x-on:livewire-upload-start="uploading = true" 
                         x-on:livewire-upload-finish="uploading = false" 
                         x-on:livewire-upload-error="uploading = false" 
                         x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <label class="form-label required">Berita Acara (PDF/DOC)</label>
                        <input type="file" class="form-control" wire:model="berita_acara" wire:loading.attr="disabled" wire:target="saveMonev, berita_acara, borang, rekap_penilaian">
                        <div class="progress progress-xs mt-2" x-show="uploading" style="display: none;">
                            <div class="progress-bar bg-primary" :style="`width: ${progress}%`"></div>
                        </div>
                        @if($selectedMonev && $selectedMonev->hasMedia('berita_acara'))
                            <div class="mt-1 small text-success">Sudah diunggah: <a href="{{ $selectedMonev->getFirstMediaUrl('berita_acara') }}" target="_blank">Lihat</a></div>
                        @endif
                        @error('berita_acara') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Borang -->
                    <div class="mb-3" x-data="{ uploading: false, progress: 0 }" 
                         x-on:livewire-upload-start="uploading = true" 
                         x-on:livewire-upload-finish="uploading = false" 
                         x-on:livewire-upload-error="uploading = false" 
                         x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <label class="form-label required">Borang Monev (PDF/DOC)</label>
                        <input type="file" class="form-control" wire:model="borang" wire:loading.attr="disabled" wire:target="saveMonev, berita_acara, borang, rekap_penilaian">
                        <div class="progress progress-xs mt-2" x-show="uploading" style="display: none;">
                            <div class="progress-bar bg-primary" :style="`width: ${progress}%`"></div>
                        </div>
                        @if($selectedMonev && $selectedMonev->hasMedia('borang'))
                            <div class="mt-1 small text-success">Sudah diunggah: <a href="{{ $selectedMonev->getFirstMediaUrl('borang') }}" target="_blank">Lihat</a></div>
                        @endif
                        @error('borang') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Rekap -->
                    <div class="mb-3" x-data="{ uploading: false, progress: 0 }" 
                         x-on:livewire-upload-start="uploading = true" 
                         x-on:livewire-upload-finish="uploading = false" 
                         x-on:livewire-upload-error="uploading = false" 
                         x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <label class="form-label required">Rekap Penilaian (PDF/DOC)</label>
                        <input type="file" class="form-control" wire:model="rekap_penilaian" wire:loading.attr="disabled" wire:target="saveMonev, berita_acara, borang, rekap_penilaian">
                        <div class="progress progress-xs mt-2" x-show="uploading" style="display: none;">
                            <div class="progress-bar bg-primary" :style="`width: ${progress}%`"></div>
                        </div>
                        @if($selectedMonev && $selectedMonev->hasMedia('rekap_penilaian'))
                            <div class="mt-1 small text-success">Sudah diunggah: <a href="{{ $selectedMonev->getFirstMediaUrl('rekap_penilaian') }}" target="_blank">Lihat</a></div>
                        @endif
                        @error('rekap_penilaian') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" wire:click="$set('showFormModal', false)">Batal</button>
                    <button type="button" class="btn btn-primary ms-auto" wire:click="saveMonev" wire:loading.attr="disabled" wire:target="saveMonev, berita_acara, borang, rekap_penilaian">
                        <span wire:loading.remove wire:target="saveMonev">Simpan Monev</span>
                        <span wire:loading wire:target="saveMonev">Menyimpan...</span>
                        <span wire:loading wire:target="berita_acara, borang, rekap_penilaian">Mengunggah file...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($showListModal)
        <div class="modal-backdrop fade show"></div>
    @endif
    @if($showFormModal)
        <div class="modal-backdrop fade show" style="z-index: 1055;"></div>
    @endif
</div>
