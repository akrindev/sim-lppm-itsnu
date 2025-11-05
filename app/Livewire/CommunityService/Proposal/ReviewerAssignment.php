<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Actions\AssignReviewersAction;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ReviewerAssignment extends Component
{
    public string $proposalId = '';

    public string $confirmingRemoveReviewerId = '';

    #[Validate('required')]
    public string $selectedReviewer = '';

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
    public function availableReviewers()
    {
        return User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['reviewer']);
        })
            ->with('identity:id,user_id,identity_id')
            ->get();
    }

    #[Computed]
    public function currentReviewers()
    {
        return $this->proposal->reviewers()
            ->with('user')
            ->get();
    }

    public function assignReviewers(): void
    {
        $this->validate();

        $proposal = $this->proposal;
        $action = app(AssignReviewersAction::class);
        $result = $action->execute($proposal, $this->selectedReviewer);

        if ($result['success']) {
            $this->dispatch('success', message: $result['message']);
            $this->dispatch('reviewers-assigned', proposalId: $proposal->id);
            $this->selectedReviewer = '';
        } else {
            $this->dispatch('error', message: $result['message']);
        }
    }

    public function removeReviewer(string $reviewerId): void
    {
        $reviewer = $this->proposal->reviewers()
            ->where('user_id', $reviewerId)
            ->first();

        if ($reviewer) {
            $reviewer->delete();
            $this->dispatch('success', message: 'Reviewer berhasil dihapus');
        }
    }

    public function confirmRemoveReviewer(string $reviewerId): void
    {
        $this->confirmingRemoveReviewerId = $reviewerId;
    }

    public function cancelRemoveReviewer(): void
    {
        $this->confirmingRemoveReviewerId = '';
    }

    public function resetReviewerForm(): void
    {
        $this->selectedReviewer = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.community-service.proposal.reviewer-assignment');
    }
}
