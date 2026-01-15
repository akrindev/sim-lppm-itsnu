<?php

namespace App\Livewire\Settings;

use App\Livewire\Concerns\HasToast;
use App\Models\Setting;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProposalTemplate extends Component
{
    use HasToast, WithFileUploads;

    public $research_template;
    public $community_service_template;

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

    public function downloadResearchTemplate()
    {
        $setting = Setting::where('key', 'research_proposal_template')->first();
        if ($setting && $setting->hasMedia('template')) {
            return response()->download($setting->getFirstMedia('template')->getPath(), $setting->getFirstMedia('template')->file_name);
        }
        $message = 'Template belum tersedia.';
        session()->flash('error', $message);
        $this->toastError($message);
    }

    public function downloadCommunityServiceTemplate()
    {
        $setting = Setting::where('key', 'community_service_proposal_template')->first();
        if ($setting && $setting->hasMedia('template')) {
            return response()->download($setting->getFirstMedia('template')->getPath(), $setting->getFirstMedia('template')->file_name);
        }
        $message = 'Template belum tersedia.';
        session()->flash('error', $message);
        $this->toastError($message);
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

    public function render()
    {
        return view('livewire.settings.proposal-template');
    }
}
