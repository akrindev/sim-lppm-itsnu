<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use App\Models\ProposalReviewer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ReviewerForm extends Component
{
    public string $proposalId = '';

    #[Validate('required|min:10')]
    public string $reviewNotes = '';

    #[Validate('required|in:approved,rejected,revision_needed')]
    public string $recommendation = '';

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
    public function myReview()
    {
        return $this->proposal->reviewers()
            ->where('user_id', Auth::id())
            ->first();
    }

    #[Computed]
    public function canReview(): bool
    {
        $review = $this->myReview;

        if (! $review) {
            return false;
        }

        return $review->status === 'pending';
    }

    public function submitReview(): void
    {
        $this->validate();

        $review = $this->myReview;

        if (! $review) {
            $this->dispatch('error', message: 'Anda bukan reviewer untuk proposal ini');
            return;
        }

        if ($review->status !== 'pending') {
            $this->dispatch('error', message: 'Anda sudah mensubmit review');
            return;
        }

        try {
            $review->update([
                'status' => 'completed',
                'review_notes' => $this->reviewNotes,
                'recommendation' => $this->recommendation,
            ]);

            // Check if all reviews are completed
            $allCompleted = $this->proposal->allReviewsCompleted();

            if ($allCompleted) {
                // Update proposal status to reviewed
                $this->proposal->update(['status' => 'reviewed']);
            }

            $this->dispatch('success', message: 'Review berhasil disubmit');
            $this->dispatch('review-submitted', proposalId: $this->proposalId);

            $this->reviewNotes = '';
            $this->recommendation = '';
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Gagal submit review: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.reviewer-form');
    }
}
