<?php

namespace App\Livewire\Research\Proposal;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ReviewerForm extends Component
{
    public string $proposalId = '';

    public bool $showForm = false;

    #[Validate('required|min:10')]
    public string $reviewNotes = '';

    #[Validate('required|in:approved,rejected,revision_needed')]
    public string $recommendation = '';

    public function mount(string $proposalId): void
    {
        $this->proposalId = $proposalId;

        // Load existing review data if available
        $myReview = $this->myReview;
        if ($myReview && $myReview->isCompleted()) {
            $this->reviewNotes = $myReview->review_notes ?? '';
            $this->recommendation = $myReview->recommendation ?? '';
        }

        // Mark as started when form is mounted (if reviewer is viewing)
        $this->markReviewAsStarted();

        // If review is in progress, show the form by default
        if ($this->myReview && $this->myReview->isInProgress()) {
            $this->showForm = true;
        }
    }

    /**
     * Mark the review as started when reviewer first opens the form
     */
    protected function markReviewAsStarted(): void
    {
        $review = $this->myReview;
        if ($review && $review->isPending()) {
            $review->markAsStarted();
        }
    }

    #[Computed]
    public function proposal()
    {
        return Proposal::with([
            'reviewers.user.identity',
        ])->find($this->proposalId);
    }

    #[Computed]
    public function myReview()
    {
        return $this->proposal->reviewers
            ->where('user_id', Auth::id())
            ->first();
    }

    #[Computed]
    public function allReviews()
    {
        return $this->proposal->reviewers;
    }

    #[Computed]
    public function canReview(): bool
    {
        return $this->myReview !== null;
    }

    #[Computed]
    public function needsAction(): bool
    {
        return $this->myReview !== null && (
            $this->myReview->requiresAction() || $this->myReview->isInProgress()
        );
    }

    #[Computed]
    public function hasReviewed(): bool
    {
        $review = $this->myReview;

        return $review && $review->isCompleted();
    }

    #[Computed]
    public function needsReReview(): bool
    {
        $review = $this->myReview;

        return $review && $review->isReReviewRequested();
    }

    #[Computed]
    public function canEditReview(): bool
    {
        $review = $this->myReview;
        if (! $review) {
            return false;
        }

        // Jika rekomendasi sudah approved, tidak bisa edit
        if ($review->recommendation === 'approved') {
            return false;
        }

        // Jika proposal sudah final, tidak bisa edit
        if ($this->proposal->status->isFinal()) {
            return false;
        }

        return $review->isCompleted();
    }

    #[Computed]
    public function reviewRound(): int
    {
        return $this->myReview?->round ?? 1;
    }

    #[Computed]
    public function deadline()
    {
        return $this->myReview?->deadline_at;
    }

    #[Computed]
    public function isOverdue(): bool
    {
        return $this->myReview?->isOverdue() ?? false;
    }

    #[Computed]
    public function daysRemaining(): ?int
    {
        return $this->myReview?->days_remaining;
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;

        // Mark as started when form is opened
        if ($this->showForm) {
            $this->markReviewAsStarted();
        }
    }

    public function submitReview(): void
    {
        $this->validate();

        $review = $this->myReview;

        if (! $review) {
            $this->dispatch('error', message: 'Anda bukan reviewer untuk proposal ini');

            return;
        }

        try {
            DB::transaction(function (): void {
                $review = $this->myReview;

                // Complete the review with new method
                $review->complete($this->reviewNotes, $this->recommendation);

                // Refresh the proposal and review from DB to get updated data
                $proposal = $this->proposal->fresh(['reviewers']);

                // Check if all reviews are completed using fresh data
                $allCompleted = $proposal->allReviewsCompleted();

                if ($allCompleted) {
                    // Update proposal status to reviewed
                    $proposal->update(['status' => ProposalStatus::REVIEWED]);
                }
            });

            $message = $this->needsReReview ? 'Review ulang berhasil disubmit' : 'Review berhasil disubmit';
            $this->dispatch('success', message: $message);

            session()->flash('success', $message);
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Gagal menyimpan review: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.reviewer-form');
    }
}
