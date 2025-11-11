<x-slot:title>Laporan Akhir - {{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>Laporan Akhir</x-slot:pageTitle>
<x-slot:pageSubtitle>{{ $proposal->title }}</x-slot:pageSubtitle>
<x-slot:pageActions>
    <a href="{{ route('community-service.final-report.index') }}" class="btn-outline-secondary btn" wire:navigate>
        <x-lucide-arrow-left class="icon" />
        Kembali
    </a>
</x-slot:pageActions>

<div>
    <x-tabler.alert />

    <!-- Ringkasan & Kata Kunci -->
    <div class="mb-3 card">
        <div class="card-header">
            <h3 class="card-title"><x-lucide-file-text class="me-2 icon" />Ringkasan & Kata Kunci</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label required">Ringkasan Akhir</label>
                <textarea wire:model="form.summaryUpdate" rows="8" class="form-control"
                    placeholder="Masukkan ringkasan akhir penelitian..." @disabled(!$canEdit)></textarea>
                @error('form.summaryUpdate')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Kata Kunci (Keywords)</label>
                <input type="text" wire:model="form.keywordsInput" class="form-control"
                    placeholder="Contoh: AI; Machine Learning; IoT" /> @disabled(!$canEdit) />
                <small class="form-hint">Pisahkan kata kunci dengan titik koma (;). Contoh: AI; Machine Learning; Deep
                    Learning</small>
                @error('form.keywordsInput')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label required">Tahun Pelaporan</label>
                <input type="number" wire:model="form.reportingYear" class="form-control" min="2020"
                    max="2030" /> @disabled(!$canEdit) />
                @error('form.reportingYear')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>

    <!-- Dokumen Laporan Akhir -->
    <div class="mb-3 card">
        <div class="card-header">
            <h3 class="card-title"><x-lucide-file-text class="me-2 icon" />Dokumen Laporan Akhir</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">File Substansi Laporan (PDF)</label>
                <input type="file" wire:model="form.substanceFile"
                    class="form-control @error('form.substanceFile') is-invalid @enderror" accept=".pdf"
                    @disabled(!$canEdit) />
                @error('form.substanceFile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-hint">Maksimal 10MB, format PDF</small>

                <div wire:loading wire:target="form.substanceFile">
                    <small class="text-muted">
                        <span class="me-2 spinner-border spinner-border-sm"></span>
                        Uploading...
                    </small>
                </div>

                @if ($progressReport && $progressReport->hasMedia('substance_file'))
                    @php
                        $media = $progressReport->getFirstMedia('substance_file');
                    @endphp
                    <div class="mt-2 mb-0 alert alert-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <x-lucide-file-check class="me-2 text-success icon" />
                                <strong>{{ $media->name }}</strong>
                                <small class="ms-2 text-muted">({{ $media->human_readable_size }})</small>
                            </div>
                            <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-sm btn-primary">
                                <x-lucide-eye class="icon" /> Lihat
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label">File Realisasi Keterlibatan (PDF/DOCX)</label>
                <input type="file" wire:model="form.realizationFile"
                    class="form-control @error('form.realizationFile') is-invalid @enderror" accept=".pdf,.docx"
                    @disabled(!$canEdit) />
                @error('form.realizationFile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-hint">Maksimal 10MB, format PDF atau DOCX</small>

                <div wire:loading wire:target="form.realizationFile">
                    <small class="text-muted">
                        <span class="me-2 spinner-border spinner-border-sm"></span>
                        Uploading...
                    </small>
                </div>

                @if ($progressReport && $progressReport->hasMedia('realization_file'))
                    @php
                        $media = $progressReport->getFirstMedia('realization_file');
                    @endphp
                    <div class="mt-2 mb-0 alert alert-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <x-lucide-file-check class="me-2 text-success icon" />
                                <strong>{{ $media->name }}</strong>
                                <small class="ms-2 text-muted">({{ $media->human_readable_size }})</small>
                            </div>
                            <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-sm btn-primary">
                                <x-lucide-eye class="icon" /> Lihat
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label">File Presentasi Hasil (PDF/PPTX)</label>
                <input type="file" wire:model="form.presentationFile"
                    class="form-control @error('form.presentationFile') is-invalid @enderror" accept=".pdf,.pptx"
                    @disabled(!$canEdit) />
                @error('form.presentationFile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-hint">Maksimal 50MB, format PDF atau PPTX</small>

                <div wire:loading wire:target="form.presentationFile">
                    <small class="text-muted">
                        <span class="me-2 spinner-border spinner-border-sm"></span>
                        Uploading...
                    </small>
                </div>

                @if ($progressReport && $progressReport->hasMedia('presentation_file'))
                    @php
                        $media = $progressReport->getFirstMedia('presentation_file');
                    @endphp
                    <div class="mt-2 mb-0 alert alert-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <x-lucide-file-check class="me-2 text-success icon" />
                                <strong>{{ $media->name }}</strong>
                                <small class="ms-2 text-muted">({{ $media->human_readable_size }})</small>
                            </div>
                            <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-sm btn-primary">
                                <x-lucide-eye class="icon" /> Lihat
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Luaran Wajib -->
    <div class="mb-3 card">
        <div class="card-header">
            <h3 class="card-title"><x-lucide-book-open class="me-2 icon" />Luaran Wajib</h3>
        </div>
        <div class="card-body">
            @php
                $wajibs = $proposal->outputs->where('category', 'Wajib');
            @endphp

            @if ($wajibs->isNotEmpty())
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Luaran</th>
                                <th>Tahun Target</th>
                                <th>Target Status</th>
                                <th>Status Input</th>
                                <th>Dokumen</th>
                                <th class="w-1">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wajibs as $index => $output)
                                @php
                                    $mandatoryOutput = $progressReport
                                        ? $progressReport
                                            ->mandatoryOutputs()
                                            ->where('proposal_output_id', $output->id)
                                            ->first()
                                        : null;
                                @endphp
                                <tr wire:key="wajib-row-{{ $output->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $output->type }}</div>
                                    </td>
                                    <td>{{ $output->output_year }}</td>
                                    <td>
                                        <x-tabler.badge variant="outline">
                                            {{ $output->target_status }}
                                        </x-tabler.badge>
                                    </td>
                                    <td>
                                        @php
                                            $hasData =
                                                isset($form->mandatoryOutputs[$output->id]['status_type']) &&
                                                !empty($form->mandatoryOutputs[$output->id]['status_type']);
                                        @endphp
                                        @if ($hasData)
                                            <x-tabler.badge color="success">
                                                <x-lucide-check class="icon icon-sm" />
                                                Sudah Diisi
                                            </x-tabler.badge>
                                        @else
                                            <x-tabler.badge color="secondary">
                                                Belum Diisi
                                            </x-tabler.badge>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($mandatoryOutput && $mandatoryOutput->hasMedia('journal_article'))
                                            @php
                                                $media = $mandatoryOutput->getFirstMedia('journal_article');
                                            @endphp
                                            <a href="{{ $media->getUrl() }}" target="_blank"
                                                class="btn btn-sm btn-success">
                                                <x-lucide-file-check class="icon icon-sm" />
                                                Lihat Dokumen
                                            </a>
                                        @else
                                            <span class="text-muted">
                                                <x-lucide-file-x class="icon icon-sm" />
                                                Belum Upload
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($canEdit)
                                            <button type="button"
                                                wire:click="editMandatoryOutput({{ $output->id }})"
                                                class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modalMandatoryOutput">
                                                <x-lucide-pencil class="icon" />
                                            </button>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-4 text-muted text-center">
                    <x-lucide-inbox class="mb-2 icon icon-lg" />
                    <p>Tidak ada luaran wajib yang direncanakan</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Luaran Tambahan -->
    <div class="mb-3 card">
        <div class="card-header">
            <h3 class="card-title"><x-lucide-book class="me-2 icon" />Luaran Tambahan</h3>
        </div>
        <div class="card-body">
            @php
                $tambahans = $proposal->outputs->where('category', 'Tambahan');
            @endphp

            @if ($tambahans->isNotEmpty())
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Luaran</th>
                                <th>Tahun Target</th>
                                <th>Status Input</th>
                                <th>Dokumen</th>
                                <th class="w-1">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tambahans as $index => $output)
                                @php
                                    $additionalOutput = $progressReport
                                        ? $progressReport
                                            ->additionalOutputs()
                                            ->where('proposal_output_id', $output->id)
                                            ->first()
                                        : null;
                                @endphp
                                <tr wire:key="tambahan-row-{{ $output->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $output->type }}</div>
                                    </td>
                                    <td>{{ $output->output_year }}</td>
                                    <td>
                                        @php
                                            $hasData =
                                                isset($form->additionalOutputs[$output->id]['status']) &&
                                                !empty($form->additionalOutputs[$output->id]['status']);
                                        @endphp
                                        @if ($hasData)
                                            <x-tabler.badge color="success">
                                                <x-lucide-check class="icon icon-sm" />
                                                Sudah Diisi
                                            </x-tabler.badge>
                                        @else
                                            <x-tabler.badge color="secondary">
                                                Belum Diisi
                                            </x-tabler.badge>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($additionalOutput)
                                            <div class="d-flex gap-2">
                                                @if ($additionalOutput->hasMedia('book_document'))
                                                    @php
                                                        $media = $additionalOutput->getFirstMedia('book_document');
                                                    @endphp
                                                    <a href="{{ $media->getUrl() }}" target="_blank"
                                                        class="btn btn-sm btn-success">
                                                        <x-lucide-book class="icon icon-sm" />
                                                        Buku
                                                    </a>
                                                @endif

                                                @if ($additionalOutput->hasMedia('publication_certificate'))
                                                    @php
                                                        $media = $additionalOutput->getFirstMedia(
                                                            'publication_certificate',
                                                        );
                                                    @endphp
                                                    <a href="{{ $media->getUrl() }}" target="_blank"
                                                        class="btn btn-sm btn-info">
                                                        <x-lucide-award class="icon icon-sm" />
                                                        Sertifikat
                                                    </a>
                                                @endif
                                            </div>

                                            @if (!$additionalOutput->hasMedia('book_document') && !$additionalOutput->hasMedia('publication_certificate'))
                                                <span class="text-muted">
                                                    <x-lucide-file-x class="icon icon-sm" />
                                                    Belum Upload
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">
                                                <x-lucide-file-x class="icon icon-sm" />
                                                Belum Upload
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($canEdit)
                                            <button type="button"
                                                wire:click="editAdditionalOutput({{ $output->id }})"
                                                class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modalAdditionalOutput">
                                                <x-lucide-pencil class="icon" />
                                            </button>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-4 text-muted text-center">
                    <x-lucide-inbox class="mb-2 icon icon-lg" />
                    <p>Tidak ada luaran tambahan yang direncanakan</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    @if ($canEdit)
        <div class="card">
            <div class="card-body">
                <div class="justify-content-end btn-list">
                    <button type="button" wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <x-lucide-save class="icon" /> Simpan Draft
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="me-2 spinner-border spinner-border-sm"></span>
                            Menyimpan...
                        </span>
                    </button>
                    <button type="button" wire:click="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submit">
                            <x-lucide-send class="icon" /> Ajukan Laporan Akhir
                        </span>
                        <span wire:loading wire:target="submit">
                            <span class="me-2 spinner-border spinner-border-sm"></span>
                            Mengajukan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal: Mandatory Output -->
    @teleport('body')
        <x-tabler.modal id="modalMandatoryOutput" title="Edit Luaran Wajib - Jurnal" size="xl" scrollable
            wire:ignore.self onHide="closeMandatoryModal">

            <x-slot:body>
                @if ($form->editingMandatoryId)
                    <div class="row g-3">
                        <!-- Status Type -->
                        <div class="col-md-6">
                            <label class="form-label required">Status Publikasi</label>
                            <select wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.status_type"
                                class="form-select">
                                <option value="">Pilih Status</option>
                                <option value="published">Published</option>
                                <option value="accepted">Accepted</option>
                                <option value="under_review">Under Review</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.status_type")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Author Status -->
                        <div class="col-md-6">
                            <label class="form-label required">Status Penulis</label>
                            <select wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.author_status"
                                class="form-select">
                                <option value="">Pilih Status</option>
                                <option value="first_author">First Author</option>
                                <option value="co_author">Co-Author</option>
                                <option value="corresponding_author">Corresponding Author</option>
                            </select>
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.author_status")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Journal Title -->
                        <div class="col-md-12">
                            <label class="form-label required">Judul Jurnal</label>
                            <input type="text"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.journal_title"
                                class="form-control" placeholder="Masukkan judul jurnal" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.journal_title")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- ISSN / E-ISSN -->
                        <div class="col-md-6">
                            <label class="form-label">ISSN</label>
                            <input type="text" wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.issn"
                                class="form-control" placeholder="1234-5678" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.issn")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-ISSN</label>
                            <input type="text"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.eissn"
                                class="form-control" placeholder="1234-5678" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.eissn")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Indexing Body -->
                        <div class="col-md-6">
                            <label class="form-label">Lembaga Pengindex</label>
                            <input type="text"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.indexing_body"
                                class="form-control" placeholder="Scopus, WoS, Sinta, dll" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.indexing_body")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Journal URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Jurnal</label>
                            <input type="url"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.journal_url"
                                class="form-control" placeholder="https://" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.journal_url")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Article Title -->
                        <div class="col-md-12">
                            <label class="form-label required">Judul Artikel</label>
                            <input type="text"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.article_title"
                                class="form-control" placeholder="Masukkan judul artikel" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.article_title")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Publication Year -->
                        <div class="col-md-3">
                            <label class="form-label required">Tahun Terbit</label>
                            <input type="number"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.publication_year"
                                class="form-control" min="2000" max="2030" /> @disabled(!$canEdit) />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.publication_year")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Volume -->
                        <div class="col-md-3">
                            <label class="form-label">Volume</label>
                            <input type="text"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.volume"
                                class="form-control" placeholder="Vol. 1" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.volume")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Issue Number -->
                        <div class="col-md-3">
                            <label class="form-label">Nomor</label>
                            <input type="text"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.issue_number"
                                class="form-control" placeholder="No. 1" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.issue_number")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Pages -->
                        <div class="col-md-3">
                            <label class="form-label">Halaman</label>
                            <div class="input-group">
                                <input type="number"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.page_start"
                                    class="form-control" placeholder="1" />
                                <span class="input-group-text">-</span>
                                <input type="number"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.page_end"
                                    class="form-control" placeholder="10" />
                            </div>
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.page_start")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Article URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Artikel</label>
                            <input type="url"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.article_url"
                                class="form-control" placeholder="https://" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.article_url")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- DOI -->
                        <div class="col-md-6">
                            <label class="form-label">DOI Artikel</label>
                            <input type="text" wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.doi"
                                class="form-control" placeholder="10.xxxx/xxxxx" />
                            @error("form.mandatoryOutputs.{$editingMandatoryId}.doi")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="col-md-12">
                            <label class="form-label">Dokumen Artikel (PDF)</label>
                            <input type="file" wire:model="form.tempMandatoryFiles.{{ $form->editingMandatoryId }}"
                                class="form-control" accept=".pdf" />
                            @if (isset($mandatoryOutputs[$editingMandatoryId]['document_file']) &&
                                    $mandatoryOutputs[$editingMandatoryId]['document_file']
                            )
                                <div class="mt-2">
                                    <small class="text-success">
                                        <x-lucide-check class="icon icon-sm" />
                                        File tersimpan
                                    </small>
                                </div>
                            @endif
                            <div wire:loading wire:target="form.tempMandatoryFiles.{{ $form->editingMandatoryId }}">
                                <small class="text-muted">
                                    <span class="me-2 spinner-border spinner-border-sm"></span>
                                    Uploading...
                                </small>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-muted">Tidak ada data yang sedang diedit</p>
                @endif
            </x-slot:body>

            <x-slot:footer>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Tutup
                </button>
                <button type="button" wire:click="saveMandatoryOutput({{ $form->editingMandatoryId }})"
                    class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveMandatoryOutput">
                        <x-lucide-save class="icon" /> Simpan
                    </span>
                    <span wire:loading wire:target="saveMandatoryOutput">
                        <span class="me-2 spinner-border spinner-border-sm"></span>
                        Menyimpan...
                    </span>
                </button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport

    <!-- Modal: Additional Output -->
    @teleport('body')
        <x-tabler.modal id="modalAdditionalOutput" title="Edit Luaran Tambahan - Buku" size="lg" scrollable
            wire:ignore.self onHide="closeAdditionalModal">

            <x-slot:body>
                @if ($form->editingAdditionalId)
                    <div class="row g-3">
                        <!-- Status -->
                        <div class="col-md-12">
                            <label class="form-label required">Status</label>
                            <select wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.status"
                                class="form-select">
                                <option value="">Pilih Status</option>
                                <option value="review">Review</option>
                                <option value="editing">Editing</option>
                                <option value="published">Terbit</option>
                            </select>
                            @error("form.additionalOutputs.{$editingAdditionalId}.status")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Book Title -->
                        <div class="col-md-12">
                            <label class="form-label required">Judul Buku</label>
                            <input type="text"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.book_title"
                                class="form-control" placeholder="Masukkan judul buku" />
                            @error("form.additionalOutputs.{$editingAdditionalId}.book_title")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Publisher Name -->
                        <div class="col-md-6">
                            <label class="form-label required">Nama Penerbit</label>
                            <input type="text"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.publisher_name"
                                class="form-control" placeholder="Masukkan nama penerbit" />
                            @error("form.additionalOutputs.{$editingAdditionalId}.publisher_name")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- ISBN -->
                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <input type="text"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.isbn"
                                class="form-control" placeholder="978-xxx-xxx-xxx-x" />
                            @error("form.additionalOutputs.{$editingAdditionalId}.isbn")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Publication Year -->
                        <div class="col-md-6">
                            <label class="form-label">Tahun Terbit</label>
                            <input type="number"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.publication_year"
                                class="form-control" min="2000" max="2030" /> @disabled(!$canEdit) />
                            @error("form.additionalOutputs.{$editingAdditionalId}.publication_year")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Total Pages -->
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Halaman</label>
                            <input type="number"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.total_pages"
                                class="form-control" placeholder="100" />
                            @error("form.additionalOutputs.{$editingAdditionalId}.total_pages")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Publisher URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Web Penerbit</label>
                            <input type="url"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.publisher_url"
                                class="form-control" placeholder="https://" />
                            @error("form.additionalOutputs.{$editingAdditionalId}.publisher_url")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Book URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Buku</label>
                            <input type="url"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.book_url"
                                class="form-control" placeholder="https://" />
                            @error("form.additionalOutputs.{$editingAdditionalId}.book_url")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Document File -->
                        <div class="col-md-6">
                            <label class="form-label">Dokumen Buku/Draft</label>
                            <input type="file" wire:model="form.tempAdditionalFiles.{{ $form->editingAdditionalId }}"
                                class="form-control" accept=".pdf" />
                            @if (isset($additionalOutputs[$editingAdditionalId]['document_file']) &&
                                    $additionalOutputs[$editingAdditionalId]['document_file']
                            )
                                <div class="mt-2">
                                    <small class="text-success">
                                        <x-lucide-check class="icon icon-sm" />
                                        File tersimpan
                                    </small>
                                </div>
                            @endif
                            <div wire:loading wire:target="form.tempAdditionalFiles.{{ $form->editingAdditionalId }}">
                                <small class="text-muted">
                                    <span class="me-2 spinner-border spinner-border-sm"></span>
                                    Uploading...
                                </small>
                            </div>
                        </div>

                        <!-- Publication Certificate -->
                        <div class="col-md-6">
                            <label class="form-label">Surat Keterangan Terbit</label>
                            <input type="file" wire:model="form.tempAdditionalCerts.{{ $form->editingAdditionalId }}"
                                class="form-control" accept=".pdf" />
                            @if (isset($additionalOutputs[$editingAdditionalId]['publication_certificate']) &&
                                    $additionalOutputs[$editingAdditionalId]['publication_certificate']
                            )
                                <div class="mt-2">
                                    <small class="text-success">
                                        <x-lucide-check class="icon icon-sm" />
                                        File tersimpan
                                    </small>
                                </div>
                            @endif
                            <div wire:loading wire:target="form.tempAdditionalCerts.{{ $form->editingAdditionalId }}">
                                <small class="text-muted">
                                    <span class="me-2 spinner-border spinner-border-sm"></span>
                                    Uploading...
                                </small>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-muted">Tidak ada data yang sedang diedit</p>
                @endif
            </x-slot:body>

            <x-slot:footer>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Tutup
                </button>
                <button type="button" wire:click="saveAdditionalOutput({{ $form->editingAdditionalId }})"
                    class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveAdditionalOutput">
                        <x-lucide-save class="icon" /> Simpan
                    </span>
                    <span wire:loading wire:target="saveAdditionalOutput">
                        <span class="me-2 spinner-border spinner-border-sm"></span>
                        Menyimpan...
                    </span>
                </button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport
</div>
