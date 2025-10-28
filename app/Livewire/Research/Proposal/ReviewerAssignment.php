<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ReviewerAssignment extends Component
{
    public string $proposalId = '';

    #[Validate('required|array|min:1')]
    public array $selectedReviewers = [];

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
            $query->whereIn('name', ['reviewer', 'admin lppm', 'kepala lppm']);
        })
            ->whereNotIn('id', $this->currentReviewers->pluck('user_id'))
            ->get(['id', 'name', 'email']);
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

        try {
            foreach ($this->selectedReviewers as $userId) {
                // Check if reviewer already assigned
                if ($proposal->reviewers()->where('user_id', $userId)->exists()) {
                    continue;
                }

                ProposalReviewer::create([
                    'proposal_id' => $proposal->id,
                    'user_id' => $userId,
                    'status' => 'pending',
                ]);
            }

            // Update proposal status to under_review if not already
            if ($proposal->status === 'submitted') {
                $proposal->update(['status' => 'under_review']);
            }

            $this->dispatch('success', message: 'Reviewer berhasil ditugaskan');
            $this->dispatch('reviewers-assigned', proposalId: $proposal->id);
            $this->selectedReviewers = [];
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Gagal menugaskan reviewer: ' . $e->getMessage());
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

    public function render(): View
    {
        return view('livewire.research.proposal.reviewer-assignment');
    }
}
