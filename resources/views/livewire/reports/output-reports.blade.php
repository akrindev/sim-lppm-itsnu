<x-slot:title>Laporan Luaran</x-slot:title>
<x-slot:pageTitle>Laporan Luaran</x-slot:pageTitle>
<x-slot:pageSubtitle>
    Laporan semua luaran penelitian dan pengabdian masyarakat
</x-slot:pageSubtitle>

<!-- Statistics Cards -->
<div>
    <div>
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('Total Luaran') }}</div>
                        </div>
                        <div class="h1 mb-0">{{ $statistics['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('Luaran Wajib') }}</div>
                        </div>
                        <div class="h1 mb-0">{{ $statistics['mandatory'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('Luaran Tambahan') }}</div>
                        </div>
                        <div class="h1 mb-0">{{ $statistics['additional'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('Total Proposal') }}</div>
                        </div>
                        <div class="h1 mb-0">{{ $statistics['proposals'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <!-- Tabs -->
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button wire:click="setTab('research')"
                            class="nav-link {{ $activeTab === 'research' ? 'active' : '' }}" type="button">
                            <x-lucide-puzzle class="icon me-1" />
                            {{ __('Penelitian') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setTab('community-service')"
                            class="nav-link {{ $activeTab === 'community-service' ? 'active' : '' }}" type="button">
                            <x-lucide-gift class="icon me-1" />
                            {{ __('Pengabdian') }}
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <div class="input-icon">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="{{ __('Cari judul, ISBN, nama produk...') }}">
                            <span class="input-icon-addon">
                                <x-lucide-search class="icon" />
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <select wire:model.live="outputType" class="form-select">
                            <option value="all">{{ __('Semua Luaran') }}</option>
                            <option value="mandatory">{{ __('Luaran Wajib') }}</option>
                            <option value="additional">{{ __('Luaran Tambahan') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Grouped Outputs by Proposal -->
                @forelse ($proposals as $proposal)
                    <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-surface-secondary py-3 border-bottom">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-fill">
                                    <div class="mb-1">
                                        <x-tabler.badge color="azure" class="mb-1">
                                            {{ $proposal->researchScheme?->name ?? 'Proposal' }}
                                        </x-tabler.badge>
                                    </div>
                                    <h3 class="card-title mb-1 text-primary fw-bold">
                                        {{ $proposal->title }}
                                    </h3>
                                    <div class="text-secondary small d-flex align-items-center">
                                        <x-lucide-user class="icon icon-inline me-1" />
                                        <span>{{ $proposal->user?->name ?? '-' }}</span>
                                        <span class="mx-2 text-muted">|</span>
                                        <x-lucide-calendar class="icon icon-inline me-1" />
                                        <span>Tahun {{ $proposal->start_year }}</span>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    @php
                                        $outputCount = 0;
                                        foreach($proposal->progressReports as $report) {
                                            $outputCount += $report->mandatoryOutputs->count() + $report->additionalOutputs->count();
                                        }
                                    @endphp
                                    <span class="badge bg-blue text-blue-fg badge-pill">{{ $outputCount }} Luaran</span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table-vcenter card-table table">
                                <thead class="bg-surface-secondary">
                                    <tr>
                                        <th style="width: 120px;">{{ __('Kategori') }}</th>
                                        <th>{{ __('Jenis & Judul Luaran') }}</th>
                                        <th style="width: 200px;">{{ __('Detail Identitas') }}</th>
                                        <th style="width: 100px;">{{ __('Status') }}</th>
                                        <th style="width: 120px;">{{ __('Tanggal') }}</th>
                                        <th class="w-1">{{ __('Aksi') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop through progress reports to get outputs --}}
                                    @foreach ($proposal->progressReports as $report)
                                        {{-- Mandatory Outputs --}}
                                        @if($outputType !== 'additional')
                                            @foreach ($report->mandatoryOutputs as $output)
                                                <tr wire:key="mandatory-{{ $output->id }}">
                                                    <td>
                                                        <div class="mb-1">
                                                            <x-tabler.badge color="primary">
                                                                Wajib
                                                            </x-tabler.badge>
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $this->getOutputCategoryName($output->proposalOutput->category ?? 'journal') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="fw-medium text-body">
                                                            {{ $output->article_title ?? ($output->book_title ?? ($output->product_name ?? '-')) }}
                                                        </div>
                                                        <small class="text-secondary">
                                                            {{ $output->proposalOutput->type ?? '-' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @if ($output->journal_title)
                                                            <div class="small">{{ $output->journal_title }}</div>
                                                            @if ($output->issn)
                                                                <div class="small text-muted">ISSN: {{ $output->issn }}</div>
                                                            @endif
                                                        @elseif($output->isbn)
                                                            <div class="small text-muted">ISBN: {{ $output->isbn }}</div>
                                                        @elseif($output->registration_number)
                                                            <div class="small text-muted">No. Reg: {{ $output->registration_number }}</div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <x-tabler.badge :color="$output->status_type === 'published' ? 'success' : 'warning'">
                                                            {{ ucfirst((string) ($output->status_type ?? 'Draft')) }}
                                                        </x-tabler.badge>
                                                    </td>
                                                    <td class="small text-secondary">
                                                        {{ $output->created_at->format('d/m/Y') }}
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            wire:click="viewMandatoryOutput('{{ $output->id }}')"
                                                            wire:loading.attr="disabled"
                                                            class="btn btn-sm btn-icon btn-outline-info" title="Lihat Detail">
                                                            <x-lucide-eye class="icon" />
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                        {{-- Additional Outputs --}}
                                        @if($outputType !== 'mandatory')
                                            @foreach ($report->additionalOutputs as $output)
                                                <tr wire:key="additional-{{ $output->id }}">
                                                    <td>
                                                        <div class="mb-1">
                                                            <x-tabler.badge color="info">
                                                                Tambahan
                                                            </x-tabler.badge>
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $this->getOutputCategoryName($output->proposalOutput->category ?? 'book') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="fw-medium text-body">
                                                            {{ $output->book_title ?? ($output->journal_title ?? ($output->product_name ?? '-')) }}
                                                        </div>
                                                        <small class="text-secondary">
                                                            {{ $output->proposalOutput->type ?? '-' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @if ($output->isbn)
                                                            <div class="small text-muted">ISBN: {{ $output->isbn }}</div>
                                                        @elseif($output->issn)
                                                            <div class="small text-muted">ISSN: {{ $output->issn }}</div>
                                                        @elseif($output->registration_number)
                                                            <div class="small text-muted">No. Reg: {{ $output->registration_number }}</div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <x-tabler.badge :color="$output->status === 'published' ? 'success' : 'warning'">
                                                            {{ ucfirst((string) ($output->status ?? 'Draft')) }}
                                                        </x-tabler.badge>
                                                    </td>
                                                    <td class="small text-secondary">
                                                        {{ $output->created_at->format('d/m/Y') }}
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            wire:click="viewAdditionalOutput('{{ $output->id }}')"
                                                            wire:loading.attr="disabled"
                                                            class="btn btn-sm btn-icon btn-outline-info" title="Lihat Detail">
                                                            <x-lucide-eye class="icon" />
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="empty">
                        <div class="empty-icon">
                            <x-lucide-inbox class="icon" />
                        </div>
                        <p class="empty-title">{{ __('Tidak ada data luaran') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ __('Belum ada luaran yang dilaporkan untuk kategori ini.') }}
                        </p>
                    </div>
                @endforelse

                <div class="mt-3">
                    {{ $proposals->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- Modal: View Mandatory Output -->
    <x-tabler.modal id="modalMandatoryOutput" title="Lihat Luaran Wajib" size="xl" scrollable wire:ignore.self
        wire:key="modal-mandatory"
        onHide="closeMandatoryModal">
        <x-slot:body>
            @if ($mandatoryOutput = $this->mandatoryOutput())
                @php
                    $outputType = $mandatoryOutput->proposalOutput->type ?? '';
                    $outputCategory = $mandatoryOutput->proposalOutput->category ?? '';
                @endphp

                <div class="row g-3">
                    <!-- Status -->
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div>
                            <x-tabler.badge :color="$mandatoryOutput->status_type === 'published' ? 'success' : 'warning'">
                                {{ ucfirst((string) ($mandatoryOutput->status_type ?? 'Draft')) }}
                            </x-tabler.badge>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <div>
                            <x-tabler.badge color="primary">
                                {{ $this->getOutputCategoryName($outputCategory) }}
                            </x-tabler.badge>
                        </div>
                    </div>

                    <!-- Journal Fields -->
                    @if (str_contains(strtolower($outputType), 'jurnal') || str_contains(strtolower($outputCategory), 'journal'))
                        <div class="col-md-12">
                            <label class="form-label">Judul Jurnal</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->journal_title ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ISSN</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->issn ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-ISSN</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->eissn ?? '-' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Judul Artikel</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->article_title ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">DOI</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->doi ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tahun Terbit</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->publication_year ?? '-' }}</p>
                        </div>
                    @endif

                    <!-- Book Fields -->
                    @if (str_contains(strtolower($outputType), 'buku') || str_contains(strtolower($outputCategory), 'book'))
                        <div class="col-md-12">
                            <label class="form-label">Judul Buku</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->book_title ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->isbn ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Penerbit</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->publisher ?? '-' }}</p>
                        </div>
                    @endif

                    <!-- HKI Fields -->
                    @if (str_contains(strtolower($outputType), 'hki') || str_contains(strtolower($outputCategory), 'hki'))
                        <div class="col-md-6">
                            <label class="form-label">Jenis HKI</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->hki_type ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Registrasi</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->registration_number ?? '-' }}</p>
                        </div>
                    @endif

                    <!-- Product Fields -->
                    @if (str_contains(strtolower($outputType), 'produk') || str_contains(strtolower($outputCategory), 'product'))
                        <div class="col-md-12">
                            <label class="form-label">Nama Produk</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->product_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <p class="form-control-plaintext">{{ $mandatoryOutput->description ?? '-' }}</p>
                        </div>
                    @endif

                    <!-- Document -->
                    @if ($media = $mandatoryOutput->getFirstMedia('journal_article'))
                        <div class="col-12">
                            <label class="form-label">Dokumen</label>
                            <div class="bg-surface-secondary rounded border p-2">
                                <div class="d-flex align-items-center">
                                    <x-lucide-file-text class="text-primary icon me-2" />
                                    <div class="flex-fill">
                                        <small>{{ $media->name }}</small><br>
                                        <small class="text-muted">({{ $media->human_readable_size }})</small>
                                    </div>
                                    <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-sm btn-primary">
                                        <x-lucide-download class="icon" /> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </x-slot:body>
    </x-tabler.modal>

    <!-- Modal: View Additional Output -->
    <x-tabler.modal id="modalAdditionalOutput" title="Lihat Luaran Tambahan" size="xl" scrollable
        wire:key="modal-additional"
        wire:ignore.self onHide="closeAdditionalModal">
        <x-slot:body>
            @if ($additionalOutput = $this->additionalOutput())
                @php
                    $outputType = $additionalOutput->proposalOutput->type ?? '';
                    $outputCategory = $additionalOutput->proposalOutput->category ?? '';
                @endphp

                <div class="row g-3">
                    <!-- Status -->
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div>
                            <x-tabler.badge :color="$additionalOutput->status === 'published' ? 'success' : 'warning'">
                                {{ ucfirst((string) ($additionalOutput->status ?? 'Draft')) }}
                            </x-tabler.badge>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <div>
                            <x-tabler.badge color="info">
                                {{ $this->getOutputCategoryName($outputCategory) }}
                            </x-tabler.badge>
                        </div>
                    </div>

                    <!-- Journal Fields -->
                    @if (str_contains(strtolower($outputType), 'jurnal') || str_contains(strtolower($outputCategory), 'journal'))
                        <div class="col-md-12">
                            <label class="form-label">Judul Jurnal</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->journal_title ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ISSN</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->issn ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">DOI</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->doi ?? '-' }}</p>
                        </div>
                    @endif

                    <!-- Book Fields -->
                    @if (str_contains(strtolower($outputType), 'buku') || str_contains(strtolower($outputCategory), 'book'))
                        <div class="col-md-12">
                            <label class="form-label">Judul Buku</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->book_title ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->isbn ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Penerbit</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->publisher_name ?? '-' }}</p>
                        </div>
                    @endif

                    <!-- HKI Fields -->
                    @if (str_contains(strtolower($outputType), 'hki') || str_contains(strtolower($outputCategory), 'hki'))
                        <div class="col-md-6">
                            <label class="form-label">Jenis HKI</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->hki_type ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Registrasi</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->registration_number ?? '-' }}</p>
                        </div>
                    @endif

                    <!-- Product Fields -->
                    @if (str_contains(strtolower($outputType), 'produk') || str_contains(strtolower($outputCategory), 'product'))
                        <div class="col-md-12">
                            <label class="form-label">Nama Produk</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->product_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <p class="form-control-plaintext">{{ $additionalOutput->description ?? '-' }}</p>
                        </div>
                    @endif

                    <!-- Document -->
                    @if ($media = $additionalOutput->getFirstMedia('book_document'))
                        <div class="col-12">
                            <label class="form-label">Dokumen</label>
                            <div class="bg-surface-secondary rounded border p-2">
                                <div class="d-flex align-items-center">
                                    <x-lucide-file-text class="text-primary icon me-2" />
                                    <div class="flex-fill">
                                        <small>{{ $media->name }}</small><br>
                                        <small class="text-muted">({{ $media->human_readable_size }})</small>
                                    </div>
                                    <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-sm btn-primary">
                                        <x-lucide-download class="icon" /> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </x-slot:body>
    </x-tabler.modal>
</div>
