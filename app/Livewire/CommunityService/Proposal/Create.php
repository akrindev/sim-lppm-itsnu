<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\FocusArea;
use App\Models\NationalPriority;
use App\Models\ScienceCluster;
use App\Models\Theme;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Buat Proposal Pengabdian Masyarakat')]
class Create extends Component
{
    public ProposalForm $form;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        //
    }

    /**
     * Save the proposal using the form object (Community Service type)
     */
    public function save(): void
    {
        // Validate the form before attempting to store
        try {
            $this->form->validate();
        } catch (\Exception $e) {
            // Validation failed, errors will be displayed in the form
            // Do NOT reset the form - keep the data so user can correct errors
            return;
        }

        try {
            $proposal = $this->form->storeCommunityService(Auth::user()->getKey());
            session()->flash('success', 'Proposal pengabdian masyarakat berhasil dibuat');
            $this->redirect(route('community-service.proposal.show', $proposal));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat proposal: '.$e->getMessage());
        }
    }

    #[Computed]
    public function focusAreas()
    {
        return FocusArea::all();
    }

    #[Computed]
    public function themes()
    {
        return Theme::all();
    }

    #[Computed]
    public function topics()
    {
        return Topic::all();
    }

    #[Computed]
    public function nationalPriorities()
    {
        return NationalPriority::all();
    }

    #[Computed]
    public function scienceClusters()
    {
        return ScienceCluster::all();
    }

    #[Computed]
    public function partners()
    {
        return \App\Models\Partner::all();
    }

    public function render(): View
    {
        return view('livewire.community-service.proposal.create');
    }
}
