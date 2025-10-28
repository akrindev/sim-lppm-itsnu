<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Detail Proposal Pengabdian Masyarakat')]
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
            session()->flash('success', 'Proposal pengabdian masyarakat berhasil dihapus');
            $this->redirect(route('community-service.proposal.index'));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus proposal: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.community-service.proposal.show', [
            'proposal' => $this->form->proposal->load([
                'submitter',
                'focusArea',
                'theme',
                'topic',
                'nationalPriority',
                'teamMembers',
            ]),
        ]);
    }
}
