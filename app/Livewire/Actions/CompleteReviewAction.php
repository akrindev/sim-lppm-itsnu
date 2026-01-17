<?php

namespace App\Livewire\Actions;

use App\Enums\ProposalStatus;
use App\Models\ProposalReviewer;
use App\Models\ReviewLog;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class CompleteReviewAction
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Complete a review submission.
     */
    public function execute(ProposalReviewer $review, string $comments, string $recommendation): array
    {
        $validRecommendations = ['approved', 'rejected', 'revision_needed'];
        if (! in_array($recommendation, $validRecommendations)) {
            return [
                'success' => false,
                'message' => 'Rekomendasi harus "approved", "rejected", atau "revision_needed".',
            ];
        }

        if ($review->isCompleted()) {
            return [
                'success' => false,
                'message' => 'Review sudah selesai dan tidak dapat diubah.',
            ];
        }

        // Complete the review
        $review->complete($comments, $recommendation);

        // Create review log for history tracking
        $this->createReviewLog($review, $comments, $recommendation);

        // Check if proposal can now be approved
        $proposal = $review->proposal;

        // Send notifications
        $this->sendNotifications($proposal, $review->user, $review);

        if ($proposal->allReviewersCompleted()) {
            // FIXED: Use REVIEWED status, not COMPLETED
            // Kepala LPPM must make the final decision
            $proposal->update(['status' => ProposalStatus::REVIEWED]);

            // Send special notification for all reviews completed
            $this->sendAllReviewsCompletedNotification($proposal);
        }

        return [
            'success' => true,
            'message' => 'Review berhasil diserahkan.',
        ];
    }

    /**
     * Create a review log entry for history tracking.
     */
    protected function createReviewLog(ProposalReviewer $review, string $comments, string $recommendation): ReviewLog
    {
        return ReviewLog::create([
            'proposal_reviewer_id' => $review->id,
            'proposal_id' => $review->proposal_id,
            'user_id' => $review->user_id,
            'round' => $review->round ?? 1,
            'review_notes' => $comments,
            'recommendation' => $recommendation,
            'started_at' => $review->started_at,
            'completed_at' => $review->completed_at ?? now(),
        ]);
    }

    /**
     * Send notifications when a review is completed
     */
    protected function sendNotifications($proposal, User $reviewer, ProposalReviewer $review): void
    {
        $recipients = collect()
            ->push($proposal->submitter) // Submitter
            ->merge($proposal->teamMembers) // Team Members
            ->filter(fn ($user) => $user && $user->id !== $reviewer->id) // Exclude reviewer
            ->unique('id')
            ->values();

        $this->notificationService->notifyReviewCompleted(
            $proposal,
            $reviewer,
            false, // Not all reviews complete yet
            $recipients
        );
    }

    /**
     * Send special notification when all reviews are completed
     */
    protected function sendAllReviewsCompletedNotification($proposal): void
    {
        $recipients = collect()
            ->push($proposal->submitter) // Submitter
            ->push(User::role('kepala lppm')->first()) // Kepala LPPM
            ->push(User::role('dekan')->first()) // Dekan
            ->push(User::role('admin lppm')->first()) // Admin LPPM
            ->merge($proposal->teamMembers) // Team Members
            ->filter()
            ->unique('id')
            ->values();

        $this->notificationService->notifyReviewCompleted(
            $proposal,
            Auth::user(),
            true, // All reviews complete
            $recipients
        );
    }
}
