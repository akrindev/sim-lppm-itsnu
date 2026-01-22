<x-slot:title>{{ $proposal->title }}</x-slot:title>
<x-slot:pageTitle>{{ $proposal->title }}</x-slot:pageTitle>
<x-slot:pageSubtitle>Detail Revisi Proposal Pengabdian Masyarakat</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        <a href="{{ route('community-service.proposal-revision.index') }}" class="btn-outline-secondary btn"
            wire:navigate>
            <x-lucide-arrow-left class="icon" />
            Kembali
        </a>
    </div>
</x-slot:pageActions>

<div class="row" x-data="{ currentStep: 1 }">
    <div class="col-md-12">
        <x-tabler.alert />
    </div>

    <!-- Steps Indicator -->
    <div class="mb-3 col-md-12">
        <div class="card">
            <div class="card-body">
                <ul class="my-4 steps steps-green steps-counter">
                    <li class="step-item" :class="{ 'active': currentStep === 1 }">
                        <a href="#" @click.prevent="currentStep = 1" class="text-decoration-none">Substansi
                            Usulan</a>
                    </li>
                    <li class="step-item" :class="{ 'active': currentStep === 2 }">
                        <a href="#" @click.prevent="currentStep = 2" class="text-decoration-none">RAB</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="col-md-12">
        <!-- Section 1: Substansi Usulan -->
        <div id="section-substansi" x-show="currentStep === 1">
            <!-- Basic Info Card -->
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-file-text class="me-2 icon" />Judul</label>
                            <p class="text-reset">{{ $proposal->title }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-info class="me-2 icon" />Status</label>
                            <p>
                                <x-tabler.badge :color="$proposal->status->color()" class="fw-normal">
                                    {{ $proposal->status->label() }}
                                </x-tabler.badge>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-user class="me-2 icon" />Author</label>
                            <p class="text-reset">{{ $proposal->submitter?->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><x-lucide-clipboard-list class="me-2 icon" />Skema
                                Pengabdian</label>
                            <p class="text-reset">{{ $proposal->communityServiceScheme?->name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partner/Mitra Card -->
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">1.1 Mitra Pengabdian</h3>
                    @if ($this->canEdit())
                        <div class="card-actions">
                            <x-tabler.badge color="info">
                                <x-lucide-pencil class="icon icon-sm" />
                                Dapat Diedit
                            </x-tabler.badge>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @php $communityService = $proposal->detailable; @endphp

                    @if ($this->canEdit())
                        <div class="mb-3">
                            <label class="form-label required">
                                <x-lucide-users class="me-2 icon" />
                                Pilih Mitra
                            </label>
                            <select wire:model="partnerId"
                                class="form-select @error('partnerId') is-invalid @enderror">
                                <option value="">Pilih Mitra</option>
                                @foreach ($this->partners as $partner)
                                    <option value="{{ $partner->id }}">
                                        {{ $partner->name }}
                                        @if ($partner->institution)
                                            - {{ $partner->institution }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('partnerId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($partnerId)
                                @php
                                    $selectedPartner = $this->partners->firstWhere('id', $partnerId);
                                @endphp
                                @if ($selectedPartner)
                                    <div class="mt-2">
                                        <small class="form-hint text-muted">
                                            @if ($selectedPartner->email)
                                                <x-lucide-mail class="icon icon-sm" />
                                                {{ $selectedPartner->email }}
                                            @endif
                                            @if ($selectedPartner->country)
                                                <x-lucide-map-pin class="icon icon-sm ms-2" />
                                                {{ $selectedPartner->country }}
                                            @endif
                                        </small>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label"><x-lucide-users class="me-2 icon" />Mitra</label>
                            @if ($communityService?->partner)
                                <p class="text-reset fw-bold">{{ $communityService->partner->name }}</p>
                                @if ($communityService->partner->institution)
                                    <p class="mb-1 text-muted">
                                        <x-lucide-building class="icon icon-sm" />
                                        {{ $communityService->partner->institution }}
                                    </p>
                                @endif
                                @if ($communityService->partner->email)
                                    <p class="mb-1 text-muted">
                                        <x-lucide-mail class="icon icon-sm" />
                                        {{ $communityService->partner->email }}
                                    </p>
                                @endif
                                @if ($communityService->partner->country)
                                    <p class="mb-0 text-muted">
                                        <x-lucide-map-pin class="icon icon-sm" />
                                        {{ $communityService->partner->country }}
                                    </p>
                                @endif
                            @else
                                <p class="text-reset">—</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Partner Issue Summary Card -->
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">1.2 Ringkasan Masalah Mitra</h3>
                    @if ($this->canEdit())
                        <div class="card-actions">
                            <x-tabler.badge color="info">
                                <x-lucide-pencil class="icon icon-sm" />
                                Dapat Diedit
                            </x-tabler.badge>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if ($this->canEdit())
                        <div class="mb-3">
                            <label class="form-label required">
                                <x-lucide-alert-circle class="me-2 icon" />
                                Ringkasan Masalah Mitra
                            </label>
                            <textarea wire:model="partnerIssueSummary" rows="6"
                                class="form-control @error('partnerIssueSummary') is-invalid @enderror"
                                placeholder="Jelaskan permasalahan yang dihadapi mitra (minimal 50 karakter)"></textarea>
                            @error('partnerIssueSummary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                Minimal 50 karakter. Saat ini: {{ strlen($partnerIssueSummary) }} karakter
                            </small>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label"><x-lucide-alert-circle class="me-2 icon" />Ringkasan Masalah
                                Mitra</label>
                            <p class="text-reset">{{ $communityService?->partner_issue_summary ?? '—' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Solution Offered Card -->
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">1.3 Solusi yang Ditawarkan</h3>
                    @if ($this->canEdit())
                        <div class="card-actions">
                            <x-tabler.badge color="info">
                                <x-lucide-pencil class="icon icon-sm" />
                                Dapat Diedit
                            </x-tabler.badge>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if ($this->canEdit())
                        <div class="mb-3">
                            <label class="form-label required">
                                <x-lucide-lightbulb class="me-2 icon" />
                                Solusi yang Ditawarkan
                            </label>
                            <textarea wire:model="solutionOffered" rows="6"
                                class="form-control @error('solutionOffered') is-invalid @enderror"
                                placeholder="Jelaskan solusi yang ditawarkan untuk mengatasi masalah mitra (minimal 50 karakter)"></textarea>
                            @error('solutionOffered')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">
                                Minimal 50 karakter. Saat ini: {{ strlen($solutionOffered) }} karakter
                            </small>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label"><x-lucide-lightbulb class="me-2 icon" />Solusi yang
                                Ditawarkan</label>
                            <p class="text-reset">{{ $communityService?->solution_offered ?? '—' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviewer Notes Card -->
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">1.4 Catatan dan Rekomendasi Reviewer</h3>
                </div>
                @php
                    $completedReviewers = $proposal->reviewers->where('status', 'completed');
                @endphp
                @if ($completedReviewers->isEmpty())
                    <div class="card-body">
                        <p class="text-muted">Belum ada catatan dari reviewer</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($completedReviewers as $reviewer)
                            <div class="list-group-item">
                                <div class="mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <x-lucide-user-circle class="me-2 icon text-primary" />
                                            <strong>{{ $reviewer->user?->name ?? 'Reviewer' }}</strong>
                                        </div>
                                        <small class="text-muted">
                                            {{ $reviewer->updated_at?->format('d M Y H:i') }}
                                        </small>
                                    </div>
                                </div>

                                @if ($reviewer->review_notes)
                                    <div class="mb-2">
                                        <label class="form-label fw-bold mb-1">
                                            <x-lucide-message-square class="icon icon-sm" />
                                            Catatan Review:
                                        </label>
                                        <div class="p-3 bg-body-tertiary rounded">
                                            <p class="mb-0 text-reset">{{ $reviewer->review_notes }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($reviewer->recommendation)
                                    <div class="mb-0">
                                        <label class="form-label fw-bold mb-1">
                                            <x-lucide-star class="icon icon-sm" />
                                            Rekomendasi:
                                        </label>
                                        <p class="mb-0">
                                            @if ($reviewer->recommendation === 'approved')
                                                <x-tabler.badge color="success">
                                                    <x-lucide-check class="icon icon-sm" />
                                                    Disetujui
                                                </x-tabler.badge>
                                            @elseif ($reviewer->recommendation === 'revision_needed')
                                                <x-tabler.badge color="warning">
                                                    <x-lucide-alert-triangle class="icon icon-sm" />
                                                    Perlu Revisi
                                                </x-tabler.badge>
                                            @elseif ($reviewer->recommendation === 'rejected')
                                                <x-tabler.badge color="danger">
                                                    <x-lucide-x class="icon icon-sm" />
                                                    Ditolak
                                                </x-tabler.badge>
                                            @else
                                                <x-tabler.badge color="secondary">
                                                    {{ ucfirst(str_replace('_', ' ', $reviewer->recommendation)) }}
                                                </x-tabler.badge>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Save Button for Submitter -->
            @if ($this->canEdit())
                <div class="mb-3 card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" wire:click="save" class="btn btn-primary"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <x-lucide-save class="icon" />
                                    Simpan Perubahan
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <x-lucide-info class="icon icon-sm" />
                            Pastikan Anda telah memilih mitra dan mengisi ringkasan masalah serta solusi yang
                            ditawarkan sebelum menyimpan.
                        </small>
                    </div>
                </div>
            @endif
        </div>

        <!-- Section 2: RAB (Read-Only) -->
        <div id="section-rab" x-show="currentStep === 2">
            <div class="mb-3 card">
                <div class="card-header">
                    <h3 class="card-title">2.1 Rencana Anggaran Biaya (RAB)</h3>
                    <div class="card-actions">
                        <x-tabler.badge color="info">Read-Only</x-tabler.badge>
                    </div>
                </div>
                @if ($proposal->budgetItems->isEmpty())
                    <div class="card-body">
                        <div class="py-4 text-muted text-center">
                            <x-lucide-inbox class="mb-2 icon icon-lg" />
                            <p>Belum ada item anggaran</p>
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="card-table table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <th width="20%">Kelompok</th>
                                    <th width="25%">Komponen</th>
                                    <th width="10%">Volume</th>
                                    <th width="10%">Unit</th>
                                    <th width="15%">Harga Satuan</th>
                                    <th width="20%">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proposal->budgetItems as $item)
                                    <tr>
                                        <td>{{ $item->budgetGroup?->name ?? ($item->group ?? '-') }}</td>
                                        <td>{{ $item->budgetComponent?->name ?? ($item->component ?? '-') }}</td>
                                        <td class="text-center">{{ $item->volume }}</td>
                                        <td class="text-center">
                                            <x-tabler.badge variant="outline">
                                                {{ $item->budgetComponent?->unit ?? '-' }}
                                            </x-tabler.badge>
                                        </td>
                                         <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                         </td>
                                         <td class="text-end fw-bold">Rp
                                             {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                     </tr>
                                 @endforeach
                             </tbody>
                             <tfoot>
                                 <tr class="table-active">
                                     <th colspan="5" class="text-end">Total Anggaran:</th>
                                     <th class="text-end">
                                         <span class="text-primary">
                                             Rp
                                             {{ number_format($proposal->budgetItems->sum('total_price'), 0, ',', '.') }}
                                         </span>
                                     </th>
                                 </tr>
                             </tfoot>
                         </table>
                     </div>
                 @endif
             </div>
 
             <!-- Summary Card -->
             <div class="mb-3 card">
                 <div class="card-header">
                     <h3 class="card-title">Ringkasan Anggaran</h3>
                 </div>
                 <div class="card-body">
                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label class="form-label">Jumlah Item Anggaran</label>
                                 <p class="text-reset h4">{{ $proposal->budgetItems->count() }} item</p>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label class="form-label">Total Anggaran</label>
                                 <p class="text-primary text-reset h4">
                                     Rp {{ number_format($proposal->budgetItems->sum('total_price'), 0, ',', '.') }}
                                 </p>
                             </div>
                         </div>
                     </div>
                     @if ($proposal->sbk_value)
                         <div class="row">
                             <div class="col-md-12">
                                 <div class="mb-0">
                                     <label class="form-label">Nilai SBK</label>
                                     <p class="text-reset">Rp {{ number_format($proposal->sbk_value, 0, ',', '.') }}
                                     </p>
                                 </div>
                             </div>
                         </div>
                     @endif

                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-4">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn" @click="currentStep--" x-show="currentStep > 1">
                    <x-lucide-arrow-left class="icon" />
                    Kembali
                </button>
                <div x-show="currentStep === 1"></div>
                <button type="button" class="btn btn-primary" @click="currentStep++" x-show="currentStep < 2">
                    Selanjutnya
                    <x-lucide-arrow-right class="icon" />
                </button>
            </div>
        </div>
    </div>
</div>
