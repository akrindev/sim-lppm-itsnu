<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Actions\CompleteReviewAction;
use App\Models\ProposalReviewer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ReviewForm extends Component
{
    public ProposalReviewer $review;

    #[Validate('required|string|min:10')]
    public string $comments = '';

    #[Validate('required|in:approved,rejected,revision')]
    public string $recommendation = '';

    #[Computed]
    public function proposal()
    {
        return $this->review->proposal;
    }

    public function submitReview(): void
    {
        $this->validate();

        // Verify current user is the reviewer
        if (Auth::id() !== $this->review->user_id) {
            session()->flash('error', 'Anda tidak memiliki akses untuk melakukan review ini.');
            return;
        }

        $action = new CompleteReviewAction();
        $result = $action->execute($this->review, $this->comments, $this->recommendation);

        if ($result['success']) {
            $this->dispatch('review-completed', reviewId: $this->review->id);
            session()->flash('success', $result['message']);
            $this->reset();
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.review-form');
    }
}
