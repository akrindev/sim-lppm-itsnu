<?php

namespace App\Livewire\Research\Proposal;

use App\Enums\ProposalStatus;
use App\Livewire\Actions\ApproveProposalAction;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ApprovalButton extends Component
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
    public function canApprove(): bool
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);
        $proposal = $this->proposal;

        return $isAdmin && $proposal->status === ProposalStatus::REVIEWED && $proposal->allReviewsCompleted();
    }

    #[Computed]
    public function pendingReviewers()
    {
        return $this->proposal->pendingReviewers()->get();
    }

    #[Computed]
    public function reviewSummary()
    {
        return $this->proposal->reviewers()
            ->select('recommendation')
            ->get()
            ->groupBy('recommendation')
            ->map->count();
    }

    public function approve(): void
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);

        if (! $isAdmin) {
            $this->dispatch('error', message: 'Anda tidak memiliki akses untuk approve proposal');

            return;
        }

        $proposal = $this->proposal;
        $action = new ApproveProposalAction;
        $result = $action->execute($proposal, 'approved');

        if ($result['success']) {
            $this->dispatch('success', message: $result['message']);
            $this->dispatch('proposal-approved', proposalId: $proposal->id);
        } else {
            $this->dispatch('error', message: $result['message']);
        }
    }

    public function reject(): void
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);

        if (! $isAdmin) {
            $this->dispatch('error', message: 'Anda tidak memiliki akses untuk reject proposal');

            return;
        }

        $proposal = $this->proposal;
        $action = new ApproveProposalAction;
        $result = $action->execute($proposal, 'rejected');

        if ($result['success']) {
            $this->dispatch('warning', message: $result['message']);
            $this->dispatch('proposal-rejected', proposalId: $proposal->id);
        } else {
            $this->dispatch('error', message: $result['message']);
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.approval-button');
    }
}
