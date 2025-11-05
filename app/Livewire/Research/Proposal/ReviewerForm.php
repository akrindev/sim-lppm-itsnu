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
        if ($myReview && $myReview->status === 'completed') {
            $this->reviewNotes = $myReview->review_notes ?? '';
            $this->recommendation = $myReview->recommendation ?? '';
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
    public function hasReviewed(): bool
    {
        $review = $this->myReview;

        return $review && $review->status === 'completed';
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

        return $review->status === 'completed';
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
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
            $isNew = ! $review->exists;

            DB::transaction(function (): void {
                $review = $this->myReview;

                // Allow updating review even after submission
                $review->update([
                    'status' => 'completed',
                    'review_notes' => $this->reviewNotes,
                    'recommendation' => $this->recommendation,
                ]);

                // Refresh the proposal and review from DB to get updated data
                $proposal = $this->proposal->fresh(['reviewers']);
                $review = $proposal->reviewers->where('user_id', Auth::id())->first();

                // Check if all reviews are completed using fresh data
                $allCompleted = $proposal->allReviewsCompleted();

                if ($allCompleted) {
                    // Update proposal status to reviewed
                    $proposal->update(['status' => ProposalStatus::REVIEWED]);
                }
            });

            $review = $this->myReview;
            $message = $isNew ? 'Review berhasil disubmit' : 'Review berhasil diupdate';
            $this->dispatch('success', message: $message);
            $this->dispatch('review-submitted', proposalId: $this->proposalId);
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Gagal menyimpan review: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.reviewer-form');
    }
}
