<x-slot:title>Laporan Kemajuan - {{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>Laporan Kemajuan</x-slot:pageTitle>
<x-slot:pageSubtitle>{{ $proposal->title }}</x-slot:pageSubtitle>
<x-slot:pageActions>
    <a href="{{ route('research.progress-report.index') }}" class="btn-outline-secondary btn" wire:navigate>
        <x-lucide-arrow-left class="icon" />
        Kembali
    </a>
</x-slot:pageActions>

<div x-data="{ activeTab: 'ringkasan' }">
    <x-tabler.alert />

    <!-- Tab Navigation -->
    <div class="mb-3 card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link" :class="{ 'active': activeTab === 'ringkasan' }"
                        @click.prevent="activeTab = 'ringkasan'" href="#">
                        📝 Ringkasan & Kata Kunci
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :class="{ 'active': activeTab === 'luaran-wajib' }"
                        @click.prevent="activeTab = 'luaran-wajib'" href="#">
                        📚 Luaran Wajib ({{ $proposal->outputs->where('category', 'wajib')->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :class="{ 'active': activeTab === 'luaran-tambahan' }"
                        @click.prevent="activeTab = 'luaran-tambahan'" href="#">
                        📖 Luaran Tambahan ({{ $proposal->outputs->where('category', 'tambahan')->count() }})
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->

    <!-- Tab 1: Ringkasan & Keywords -->
    <div x-show="activeTab === 'ringkasan'" class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label required">Ringkasan Terkini</label>
                <textarea wire:model="summaryUpdate" rows="8" class="form-control"
                    placeholder="Masukkan ringkasan kemajuan penelitian..."></textarea>
                @error('summaryUpdate')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Kata Kunci (Keywords)</label>
                <select wire:model="selectedKeywords" multiple class="form-select" size="8">
                    @foreach ($allKeywords as $keyword)
                        <option value="{{ $keyword->id }}">{{ $keyword->name }}</option>
                    @endforeach
                </select>
                <small class="form-hint">Tekan Ctrl/Cmd untuk memilih lebih dari satu</small>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Tahun Pelaporan</label>
                    <input type="number" wire:model="reportingYear" class="form-control" min="2020" max="2030" />
                    @error('reportingYear')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Periode</label>
                    <select wire:model="reportingPeriod" class="form-select">
                        <option value="semester_1">Semester 1</option>
                        <option value="semester_2">Semester 2</option>
                        <option value="annual">Tahunan</option>
                    </select>
                    @error('reportingPeriod')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Luaran Wajib -->
    <div x-show="activeTab === 'luaran-wajib'">
        @forelse ($proposal->outputs->where('category', 'wajib') as $output)
            <div class="mb-3 card" wire:key="mandatory-{{ $output->id }}">
                <div class="card-header">
                    <h4 class="card-title">
                        {{ $output->type }} - Tahun {{ $output->output_year }}
                    </h4>
                    <small class="text-muted">Target: {{ $output->target_status }}</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Status Type -->
                        <div class="col-md-6">
                            <label class="form-label required">Status Publikasi</label>
                            <select wire:model="mandatoryOutputs.{{ $output->id }}.status_type" class="form-select">
                                <option value="">Pilih Status</option>
                                <option value="published">Published</option>
                                <option value="accepted">Accepted</option>
                                <option value="under_review">Under Review</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Author Status -->
                        <div class="col-md-6">
                            <label class="form-label required">Status Penulis</label>
                            <select wire:model="mandatoryOutputs.{{ $output->id }}.author_status" class="form-select">
                                <option value="">Pilih Status</option>
                                <option value="first_author">First Author</option>
                                <option value="co_author">Co-Author</option>
                                <option value="corresponding_author">Corresponding Author</option>
                            </select>
                        </div>

                        <!-- Journal Title -->
                        <div class="col-md-12">
                            <label class="form-label required">Judul Jurnal</label>
                            <input type="text" wire:model="mandatoryOutputs.{{ $output->id }}.journal_title"
                                class="form-control" placeholder="Masukkan judul jurnal" />
                        </div>

                        <!-- ISSN / E-ISSN -->
                        <div class="col-md-6">
                            <label class="form-label">ISSN</label>
                            <input type="text" wire:model="mandatoryOutputs.{{ $output->id }}.issn"
                                class="form-control" placeholder="1234-5678" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-ISSN</label>
                            <input type="text" wire:model="mandatoryOutputs.{{ $output->id }}.eissn"
                                class="form-control" placeholder="1234-5678" />
                        </div>

                        <!-- Indexing Body -->
                        <div class="col-md-6">
                            <label class="form-label">Lembaga Pengindex</label>
                            <input type="text" wire:model="mandatoryOutputs.{{ $output->id }}.indexing_body"
                                class="form-control" placeholder="Scopus, WoS, Sinta, dll" />
                        </div>

                        <!-- Journal URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Jurnal</label>
                            <input type="url" wire:model="mandatoryOutputs.{{ $output->id }}.journal_url"
                                class="form-control" placeholder="https://" />
                        </div>

                        <!-- Article Title -->
                        <div class="col-md-12">
                            <label class="form-label required">Judul Artikel</label>
                            <input type="text" wire:model="mandatoryOutputs.{{ $output->id }}.article_title"
                                class="form-control" placeholder="Masukkan judul artikel" />
                        </div>

                        <!-- Publication Year -->
                        <div class="col-md-3">
                            <label class="form-label required">Tahun Terbit</label>
                            <input type="number" wire:model="mandatoryOutputs.{{ $output->id }}.publication_year"
                                class="form-control" min="2000" max="2030" />
                        </div>

                        <!-- Volume -->
                        <div class="col-md-3">
                            <label class="form-label">Volume</label>
                            <input type="text" wire:model="mandatoryOutputs.{{ $output->id }}.volume"
                                class="form-control" placeholder="Vol. 1" />
                        </div>

                        <!-- Issue Number -->
                        <div class="col-md-3">
                            <label class="form-label">Nomor</label>
                            <input type="text" wire:model="mandatoryOutputs.{{ $output->id }}.issue_number"
                                class="form-control" placeholder="No. 1" />
                        </div>

                        <!-- Pages -->
                        <div class="col-md-3">
                            <label class="form-label">Halaman</label>
                            <div class="input-group">
                                <input type="number" wire:model="mandatoryOutputs.{{ $output->id }}.page_start"
                                    class="form-control" placeholder="1" />
                                <span class="input-group-text">-</span>
                                <input type="number" wire:model="mandatoryOutputs.{{ $output->id }}.page_end"
                                    class="form-control" placeholder="10" />
                            </div>
                        </div>

                        <!-- Article URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Artikel</label>
                            <input type="url" wire:model="mandatoryOutputs.{{ $output->id }}.article_url"
                                class="form-control" placeholder="https://" />
                        </div>

                        <!-- DOI -->
                        <div class="col-md-6">
                            <label class="form-label">DOI Artikel</label>
                            <input type="text" wire:model="mandatoryOutputs.{{ $output->id }}.doi"
                                class="form-control" placeholder="10.xxxx/xxxxx" />
                        </div>

                        <!-- File Upload -->
                        <div class="col-md-12">
                            <label class="form-label">Dokumen Artikel (PDF)</label>
                            <input type="file" wire:model="tempMandatoryFiles.{{ $output->id }}" class="form-control"
                                accept=".pdf" />
                            @if (isset($mandatoryOutputs[$output->id]['document_file']) && $mandatoryOutputs[$output->id]['document_file'])
                                <div class="mt-2">
                                    <small class="text-success">
                                        <x-lucide-check class="icon icon-sm" />
                                        File tersimpan
                                    </small>
                                </div>
                            @endif
                            <div wire:loading wire:target="tempMandatoryFiles.{{ $output->id }}">
                                <small class="text-muted">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Uploading...
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="py-8 text-center card-body">
                    <x-lucide-inbox class="mb-3 text-secondary icon icon-lg" />
                    <p class="text-muted">Tidak ada luaran wajib yang direncanakan.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Tab 3: Luaran Tambahan -->
    <div x-show="activeTab === 'luaran-tambahan'">
        @forelse ($proposal->outputs->where('category', 'tambahan') as $output)
            <div class="mb-3 card" wire:key="additional-{{ $output->id }}">
                <div class="card-header">
                    <h4 class="card-title">
                        {{ $output->type }} - Tahun {{ $output->output_year }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Status -->
                        <div class="col-md-12">
                            <label class="form-label required">Status</label>
                            <select wire:model="additionalOutputs.{{ $output->id }}.status" class="form-select">
                                <option value="">Pilih Status</option>
                                <option value="review">Review</option>
                                <option value="editing">Editing</option>
                                <option value="published">Terbit</option>
                            </select>
                        </div>

                        <!-- Book Title -->
                        <div class="col-md-12">
                            <label class="form-label required">Judul Buku</label>
                            <input type="text" wire:model="additionalOutputs.{{ $output->id }}.book_title"
                                class="form-control" placeholder="Masukkan judul buku" />
                        </div>

                        <!-- Publisher Name -->
                        <div class="col-md-6">
                            <label class="form-label required">Nama Penerbit</label>
                            <input type="text" wire:model="additionalOutputs.{{ $output->id }}.publisher_name"
                                class="form-control" placeholder="Masukkan nama penerbit" />
                        </div>

                        <!-- ISBN -->
                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <input type="text" wire:model="additionalOutputs.{{ $output->id }}.isbn"
                                class="form-control" placeholder="978-xxx-xxx-xxx-x" />
                        </div>

                        <!-- Publication Year -->
                        <div class="col-md-6">
                            <label class="form-label">Tahun Terbit</label>
                            <input type="number" wire:model="additionalOutputs.{{ $output->id }}.publication_year"
                                class="form-control" min="2000" max="2030" />
                        </div>

                        <!-- Total Pages -->
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Halaman</label>
                            <input type="number" wire:model="additionalOutputs.{{ $output->id }}.total_pages"
                                class="form-control" placeholder="100" />
                        </div>

                        <!-- Publisher URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Web Penerbit</label>
                            <input type="url" wire:model="additionalOutputs.{{ $output->id }}.publisher_url"
                                class="form-control" placeholder="https://" />
                        </div>

                        <!-- Book URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Buku</label>
                            <input type="url" wire:model="additionalOutputs.{{ $output->id }}.book_url"
                                class="form-control" placeholder="https://" />
                        </div>

                        <!-- Document File -->
                        <div class="col-md-6">
                            <label class="form-label">Dokumen Buku/Draft</label>
                            <input type="file" wire:model="tempAdditionalFiles.{{ $output->id }}" class="form-control"
                                accept=".pdf" />
                            @if (isset($additionalOutputs[$output->id]['document_file']) && $additionalOutputs[$output->id]['document_file'])
                                <div class="mt-2">
                                    <small class="text-success">
                                        <x-lucide-check class="icon icon-sm" />
                                        File tersimpan
                                    </small>
                                </div>
                            @endif
                            <div wire:loading wire:target="tempAdditionalFiles.{{ $output->id }}">
                                <small class="text-muted">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Uploading...
                                </small>
                            </div>
                        </div>

                        <!-- Publication Certificate -->
                        <div class="col-md-6">
                            <label class="form-label">Surat Keterangan Terbit</label>
                            <input type="file" wire:model="tempAdditionalCerts.{{ $output->id }}" class="form-control"
                                accept=".pdf" />
                            @if (isset($additionalOutputs[$output->id]['publication_certificate']) && $additionalOutputs[$output->id]['publication_certificate'])
                                <div class="mt-2">
                                    <small class="text-success">
                                        <x-lucide-check class="icon icon-sm" />
                                        File tersimpan
                                    </small>
                                </div>
                            @endif
                            <div wire:loading wire:target="tempAdditionalCerts.{{ $output->id }}">
                                <small class="text-muted">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Uploading...
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="py-8 text-center card-body">
                    <x-lucide-inbox class="mb-3 text-secondary icon icon-lg" />
                    <p class="text-muted">Tidak ada luaran tambahan yang direncanakan.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Action Buttons -->
    <div class="mt-3 card">
        <div class="card-body">
            <div class="btn-list justify-content-end">
                <button type="button" wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <x-lucide-save class="icon" /> Simpan Draft
                    </span>
                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Menyimpan...
                    </span>
                </button>
                <button type="button" wire:click="submit" class="btn btn-success" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">
                        <x-lucide-send class="icon" /> Ajukan Laporan
                    </span>
                    <span wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Mengajukan...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
