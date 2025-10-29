<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Detail Proposal Penelitian')]
class Show extends Component
{
    public ProposalForm $form;

    /**
     * Mount the component with proposal.
     */
    public function mount(Proposal $proposal): void
    {
        $this->form->setProposal($proposal);
    }

    /**
     * Delete the proposal
     */
    public function delete(): void
    {
        // Check authorization - user can only delete their own proposals
        if ($this->form->proposal->submitter_id !== Auth::user()->id) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus proposal ini');

            return;
        }

        try {
            $this->form->delete();
            session()->flash('success', 'Proposal penelitian berhasil dihapus');
            $this->redirect(route('research.proposal.index'));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus proposal: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.show', [
            'proposal' => $this->form->proposal->load([
                'submitter',
                'focusArea',
                'researchScheme',
                'theme',
                'topic',
                'nationalPriority',
                'teamMembers',
            ]),
        ]);
    }

    /**
     * Accept proposal team member invitation.
     */
    public function acceptMember(): void
    {
        $user = request()->user();
        $proposal = $this->form->proposal;
        if ($proposal->teamMembers->contains($user)) {
            $proposal->teamMembers()->updateExistingPivot($user->id, ['status' => 'accepted']);
            $this->dispatch('memberAccepted');
        }
    }

    /**
     * Reject proposal team member invitation.
     */
    public function rejectMember(): void
    {
        $user = request()->user();
        $proposal = $this->form->proposal;
        if ($proposal->teamMembers->contains($user)) {
            $proposal->teamMembers()->updateExistingPivot($user->id, ['status' => 'rejected']);
            $this->dispatch('memberRejected');
        }
    }
}
