<?php

namespace App\Livewire\Settings;

use App\Livewire\Concerns\HasToast;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProposalTemplate extends Component
{
    use HasToast, WithFileUploads;

    public $research_template;

    public $community_service_template;

    public $research_approval_template;

    public $community_service_approval_template;

    public $research_report_endorsement_template;

    public $community_service_report_endorsement_template;

    public $research_partner_commitment_template;

    public $community_service_partner_commitment_template;

    public $monev_berita_acara_template;

    public $monev_borang_template;

    public $monev_rekap_penilaian_template;

    public function saveResearchTemplate()
    {
        $this->validate([
            'research_template' => 'required|file|mimes:doc,docx,pdf|max:10240', // 10MB max
        ]);

        $setting = Setting::firstOrCreate(['key' => 'research_proposal_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->research_template->getRealPath())
            ->usingName($this->research_template->getClientOriginalName())
            ->usingFileName($this->research_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->research_template = null;
        unset($this->researchTemplateMedia); // Invalidate computed property

        $message = 'Template penelitian berhasil diunggah.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function saveCommunityServiceTemplate()
    {
        $this->validate([
            'community_service_template' => 'required|file|mimes:doc,docx,pdf|max:10240', // 10MB max
        ]);

        $setting = Setting::firstOrCreate(['key' => 'community_service_proposal_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->community_service_template->getRealPath())
            ->usingName($this->community_service_template->getClientOriginalName())
            ->usingFileName($this->community_service_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->community_service_template = null;
        unset($this->communityServiceTemplateMedia); // Invalidate computed property

        $message = 'Template pengabdian berhasil diunggah.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function saveMonevBeritaAcaraTemplate()
    {
        $this->validate([
            'monev_berita_acara_template' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $setting = Setting::firstOrCreate(['key' => 'monev_berita_acara_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->monev_berita_acara_template->getRealPath())
            ->usingName($this->monev_berita_acara_template->getClientOriginalName())
            ->usingFileName($this->monev_berita_acara_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->monev_berita_acara_template = null;
        unset($this->monevBeritaAcaraTemplateMedia);

        $this->toastSuccess('Template Berita Acara Monev berhasil diunggah.');
    }

    public function saveResearchApprovalTemplate()
    {
        $this->validate([
            'research_approval_template' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $setting = Setting::firstOrCreate(['key' => 'research_approval_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->research_approval_template->getRealPath())
            ->usingName($this->research_approval_template->getClientOriginalName())
            ->usingFileName($this->research_approval_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->research_approval_template = null;
        unset($this->researchApprovalTemplateMedia);

        $this->toastSuccess('Template halaman persetujuan penelitian berhasil diunggah.');
    }

    public function saveCommunityServiceApprovalTemplate()
    {
        $this->validate([
            'community_service_approval_template' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $setting = Setting::firstOrCreate(['key' => 'community_service_approval_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->community_service_approval_template->getRealPath())
            ->usingName($this->community_service_approval_template->getClientOriginalName())
            ->usingFileName($this->community_service_approval_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->community_service_approval_template = null;
        unset($this->communityServiceApprovalTemplateMedia);

        $this->toastSuccess('Template halaman persetujuan PKM berhasil diunggah.');
    }

    public function saveResearchReportEndorsementTemplate()
    {
        $this->validate([
            'research_report_endorsement_template' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $setting = Setting::firstOrCreate(['key' => 'research_report_endorsement_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->research_report_endorsement_template->getRealPath())
            ->usingName($this->research_report_endorsement_template->getClientOriginalName())
            ->usingFileName($this->research_report_endorsement_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->research_report_endorsement_template = null;
        unset($this->researchReportEndorsementTemplateMedia);

        $this->toastSuccess('Template halaman pengesahan laporan penelitian berhasil diunggah.');
    }

    public function saveCommunityServiceReportEndorsementTemplate()
    {
        $this->validate([
            'community_service_report_endorsement_template' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $setting = Setting::firstOrCreate(['key' => 'community_service_report_endorsement_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->community_service_report_endorsement_template->getRealPath())
            ->usingName($this->community_service_report_endorsement_template->getClientOriginalName())
            ->usingFileName($this->community_service_report_endorsement_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->community_service_report_endorsement_template = null;
        unset($this->communityServiceReportEndorsementTemplateMedia);

        $this->toastSuccess('Template halaman pengesahan laporan PKM berhasil diunggah.');
    }

    public function saveResearchPartnerCommitmentTemplate()
    {
        $this->validate([
            'research_partner_commitment_template' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $setting = Setting::firstOrCreate(['key' => 'research_partner_commitment_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->research_partner_commitment_template->getRealPath())
            ->usingName($this->research_partner_commitment_template->getClientOriginalName())
            ->usingFileName($this->research_partner_commitment_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->research_partner_commitment_template = null;
        unset($this->researchPartnerCommitmentTemplateMedia);

        $this->toastSuccess('Template surat kesanggupan mitra (penelitian) berhasil diunggah.');
    }

    public function saveCommunityServicePartnerCommitmentTemplate()
    {
        $this->validate([
            'community_service_partner_commitment_template' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $setting = Setting::firstOrCreate(['key' => 'community_service_partner_commitment_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->community_service_partner_commitment_template->getRealPath())
            ->usingName($this->community_service_partner_commitment_template->getClientOriginalName())
            ->usingFileName($this->community_service_partner_commitment_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->community_service_partner_commitment_template = null;
        unset($this->communityServicePartnerCommitmentTemplateMedia);

        $this->toastSuccess('Template surat kesanggupan mitra (PKM) berhasil diunggah.');
    }

    public function saveMonevBorangTemplate()
    {
        $this->validate([
            'monev_borang_template' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $setting = Setting::firstOrCreate(['key' => 'monev_borang_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->monev_borang_template->getRealPath())
            ->usingName($this->monev_borang_template->getClientOriginalName())
            ->usingFileName($this->monev_borang_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->monev_borang_template = null;
        unset($this->monevBorangTemplateMedia);

        $this->toastSuccess('Template Borang Monev berhasil diunggah.');
    }

    public function saveMonevRekapPenilaianTemplate()
    {
        $this->validate([
            'monev_rekap_penilaian_template' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $setting = Setting::firstOrCreate(['key' => 'monev_rekap_penilaian_template']);
        $setting->clearMediaCollection('template');
        $setting->addMedia($this->monev_rekap_penilaian_template->getRealPath())
            ->usingName($this->monev_rekap_penilaian_template->getClientOriginalName())
            ->usingFileName($this->monev_rekap_penilaian_template->getClientOriginalName())
            ->toMediaCollection('template');

        $this->monev_rekap_penilaian_template = null;
        unset($this->monevRekapPenilaianTemplateMedia);

        $this->toastSuccess('Template Rekap Penilaian Monev berhasil diunggah.');
    }

    public function downloadResearchTemplate()
    {
        return $this->downloadTemplateByKey('research_proposal_template');
    }

    public function downloadCommunityServiceTemplate()
    {
        return $this->downloadTemplateByKey('community_service_proposal_template');
    }

    public function downloadMonevBeritaAcaraTemplate()
    {
        return $this->downloadTemplateByKey('monev_berita_acara_template');
    }

    public function downloadMonevBorangTemplate()
    {
        return $this->downloadTemplateByKey('monev_borang_template');
    }

    public function downloadMonevRekapPenilaianTemplate()
    {
        return $this->downloadTemplateByKey('monev_rekap_penilaian_template');
    }

    public function downloadResearchApprovalTemplate()
    {
        return $this->downloadTemplateByKey('research_approval_template');
    }

    public function downloadCommunityServiceApprovalTemplate()
    {
        return $this->downloadTemplateByKey('community_service_approval_template');
    }

    public function downloadResearchReportEndorsementTemplate()
    {
        return $this->downloadTemplateByKey('research_report_endorsement_template');
    }

    public function downloadCommunityServiceReportEndorsementTemplate()
    {
        return $this->downloadTemplateByKey('community_service_report_endorsement_template');
    }

    public function downloadResearchPartnerCommitmentTemplate()
    {
        return $this->downloadTemplateByKey('research_partner_commitment_template');
    }

    public function downloadCommunityServicePartnerCommitmentTemplate()
    {
        return $this->downloadTemplateByKey('community_service_partner_commitment_template');
    }

    private function downloadTemplateByKey(string $key)
    {
        $setting = Setting::where('key', $key)->first();

        if (! $setting || ! $setting->hasMedia('template')) {
            $message = 'Template belum tersedia.';
            session()->flash('error', $message);
            $this->toastError($message);

            return null;
        }

        $media = $setting->getFirstMedia('template');
        $relativePath = $media->getPathRelativeToRoot();

        if (! Storage::disk($media->disk)->exists($relativePath)) {
            $message = 'Template tidak ditemukan di penyimpanan.';
            session()->flash('error', $message);
            $this->toastError($message);

            return null;
        }

        return $media->toResponse(request());
    }

    #[Computed]
    public function researchTemplateMedia()
    {
        $setting = Setting::where('key', 'research_proposal_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function communityServiceTemplateMedia()
    {
        $setting = Setting::where('key', 'community_service_proposal_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function monevBeritaAcaraTemplateMedia()
    {
        $setting = Setting::where('key', 'monev_berita_acara_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function monevBorangTemplateMedia()
    {
        $setting = Setting::where('key', 'monev_borang_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function monevRekapPenilaianTemplateMedia()
    {
        $setting = Setting::where('key', 'monev_rekap_penilaian_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function researchApprovalTemplateMedia()
    {
        $setting = Setting::where('key', 'research_approval_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function communityServiceApprovalTemplateMedia()
    {
        $setting = Setting::where('key', 'community_service_approval_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function researchReportEndorsementTemplateMedia()
    {
        $setting = Setting::where('key', 'research_report_endorsement_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function communityServiceReportEndorsementTemplateMedia()
    {
        $setting = Setting::where('key', 'community_service_report_endorsement_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function researchPartnerCommitmentTemplateMedia()
    {
        $setting = Setting::where('key', 'research_partner_commitment_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    #[Computed]
    public function communityServicePartnerCommitmentTemplateMedia()
    {
        $setting = Setting::where('key', 'community_service_partner_commitment_template')->first();

        return $setting ? $setting->getFirstMedia('template') : null;
    }

    public function render()
    {
        return view('livewire.settings.proposal-template');
    }
}
