<?php

namespace App\Livewire\Settings;

use App\Livewire\Concerns\HasToast;
use App\Models\BudgetCap;
use App\Models\Setting;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProposalSchedule extends Component
{
    use HasToast;

    public $research_start_date;

    public $research_end_date;

    public $community_service_start_date;

    public $community_service_end_date;

    public function mount()
    {
        $this->research_start_date = Setting::where('key', 'research_proposal_start_date')->value('value');
        $this->research_end_date = Setting::where('key', 'research_proposal_end_date')->value('value');
        $this->community_service_start_date = Setting::where('key', 'community_service_proposal_start_date')->value('value');
        $this->community_service_end_date = Setting::where('key', 'community_service_proposal_end_date')->value('value');
    }

    public function save()
    {
        $this->validate([
            'research_start_date' => 'nullable|date',
            'research_end_date' => 'nullable|date|after_or_equal:research_start_date',
            'community_service_start_date' => 'nullable|date',
            'community_service_end_date' => 'nullable|date|after_or_equal:community_service_start_date',
        ]);

        Setting::updateOrCreate(['key' => 'research_proposal_start_date'], ['value' => $this->research_start_date]);
        Setting::updateOrCreate(['key' => 'research_proposal_end_date'], ['value' => $this->research_end_date]);
        Setting::updateOrCreate(['key' => 'community_service_proposal_start_date'], ['value' => $this->community_service_start_date]);
        Setting::updateOrCreate(['key' => 'community_service_proposal_end_date'], ['value' => $this->community_service_end_date]);

        $message = 'Jadwal proposal berhasil disimpan.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function render()
    {
        return view('livewire.settings.proposal-schedule');
    }

    /**
     * Get budget cap for current year
     */
    #[Computed]
    public function currentYearBudgetCap()
    {
        $currentYear = (int) date('Y');

        return BudgetCap::where('year', $currentYear)->first();
    }
}
