<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\FocusArea;
use App\Models\NationalPriority;
use App\Models\ResearchScheme;
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
#[Title('Buat Proposal Penelitian')]
class Create extends Component
{
    public ProposalForm $form;


    public string $author_name = '';


    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->author_name = Str::title(Auth::user()->name .  ' (' . Auth::user()->identity->identity_id . ')');
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
     * Save the proposal using the form object
     */
    public function save(): void
    {
        $this->form->validate();

        try {
            $proposal = $this->form->store(Auth::user()->getKey());
            session()->flash('success', 'Proposal penelitian berhasil dibuat');
            $this->redirect(route('research.proposal.show', $proposal));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat proposal: ' . $e->getMessage());
        }
    }

    #[Computed]
    public function schemes()
    {
        return ResearchScheme::all();
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

    public function render(): View
    {
        return view('livewire.research.proposal.create');
    }
}
