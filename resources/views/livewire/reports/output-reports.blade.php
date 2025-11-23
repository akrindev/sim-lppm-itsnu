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

                <!-- Mandatory Outputs -->
                @if ($outputType !== 'additional' && $mandatoryOutputs->isNotEmpty())
                    <div class="mb-4">
                        <h3 class="mb-3">{{ __('Luaran Wajib') }}</h3>
                        <div class="table-responsive">
                            <table class="table-vcenter card-table table-striped table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Kategori') }}</th>
                                        <th>{{ __('Judul/Nama') }}</th>
                                        <th>{{ __('Detail') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Pengusul') }}</th>
                                        <th>{{ __('Tanggal') }}</th>
                                        <th class="w-1">{{ __('Aksi') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mandatoryOutputs as $output)
                                        <tr>
                                            <td>
                                                <x-tabler.badge color="primary">
                                                    {{ $this->getOutputCategoryName($output->proposalOutput->category ?? 'journal') }}
                                                </x-tabler.badge>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;">
                                                    {{ $output->article_title ?? ($output->book_title ?? ($output->product_name ?? '-')) }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($output->journal_title)
                                                    <small class="text-muted">{{ $output->journal_title }}</small><br>
                                                    @if ($output->issn)
                                                        <small class="text-muted">ISSN: {{ $output->issn }}</small>
                                                    @endif
                                                @elseif($output->isbn)
                                                    <small class="text-muted">ISBN: {{ $output->isbn }}</small>
                                                @elseif($output->registration_number)
                                                    <small class="text-muted">No. Reg:
                                                        {{ $output->registration_number }}</small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                <x-tabler.badge :color="$output->status_type === 'published' ? 'success' : 'warning'">
                                                    {{ ucfirst($output->status_type ?? 'Draft') }}
                                                </x-tabler.badge>
                                            </td>
                                            <td>
                                                {{ $output->progressReport?->proposal?->user?->name ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $output->created_at->format('d M Y') }}
                                            </td>
                                            <td>
                                                <button type="button"
                                                    wire:click="viewMandatoryOutput('{{ $output->id }}')"
                                                    class="btn btn-sm btn-icon btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#modalMandatoryOutput">
                                                    <x-lucide-eye class="icon" />
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Additional Outputs -->
                @if ($outputType !== 'mandatory' && $additionalOutputs->isNotEmpty())
                    <div class="mb-4">
                        <h3 class="mb-3">{{ __('Luaran Tambahan') }}</h3>
                        <div class="table-responsive">
                            <table class="table-vcenter card-table table-striped table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Kategori') }}</th>
                                        <th>{{ __('Judul/Nama') }}</th>
                                        <th>{{ __('Detail') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Pengusul') }}</th>
                                        <th>{{ __('Tanggal') }}</th>
                                        <th class="w-1">{{ __('Aksi') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($additionalOutputs as $output)
                                        <tr>
                                            <td>
                                                <x-tabler.badge color="info">
                                                    {{ $this->getOutputCategoryName($output->proposalOutput->category ?? 'book') }}
                                                </x-tabler.badge>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;">
                                                    {{ $output->book_title ?? ($output->journal_title ?? ($output->product_name ?? '-')) }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($output->isbn)
                                                    <small class="text-muted">ISBN: {{ $output->isbn }}</small>
                                                @elseif($output->issn)
                                                    <small class="text-muted">ISSN: {{ $output->issn }}</small>
                                                @elseif($output->registration_number)
                                                    <small class="text-muted">No. Reg:
                                                        {{ $output->registration_number }}</small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                <x-tabler.badge :color="$output->status === 'published' ? 'success' : 'warning'">
                                                    {{ ucfirst($output->status ?? 'Draft') }}
                                                </x-tabler.badge>
                                            </td>
                                            <td>
                                                {{ $output->progressReport?->proposal?->user?->name ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $output->created_at->format('d M Y') }}
                                            </td>
                                            <td>
                                                <button type="button"
                                                    wire:click="viewAdditionalOutput('{{ $output->id }}')"
                                                    class="btn btn-sm btn-icon btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#modalAdditionalOutput">
                                                    <x-lucide-eye class="icon" />
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Empty State -->
                @if (
                    ($outputType === 'all' && $mandatoryOutputs->isEmpty() && $additionalOutputs->isEmpty()) ||
                        ($outputType === 'mandatory' && $mandatoryOutputs->isEmpty()) ||
                        ($outputType === 'additional' && $additionalOutputs->isEmpty()))
                    <div class="empty">
                        <div class="empty-icon">
                            <x-lucide-inbox class="icon" />
                        </div>
                        <p class="empty-title">{{ __('Tidak ada data luaran') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ __('Belum ada luaran yang dilaporkan untuk kategori ini.') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Modal: View Mandatory Output -->
    @teleport('body')
        <x-tabler.modal id="modalMandatoryOutput" title="Lihat Luaran Wajib" size="xl" scrollable wire:ignore.self
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
                                    {{ ucfirst($mandatoryOutput->status_type ?? 'Draft') }}
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
                                <div class="bg-light rounded border p-2">
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

        <x-tabler.modal id="modalAdditionalOutput" title="Lihat Luaran Tambahan" size="xl" scrollable
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
                                    {{ ucfirst($additionalOutput->status ?? 'Draft') }}
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
                                <div class="bg-light rounded border p-2">
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
    @endteleport
</div>
