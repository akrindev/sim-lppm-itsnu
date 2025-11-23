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
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-lucide-file-text class="icon me-2" />Ringkasan & Kata Kunci</h3>
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
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-lucide-file-text class="icon me-2" />Dokumen Laporan Akhir</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">File Substansi Laporan (PDF)</label>
                <input type="file" wire:model="substanceFile"
                    class="form-control @error('substanceFile') is-invalid @enderror" accept=".pdf"
                    @disabled(!$canEdit) />
                @error('substanceFile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-hint">Maksimal 10MB, format PDF</small>

                <div wire:loading wire:target="substanceFile">
                    <small class="text-muted">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Uploading...
                    </small>
                </div>

                @if ($progressReport && $progressReport->hasMedia('substance_file'))
                    @php
                        $media = $progressReport->getFirstMedia('substance_file');
                    @endphp
                    <div class="alert alert-success mb-0 mt-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <x-lucide-file-check class="text-success icon me-2" />
                                <strong>{{ $media->name }}</strong>
                                <small class="text-muted ms-2">({{ $media->human_readable_size }})</small>
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
                <input type="file" wire:model="realizationFile"
                    class="form-control @error('realizationFile') is-invalid @enderror" accept=".pdf,.docx"
                    @disabled(!$canEdit) />
                @error('realizationFile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-hint">Maksimal 10MB, format PDF atau DOCX</small>

                <div wire:loading wire:target="realizationFile">
                    <small class="text-muted">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Uploading...
                    </small>
                </div>

                @if ($progressReport && $progressReport->hasMedia('realization_file'))
                    @php
                        $media = $progressReport->getFirstMedia('realization_file');
                    @endphp
                    <div class="alert alert-success mb-0 mt-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <x-lucide-file-check class="text-success icon me-2" />
                                <strong>{{ $media->name }}</strong>
                                <small class="text-muted ms-2">({{ $media->human_readable_size }})</small>
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
                <input type="file" wire:model="presentationFile"
                    class="form-control @error('presentationFile') is-invalid @enderror" accept=".pdf,.ppt,.pptx"
                    @disabled(!$canEdit) />
                @error('presentationFile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-hint">Maksimal 50MB, format PDF atau PPTX</small>

                <div wire:loading wire:target="presentationFile">
                    <small class="text-muted">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Uploading...
                    </small>
                </div>

                @if ($progressReport && $progressReport->hasMedia('presentation_file'))
                    @php
                        $media = $progressReport->getFirstMedia('presentation_file');
                    @endphp
                    <div class="alert alert-success mb-0 mt-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <x-lucide-file-check class="text-success icon me-2" />
                                <strong>{{ $media->name }}</strong>
                                <small class="text-muted ms-2">({{ $media->human_readable_size }})</small>
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
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-lucide-book-open class="icon me-2" />Luaran Wajib</h3>
        </div>
        <div class="card-body">
            @php
                $wajibs = $proposal->outputs->where('category', 'Wajib');
            @endphp

            @if ($wajibs->isNotEmpty())
                <div class="table-responsive">
                    <table class="card-table table-vcenter table">
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
                                    $rowMandatoryOutput = $progressReport
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
                                        @if ($rowMandatoryOutput && $rowMandatoryOutput->hasMedia('journal_article'))
                                            @php
                                                $media = $rowMandatoryOutput->getFirstMedia('journal_article');
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
                                            <button type="button"
                                                wire:click="editMandatoryOutput({{ $output->id }})"
                                                class="btn btn-sm btn-icon btn-info" data-bs-toggle="modal"
                                                data-bs-target="#modalMandatoryOutput">
                                                <x-lucide-eye class="icon" />
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted py-4 text-center">
                    <x-lucide-inbox class="icon icon-lg mb-2" />
                    <p>Tidak ada luaran wajib yang direncanakan</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Luaran Tambahan -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-lucide-book class="icon me-2" />Luaran Tambahan</h3>
        </div>
        <div class="card-body">
            @php
                $tambahans = $proposal->outputs->where('category', 'Tambahan');
            @endphp

            @if ($tambahans->isNotEmpty())
                <div class="table-responsive">
                    <table class="card-table table-vcenter table">
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
                                    $rowAdditionalOutput = $progressReport
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
                                        @if ($rowAdditionalOutput)
                                            <div class="d-flex gap-2">
                                                @if ($rowAdditionalOutput->hasMedia('book_document'))
                                                    @php
                                                        $media = $rowAdditionalOutput->getFirstMedia('book_document');
                                                    @endphp
                                                    <a href="{{ $media->getUrl() }}" target="_blank"
                                                        class="btn btn-sm btn-success">
                                                        <x-lucide-book class="icon icon-sm" />
                                                        Buku
                                                    </a>
                                                @endif

                                                @if ($rowAdditionalOutput->hasMedia('publication_certificate'))
                                                    @php
                                                        $media = $rowAdditionalOutput->getFirstMedia(
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

                                            @if (!$rowAdditionalOutput->hasMedia('book_document') && !$rowAdditionalOutput->hasMedia('publication_certificate'))
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
                                            <button type="button"
                                                wire:click="editAdditionalOutput({{ $output->id }})"
                                                class="btn btn-sm btn-icon btn-info" data-bs-toggle="modal"
                                                data-bs-target="#modalAdditionalOutput">
                                                <x-lucide-eye class="icon" />
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted py-4 text-center">
                    <x-lucide-inbox class="icon icon-lg mb-2" />
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
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Menyimpan...
                        </span>
                    </button>
                    <button type="button" wire:click="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submit">
                            <x-lucide-send class="icon" /> Ajukan Laporan Akhir
                        </span>
                        <span wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Mengajukan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal: Mandatory Output -->
    @teleport('body')
        <x-tabler.modal id="modalMandatoryOutput" title="{{ $canEdit ? 'Edit' : 'Lihat' }} Luaran Wajib - Jurnal"
            size="xl" scrollable wire:ignore.self onHide="closeMandatoryModal">

            <x-slot:body>
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <x-lucide-alert-circle class="icon alert-icon" />
                            </div>
                            <div>
                                <h4 class="alert-title">Terdapat kesalahan pada form!</h4>
                                <div class="text-secondary">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                @endif

                @if ($form->editingMandatoryId)
                    @php
                        $currentOutput = $proposal->outputs->find($form->editingMandatoryId);
                        $outputType = $currentOutput?->type ?? '';
                        $outputGroup = $currentOutput?->group ?? '';
                    @endphp

                    <div class="row g-3">
                        <!-- Common Fields -->
                        <div class="col-md-6">
                            <label class="form-label required">Status</label>
                            <select wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.status_type"
                                class="form-select" @disabled(!$canEdit)>
                                <option value="">Pilih Status</option>
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                                <option value="accepted">Accepted</option>
                                <option value="published">Published</option>
                                <option value="rejected">Rejected</option>
                                <option value="granted">Granted (HKI)</option>
                            </select>
                            @error("form.mandatoryOutputs.{$form->editingMandatoryId}.status_type")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- JURNAL/PROSIDING Fields -->
                        @if (str_contains(strtolower($outputType), 'jurnal') ||
                                str_contains(strtolower($outputGroup), 'jurnal') ||
                                str_contains(strtolower($outputType), 'prosiding') ||
                                str_contains(strtolower($outputGroup), 'prosiding'))
                            <div class="col-md-6">
                                <label class="form-label required">Status Penulis</label>
                                <select wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.author_status"
                                    class="form-select" @disabled(!$canEdit)>
                                    <option value="">Pilih Status</option>
                                    <option value="first_author">First Author</option>
                                    <option value="co_author">Co-Author</option>
                                    <option value="corresponding_author">Corresponding Author</option>
                                </select>
                                @error("form.mandatoryOutputs.{$form->editingMandatoryId}.author_status")
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label required">Judul
                                    {{ str_contains(strtolower($outputType), 'prosiding') ? 'Prosiding' : 'Jurnal' }}</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.journal_title"
                                    class="form-control" placeholder="Masukkan judul" @disabled(!$canEdit) />
                                @error("form.mandatoryOutputs.{$form->editingMandatoryId}.journal_title")
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label
                                    class="form-label">{{ str_contains(strtolower($outputType), 'prosiding') ? 'ISBN' : 'ISSN' }}</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.issn"
                                    class="form-control" placeholder="1234-5678" @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-6">
                                <label
                                    class="form-label">E-{{ str_contains(strtolower($outputType), 'prosiding') ? 'ISBN' : 'ISSN' }}</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.eissn"
                                    class="form-control" placeholder="1234-5678" @disabled(!$canEdit) />
                            </div>

                            <div class="col-md-12">
                                <label class="form-label required">Judul Artikel</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.article_title"
                                    class="form-control" placeholder="Masukkan judul artikel"
                                    @disabled(!$canEdit) />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">URL Publikasi</label>
                                <input type="url"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.journal_url"
                                    class="form-control" placeholder="https://" @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">URL Artikel</label>
                                <input type="url"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.article_url"
                                    class="form-control" placeholder="https://" @disabled(!$canEdit) />
                            </div>
                        @endif

                        <!-- BUKU/MODUL Fields -->
                        @if (str_contains(strtolower($outputType), 'buku') ||
                                str_contains(strtolower($outputGroup), 'buku') ||
                                str_contains(strtolower($outputType), 'modul') ||
                                str_contains(strtolower($outputType), 'pedoman'))
                            <div class="col-md-12">
                                <label class="form-label required">Judul Buku/Modul</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.book_title"
                                    class="form-control" placeholder="Masukkan judul" @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ISBN</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.isbn"
                                    class="form-control" placeholder="ISBN" @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Penerbit</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.publisher"
                                    class="form-control" placeholder="Nama Penerbit" @disabled(!$canEdit) />
                            </div>
                        @endif

                        <!-- HKI Fields -->
                        @if (str_contains(strtolower($outputType), 'hki') ||
                                str_contains(strtolower($outputType), 'paten') ||
                                str_contains(strtolower($outputType), 'hak cipta') ||
                                str_contains(strtolower($outputType), 'merek') ||
                                str_contains(strtolower($outputGroup), 'hki'))
                            <div class="col-md-6">
                                <label class="form-label required">Jenis HKI</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.hki_type"
                                    class="form-control" placeholder="Paten, Hak Cipta, Merek, dll"
                                    @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor Pendaftaran/Sertifikat</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.registration_number"
                                    class="form-control" placeholder="Nomor" @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Inventor</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.inventors"
                                    class="form-control" placeholder="Nama Inventor" @disabled(!$canEdit) />
                            </div>
                        @endif

                        <!-- MEDIA MASSA Fields -->
                        @if (str_contains(strtolower($outputType), 'media') || str_contains(strtolower($outputGroup), 'media'))
                            <div class="col-md-12">
                                <label class="form-label required">Nama Media Massa</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.media_name"
                                    class="form-control" placeholder="Kompas, Detik, dll" @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-12">
                                <label class="form-label required">URL Berita</label>
                                <input type="url"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.media_url"
                                    class="form-control" placeholder="https://" @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Tanggal Terbit</label>
                                <input type="date"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.publication_date"
                                    class="form-control" @disabled(!$canEdit) />
                            </div>
                        @endif

                        <!-- VIDEO Fields -->
                        @if (str_contains(strtolower($outputType), 'video') || str_contains(strtolower($outputGroup), 'video'))
                            <div class="col-md-12">
                                <label class="form-label required">URL Video</label>
                                <input type="url"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.video_url"
                                    class="form-control" placeholder="https://youtube.com/..."
                                    @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Platform</label>
                                <select wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.platform"
                                    class="form-select" @disabled(!$canEdit)>
                                    <option value="">Pilih Platform</option>
                                    <option value="YouTube">YouTube</option>
                                    <option value="Instagram">Instagram</option>
                                    <option value="TikTok">TikTok</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        @endif

                        <!-- PRODUK/JASA Fields -->
                        @if (str_contains(strtolower($outputType), 'produk') ||
                                str_contains(strtolower($outputType), 'jasa') ||
                                str_contains(strtolower($outputType), 'sistem') ||
                                str_contains(strtolower($outputType), 'ttg') ||
                                str_contains(strtolower($outputGroup), 'produk'))
                            <div class="col-md-12">
                                <label class="form-label required">Nama Produk/Jasa</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.product_name"
                                    class="form-control" placeholder="Masukkan nama produk/jasa"
                                    @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Deskripsi Singkat</label>
                                <textarea wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.description" class="form-control"
                                    rows="3" placeholder="Deskripsi produk/jasa" @disabled(!$canEdit)></textarea>
                            </div>
                        @endif

                        <!-- PEMBERDAYAAN MITRA Fields -->
                        @if (str_contains(strtolower($outputType), 'pemberdayaan') ||
                                str_contains(strtolower($outputType), 'peningkatan') ||
                                str_contains(strtolower($outputGroup), 'pemberdayaan'))
                            <div class="col-md-12">
                                <label class="form-label required">Nama Mitra yang Diberdayakan</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.partner_name"
                                    class="form-control" placeholder="Nama kelompok/mitra"
                                    @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Indikator Peningkatan</label>
                                <select wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.indicator_type"
                                    class="form-select" @disabled(!$canEdit)>
                                    <option value="">Pilih Indikator</option>
                                    <option value="omzet">Peningkatan Omzet</option>
                                    <option value="kualitas">Peningkatan Kualitas</option>
                                    <option value="kuantitas">Peningkatan Kuantitas</option>
                                    <option value="keterampilan">Peningkatan Keterampilan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nilai/Persentase Peningkatan</label>
                                <input type="text"
                                    wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.improvement_value"
                                    class="form-control" placeholder="Contoh: 50% atau Rp 10.000.000"
                                    @disabled(!$canEdit) />
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Deskripsi Pencapaian</label>
                                <textarea wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.description" class="form-control"
                                    rows="3" placeholder="Jelaskan peningkatan yang dicapai..." @disabled(!$canEdit)></textarea>
                            </div>
                        @endif

                        <!-- Common Year Field -->
                        <div class="col-md-3">
                            <label class="form-label required">Tahun</label>
                            <input type="number"
                                wire:model="form.mandatoryOutputs.{{ $form->editingMandatoryId }}.publication_year"
                                class="form-control" min="2000" max="2030" @disabled(!$canEdit) />
                        </div>

                        <!-- File Upload -->
                        <div class="col-md-12">
                            <label class="form-label">Dokumen Bukti (PDF)</label>
                            <input type="file" wire:model="tempMandatoryFiles.{{ $form->editingMandatoryId }}"
                                class="form-control" accept=".pdf" @disabled(!$canEdit) />
                            @error("tempMandatoryFiles.{$form->editingMandatoryId}")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div wire:loading wire:target="tempMandatoryFiles.{{ $form->editingMandatoryId }}">
                                <small class="text-muted">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Uploading...
                                </small>
                            </div>
                            @if ($mandatoryOutput = $this->mandatoryOutput())
                                @if ($media = $mandatoryOutput->getFirstMedia('journal_article'))
                                    <div class="bg-light mt-2 rounded border p-2">
                                        <div class="d-flex align-items-center">
                                            <x-lucide-file-text class="text-primary icon me-2" />
                                            <div class="flex-fill">
                                                <small class="text-muted">File yang sudah diunggah:</small><br>
                                                <strong>{{ $media->file_name }}</strong>
                                                <small class="text-muted">({{ number_format($media->size / 1024, 2) }}
                                                    KB)</small>
                                            </div>
                                            <a href="{{ $media->getUrl() }}" target="_blank"
                                                class="btn btn-sm btn-primary">
                                                <x-lucide-download class="icon" /> Download
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endif
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
                @if ($canEdit)
                    <button type="button" wire:click="saveMandatoryOutput({{ $form->editingMandatoryId }})"
                        class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveMandatoryOutput">
                            <x-lucide-save class="icon" /> Simpan
                        </span>
                        <span wire:loading wire:target="saveMandatoryOutput">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Menyimpan...
                        </span>
                    </button>
                @endif
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport

    <!-- Modal: Additional Output -->
    @teleport('body')
        <x-tabler.modal id="modalAdditionalOutput" title="{{ $canEdit ? 'Edit' : 'Lihat' }} Luaran Tambahan - Buku"
            size="lg" scrollable wire:ignore.self onHide="closeAdditionalModal">

            <x-slot:body>
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <x-lucide-alert-circle class="icon alert-icon" />
                            </div>
                            <div>
                                <h4 class="alert-title">Terdapat kesalahan pada form!</h4>
                                <div class="text-secondary">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                    </div>
                @endif

                @if ($form->editingAdditionalId)
                    <div class="row g-3">
                        <!-- Status -->
                        <div class="col-md-12">
                            <label class="form-label required">Status</label>
                            <select wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.status"
                                class="form-select" @disabled(!$canEdit)>
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
                                class="form-control" placeholder="Masukkan judul buku" @disabled(!$canEdit) />
                            @error("form.additionalOutputs.{$editingAdditionalId}.book_title")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Publisher Name -->
                        <div class="col-md-6">
                            <label class="form-label required">Nama Penerbit</label>
                            <input type="text"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.publisher_name"
                                class="form-control" placeholder="Masukkan nama penerbit" @disabled(!$canEdit) />
                            @error("form.additionalOutputs.{$editingAdditionalId}.publisher_name")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- ISBN -->
                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <input type="text"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.isbn"
                                class="form-control" placeholder="978-xxx-xxx-xxx-x" @disabled(!$canEdit) />
                            @error("form.additionalOutputs.{$editingAdditionalId}.isbn")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Publication Year -->
                        <div class="col-md-6">
                            <label class="form-label">Tahun Terbit</label>
                            <input type="number"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.publication_year"
                                class="form-control" min="2000" max="2030" @disabled(!$canEdit) />
                            @disabled(!$canEdit) />
                            @error("form.additionalOutputs.{$editingAdditionalId}.publication_year")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Total Pages -->
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Halaman</label>
                            <input type="number"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.total_pages"
                                class="form-control" placeholder="100" @disabled(!$canEdit) />
                            @error("form.additionalOutputs.{$editingAdditionalId}.total_pages")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Publisher URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Web Penerbit</label>
                            <input type="url"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.publisher_url"
                                class="form-control" placeholder="https://" @disabled(!$canEdit) />
                            @error("form.additionalOutputs.{$editingAdditionalId}.publisher_url")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Book URL -->
                        <div class="col-md-6">
                            <label class="form-label">URL Buku</label>
                            <input type="url"
                                wire:model="form.additionalOutputs.{{ $form->editingAdditionalId }}.book_url"
                                class="form-control" placeholder="https://" @disabled(!$canEdit) />
                            @error("form.additionalOutputs.{$editingAdditionalId}.book_url")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Document File -->
                        <div class="col-md-6">
                            <label class="form-label">Dokumen Buku/Draft</label>
                            <input type="file" wire:model="tempAdditionalFiles.{{ $form->editingAdditionalId }}"
                                class="form-control" accept=".pdf" @disabled(!$canEdit) />
                            @error("tempAdditionalFiles.{$form->editingAdditionalId}")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div wire:loading wire:target="tempAdditionalFiles.{{ $form->editingAdditionalId }}">
                                <small class="text-muted">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Uploading...
                                </small>
                            </div>
                            @if ($additionalOutput = $this->additionalOutput())
                                @if ($media = $additionalOutput->getFirstMedia('book_document'))
                                    <div class="bg-light mt-2 rounded border p-2">
                                        <div class="d-flex align-items-center">
                                            <x-lucide-file-text class="text-primary icon me-2" />
                                            <div class="flex-fill">
                                                <small class="text-muted">File yang sudah diunggah:</small><br>
                                                <strong>{{ $media->file_name }}</strong>
                                                <small class="text-muted">({{ number_format($media->size / 1024, 2) }}
                                                    KB)</small>
                                            </div>
                                            <a href="{{ $media->getUrl() }}" target="_blank"
                                                class="btn btn-sm btn-primary">
                                                <x-lucide-download class="icon" /> Download
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Publication Certificate -->
                        <div class="col-md-6">
                            <label class="form-label">Surat Keterangan Terbit</label>
                            <input type="file" wire:model="tempAdditionalCerts.{{ $form->editingAdditionalId }}"
                                class="form-control" accept=".pdf" @disabled(!$canEdit) />
                            @error("tempAdditionalCerts.{$form->editingAdditionalId}")
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div wire:loading wire:target="tempAdditionalCerts.{{ $form->editingAdditionalId }}">
                                <small class="text-muted">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Uploading...
                                </small>
                            </div>
                            @if ($additionalOutput = $this->additionalOutput())
                                @if ($media = $additionalOutput->getFirstMedia('publication_certificate'))
                                    <div class="bg-light mt-2 rounded border p-2">
                                        <div class="d-flex align-items-center">
                                            <x-lucide-file-text class="text-primary icon me-2" />
                                            <div class="flex-fill">
                                                <small class="text-muted">File yang sudah diunggah:</small><br>
                                                <strong>{{ $media->file_name }}</strong>
                                                <small class="text-muted">({{ number_format($media->size / 1024, 2) }}
                                                    KB)</small>
                                            </div>
                                            <a href="{{ $media->getUrl() }}" target="_blank"
                                                class="btn btn-sm btn-primary">
                                                <x-lucide-download class="icon" /> Download
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endif
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
                @if ($canEdit)
                    <button type="button" wire:click="saveAdditionalOutput({{ $form->editingAdditionalId }})"
                        class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveAdditionalOutput">
                            <x-lucide-save class="icon" /> Simpan
                        </span>
                        <span wire:loading wire:target="saveAdditionalOutput">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Menyimpan...
                        </span>
                    </button>
                @endif
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport
</div>
