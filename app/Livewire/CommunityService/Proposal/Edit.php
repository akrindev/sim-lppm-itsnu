<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\FocusArea;
use App\Models\NationalPriority;
use App\Models\Proposal;
use App\Models\ScienceCluster;
use App\Models\Theme;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Edit Proposal Pengabdian Masyarakat')]
class Edit extends Component
{
    public ProposalForm $form;

    public string $author_name = '';

    public string $componentId;

    /**
     * Mount the component with proposal data.
     */
    public function mount(Proposal $proposal): void
    {
        // Check authorization - user can only edit their own proposals
        if ($proposal->submitter_id !== Auth::user()->id) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengedit proposal ini');
            $this->redirect(route('community-service.proposal.index'));

            return;
        }

        // Set author name
        $this->author_name = Str::title(Auth::user()->name . ' (' . Auth::user()->identity->identity_id . ')');

        // Generate a unique, stable component ID for this instance
        $this->componentId = 'lwc-' . Str::random(10);

        // Load proposal data into form
        $this->form->setProposal($proposal);
    }

    /**
     * Handle members updated event from TeamMembersForm
     */
    #[On('members-updated')]
    public function updateMembers(array $members): void
    {
        $this->form->members = $members;
    }

    /**
     * Update the proposal using the form object
     */
    public function save(): void
    {
        $this->form->validate();

        try {
            $this->form->update();

            session()->flash('success', 'Proposal pengabdian masyarakat berhasil diperbarui');

            $this->redirect(route('community-service.proposal.show', $this->form->proposal), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui proposal: ' . $e->getMessage());
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
        return view('livewire.community-service.proposal.edit');
    }
}
