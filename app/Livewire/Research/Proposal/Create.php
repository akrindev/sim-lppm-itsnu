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
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Buat Proposal Penelitian')]
class Create extends Component
{
    public ProposalForm $form;

    public string $componentId;

    public string $member_nidn = '';

    public string $member_tugas = '';

    public bool $showMemberModal = false;

    // Member verification
    public ?array $foundMember = null;

    public bool $memberFound = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        // Generate a unique, stable component ID for this instance
        $this->componentId = 'lwc-' . Str::random(10);
    }

    /**
     * Add member to the form
     */
    public function addMember(): void
    {
        $this->validate([
            'member_nidn' => 'required|string|max:255',
            'member_tugas' => 'required|string|max:500',
        ]);

        $this->form->members[] = [
            'nidn' => $this->member_nidn,
            'tugas' => $this->member_tugas,
        ];

        $this->resetMemberForm();
    }

    /**
     * Remove member from form
     */
    public function removeMember(int $index): void
    {
        unset($this->form->members[$index]);
        $this->form->members = array_values($this->form->members);
    }

    /**
     * Check if member identity exists in system
     */
    public function checkMember(): void
    {
        $this->validate([
            'member_nidn' => 'required|string|max:255',
        ]);

        // Search for user by identity_id (NIDN/NIP)
        $identity = \App\Models\Identity::where('identity_id', $this->member_nidn)
            ->with('user', 'institution', 'studyProgram')
            ->first();

        if ($identity) {
            $this->memberFound = true;
            $this->foundMember = [
                'name' => $identity->user->name,
                'email' => $identity->user->email,
                'institution' => $identity->institution?->name,
                'study_program' => $identity->studyProgram?->name,
                'identity_type' => $identity->type,
            ];
        } else {
            $this->memberFound = false;
            $this->foundMember = null;
            $this->addError('member_nidn', 'NIDN/NIP tidak ditemukan dalam sistem');
        }
    }

    /**
     * Reset member modal and form state
     */
    public function resetMemberForm(): void
    {
        $this->member_nidn = '';
        $this->member_tugas = '';
        $this->memberFound = false;
        $this->foundMember = null;
        $this->resetErrorBag();
        $this->showMemberModal = false;
    }

    /**
     * Save the proposal using the form object
     */
    public function save(): void
    {
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
