<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Actions\DekanApprovalAction;
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

    public string $approvalDecision = '';

    public string $approvalNotes = '';

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
                'communityServiceScheme',
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
            session()->flash('success', 'Anda telah menerima undangan sebagai anggota tim proposal ini');
            $this->dispatch('close-modal', modalId: 'acceptMemberModal');
            $this->dispatch('memberAccepted');
            $this->dispatch('$refresh');
            $this->form->setProposal($proposal->fresh());
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
            session()->flash('success', 'Anda telah menolak undangan sebagai anggota tim proposal ini');
            $this->dispatch('close-modal', modalId: 'rejectMemberModal');
            $this->dispatch('memberRejected');
            $this->form->setProposal($proposal->fresh());
        }
    }

    /**
     * Process Dekan approval.
     */
    public function processApproval(): void
    {
        if (! $this->approvalDecision) {
            session()->flash('error', 'Keputusan approval harus dipilih');

            return;
        }

        $proposal = $this->form->proposal;

        // Execute approval action
        $action = new DekanApprovalAction;
        $result = $action->execute($proposal, $this->approvalDecision, $this->approvalNotes);

        if ($result['success']) {
            session()->flash('success', $result['message']);
            $this->dispatch('close-modal', modalId: 'approvalModal');
            $this->cancelApproval();
            $this->form->setProposal($proposal->fresh());
        } else {
            session()->flash('error', $result['message']);
        }
    }

    /**
     * Cancel approval and reset form.
     */
    public function cancelApproval(): void
    {
        $this->approvalDecision = '';
        $this->approvalNotes = '';
    }

    /**
     * Submit Dekan decision (alias for processApproval).
     */
    public function submitDekanDecision(): void
    {
        $this->processApproval();
    }
}
