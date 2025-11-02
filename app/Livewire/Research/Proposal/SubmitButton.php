<?php

namespace App\Livewire\Research\Proposal;

use App\Enums\ProposalStatus;
use App\Livewire\Actions\SubmitProposalAction;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubmitButton extends Component
{
    public string $proposalId = '';

    public function mount(string $proposalId): void
    {
        $this->proposalId = $proposalId;
    }

    #[Computed]
    public function proposal()
    {
        return Proposal::find($this->proposalId);
    }

    #[Computed]
    public function canSubmit(): bool
    {
        $proposal = $this->proposal;
        $allowedStatuses = [
            ProposalStatus::DRAFT,
            ProposalStatus::NEED_ASSIGNMENT,
            ProposalStatus::REVISION_NEEDED,
        ];

        return in_array($proposal->status, $allowedStatuses)
            && $proposal->allTeamMembersAccepted()
            && Auth::id() === $proposal->submitter_id;
    }

    #[Computed]
    public function pendingMembers()
    {
        return $this->proposal->pendingTeamMembers()->get();
    }

    #[Computed]
    public function rejectedMembers()
    {
        return $this->proposal->teamMembers()
            ->wherePivot('status', 'rejected')
            ->get();
    }

    public function submit(): void
    {
        $proposal = $this->proposal;
        $action = new SubmitProposalAction;
        $result = $action->execute($proposal);

        if ($result['success']) {
            $this->dispatch('success', message: $result['message']);
            $this->dispatch('proposal-submitted', proposalId: $proposal->id);
            $this->redirect(route('research.proposal.show', $proposal->id));
        } else {
            $this->dispatch('error', message: $result['message']);
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.submit-button');
    }
}
