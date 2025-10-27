<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\Proposal;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
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
     * Mount the component with proposal data.
     */
    public function mount(Proposal $proposal): void
    {
        // Generate a unique, stable component ID for this instance
        $this->componentId = 'lwc-' . Str::random(10);

        // Load proposal data into form
        $this->form->setProposal($proposal);
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

        // Get member details
        $identity = \App\Models\Identity::where('identity_id', $this->member_nidn)
            ->with('user')
            ->first();

        if (! $identity || ! $identity->user) {
            $this->addError('member_nidn', 'Anggota tidak ditemukan dalam sistem');
            return;
        }

        // Check if member already added
        $alreadyAdded = collect($this->form->members)->contains(function ($member) {
            return $member['nidn'] === $this->member_nidn;
        });

        if ($alreadyAdded) {
            $this->addError('member_nidn', 'Anggota ini sudah ditambahkan');
            return;
        }

        $this->form->members[] = [
            'name' => $identity->user->name,
            'nidn' => $this->member_nidn,
            'tugas' => $this->member_tugas,
        ];

        $this->resetMemberForm();

        // Close the modal
        $this->dispatch('close-modal', 'modal-add-member');
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
     * Update the proposal using the form object
     */
    public function save(): void
    {
        try {
            $this->form->update();
            session()->flash('success', 'Proposal penelitian berhasil diperbarui');
            $this->redirect(route('research.proposal.show', $this->form->proposal));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui proposal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.research.proposal.edit', [
            'schemes' => \App\Models\ResearchScheme::all(),
            'focusAreas' => \App\Models\FocusArea::all(),
            'themes' => \App\Models\Theme::all(),
            'topics' => \App\Models\Topic::all(),
            'nationalPriorities' => \App\Models\NationalPriority::all(),
            'scienceClusters' => \App\Models\ScienceCluster::all(),
        ]);
    }
}
