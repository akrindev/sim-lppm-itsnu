<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Template Proposal
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <!-- Research Template -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Template Penelitian</h3>
                        </div>
                        <div class="card-body">
                            @if (session('success_research'))
                                <div class="alert alert-success">
                                    {{ session('success_research') }}
                                </div>
                            @endif
                            @if (session('error_research'))
                                <div class="alert alert-danger">
                                    {{ session('error_research') }}
                                </div>
                            @endif

                            <form wire:submit.prevent="saveResearchTemplate">
                                <div class="mb-3" x-data="{ uploading: false, progress: 0 }"
                                    x-on:livewire-upload-start="uploading = true"
                                    x-on:livewire-upload-finish="uploading = false"
                                    x-on:livewire-upload-error="uploading = false"
                                    x-on:livewire-upload-progress="progress = $event.detail.progress">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="research_template">
                                    @error('research_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <div class="progress mt-2" x-show="uploading" style="display: none;">
                                        <div class="progress-bar" role="progressbar" :style="`width: ${progress}%`"
                                            :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100">
                                            <span class="visually-hidden" x-text="`${progress}% Complete`"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="saveResearchTemplate">Unggah</span>
                                        <span wire:loading wire:target="saveResearchTemplate">Mengunggah...</span>
                                    </button>
                                </div>
                            </form>

                            @if ($this->researchTemplateMedia)
                                <div class="mt-3">
                                    <label class="form-label">Template Saat Ini</label>
                                    <button wire:click="downloadResearchTemplate" class="btn btn-outline-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-download" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                            <path d="M7 11l5 5l5 -5"></path>
                                            <path d="M12 4l0 12"></path>
                                        </svg>
                                        {{ $this->researchTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Community Service Template -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Template Pengabdian Masyarakat</h3>
                        </div>
                        <div class="card-body">
                            @if (session('success_community_service'))
                                <div class="alert alert-success">
                                    {{ session('success_community_service') }}
                                </div>
                            @endif
                            @if (session('error_community_service'))
                                <div class="alert alert-danger">
                                    {{ session('error_community_service') }}
                                </div>
                            @endif

                            <form wire:submit.prevent="saveCommunityServiceTemplate">
                                <div class="mb-3" x-data="{ uploading: false, progress: 0 }"
                                    x-on:livewire-upload-start="uploading = true"
                                    x-on:livewire-upload-finish="uploading = false"
                                    x-on:livewire-upload-error="uploading = false"
                                    x-on:livewire-upload-progress="progress = $event.detail.progress">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="community_service_template">
                                    @error('community_service_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <div class="progress mt-2" x-show="uploading" style="display: none;">
                                        <div class="progress-bar" role="progressbar" :style="`width: ${progress}%`"
                                            :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100">
                                            <span class="visually-hidden" x-text="`${progress}% Complete`"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                        <span wire:loading.remove
                                            wire:target="saveCommunityServiceTemplate">Unggah</span>
                                        <span wire:loading
                                            wire:target="saveCommunityServiceTemplate">Mengunggah...</span>
                                    </button>
                                </div>
                            </form>

                            @if ($this->communityServiceTemplateMedia)
                                <div class="mt-3">
                                    <label class="form-label">Template Saat Ini</label>
                                    <button wire:click="downloadCommunityServiceTemplate"
                                        class="btn btn-outline-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-download" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                            <path d="M7 11l5 5l5 -5"></path>
                                            <path d="M12 4l0 12"></path>
                                        </svg>
                                        {{ $this->communityServiceTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards mt-3">
                <div class="col-12">
                    <h2 class="page-title mb-3">Template Persetujuan Proposal</h2>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Persetujuan Proposal Penelitian</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveResearchApprovalTemplate">
                                <div class="mb-3">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="research_approval_template">
                                    @error('research_approval_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    Unggah
                                </button>
                            </form>
                            @if ($this->researchApprovalTemplateMedia)
                                <div class="mt-3">
                                    <button wire:click="downloadResearchApprovalTemplate" class="btn btn-ghost-primary w-100">
                                        Unduh: {{ $this->researchApprovalTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Persetujuan Proposal PKM</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveCommunityServiceApprovalTemplate">
                                <div class="mb-3">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="community_service_approval_template">
                                    @error('community_service_approval_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    Unggah
                                </button>
                            </form>
                            @if ($this->communityServiceApprovalTemplateMedia)
                                <div class="mt-3">
                                    <button wire:click="downloadCommunityServiceApprovalTemplate" class="btn btn-ghost-primary w-100">
                                        Unduh: {{ $this->communityServiceApprovalTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards mt-3">
                <div class="col-12">
                    <h2 class="page-title mb-3">Template Pengesahan Laporan</h2>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pengesahan Laporan Penelitian</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveResearchReportEndorsementTemplate">
                                <div class="mb-3">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="research_report_endorsement_template">
                                    @error('research_report_endorsement_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    Unggah
                                </button>
                            </form>
                            @if ($this->researchReportEndorsementTemplateMedia)
                                <div class="mt-3">
                                    <button wire:click="downloadResearchReportEndorsementTemplate" class="btn btn-ghost-primary w-100">
                                        Unduh: {{ $this->researchReportEndorsementTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pengesahan Laporan PKM</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveCommunityServiceReportEndorsementTemplate">
                                <div class="mb-3">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="community_service_report_endorsement_template">
                                    @error('community_service_report_endorsement_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    Unggah
                                </button>
                            </form>
                            @if ($this->communityServiceReportEndorsementTemplateMedia)
                                <div class="mt-3">
                                    <button wire:click="downloadCommunityServiceReportEndorsementTemplate" class="btn btn-ghost-primary w-100">
                                        Unduh: {{ $this->communityServiceReportEndorsementTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards mt-3">
                <div class="col-12">
                    <h2 class="page-title mb-3">Template Surat Kesanggupan Mitra</h2>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Surat Kesanggupan Mitra Penelitian</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveResearchPartnerCommitmentTemplate">
                                <div class="mb-3">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="research_partner_commitment_template">
                                    @error('research_partner_commitment_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    Unggah
                                </button>
                            </form>
                            @if ($this->researchPartnerCommitmentTemplateMedia)
                                <div class="mt-3">
                                    <button wire:click="downloadResearchPartnerCommitmentTemplate" class="btn btn-ghost-primary w-100">
                                        Unduh: {{ $this->researchPartnerCommitmentTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Surat Kesanggupan Mitra PKM</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveCommunityServicePartnerCommitmentTemplate">
                                <div class="mb-3">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="community_service_partner_commitment_template">
                                    @error('community_service_partner_commitment_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    Unggah
                                </button>
                            </form>
                            @if ($this->communityServicePartnerCommitmentTemplateMedia)
                                <div class="mt-3">
                                    <button wire:click="downloadCommunityServicePartnerCommitmentTemplate" class="btn btn-ghost-primary w-100">
                                        Unduh: {{ $this->communityServicePartnerCommitmentTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards mt-3">
                <div class="col-12">
                    <h2 class="page-title mb-3">Template Monev Internal</h2>
                </div>
                <!-- Monev Berita Acara -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Berita Acara Monev</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveMonevBeritaAcaraTemplate">
                                <div class="mb-3">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="monev_berita_acara_template">
                                    @error('monev_berita_acara_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    Unggah
                                </button>
                            </form>
                            @if ($this->monevBeritaAcaraTemplateMedia)
                                <div class="mt-3">
                                    <button wire:click="downloadMonevBeritaAcaraTemplate" class="btn btn-ghost-primary w-100">
                                        Unduh: {{ $this->monevBeritaAcaraTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Monev Borang -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Borang Monev</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveMonevBorangTemplate">
                                <div class="mb-3">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="monev_borang_template">
                                    @error('monev_borang_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    Unggah
                                </button>
                            </form>
                            @if ($this->monevBorangTemplateMedia)
                                <div class="mt-3">
                                    <button wire:click="downloadMonevBorangTemplate" class="btn btn-ghost-primary w-100">
                                        Unduh: {{ $this->monevBorangTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Monev Rekap Penilaian -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rekap Penilaian Monev</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveMonevRekapPenilaianTemplate">
                                <div class="mb-3">
                                    <label class="form-label">Unggah Template Baru</label>
                                    <input type="file" class="form-control" wire:model="monev_rekap_penilaian_template">
                                    @error('monev_rekap_penilaian_template')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    Unggah
                                </button>
                            </form>
                            @if ($this->monevRekapPenilaianTemplateMedia)
                                <div class="mt-3">
                                    <button wire:click="downloadMonevRekapPenilaianTemplate" class="btn btn-ghost-primary w-100">
                                        Unduh: {{ $this->monevRekapPenilaianTemplateMedia->file_name }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
