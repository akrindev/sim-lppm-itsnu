<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\Proposal;
use Illuminate\View\View;
use Livewire\Component;

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
}
