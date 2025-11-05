<?php

namespace App\Livewire\Actions;

use App\Enums\ProposalStatus;
use App\Models\ProposalReviewer;
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
        if (! in_array($recommendation, [ProposalStatus::APPROVED, ProposalStatus::REJECTED, ProposalStatus::REVISION_NEEDED])) {
            return [
                'success' => false,
                'message' => 'Rekomendasi harus "approved", "rejected", atau "revision".',
            ];
        }

        if ($review->isCompleted()) {
            return [
                'success' => false,
                'message' => 'Review sudah selesai dan tidak dapat diubah.',
            ];
        }

        $review->complete($comments, $recommendation);

        // Check if proposal can now be approved
        $proposal = $review->proposal;

        // Send notifications
        $this->sendNotifications($proposal, $review->user, $review);

        if ($proposal->allReviewersCompleted()) {
            $proposal->update(['status' => ProposalStatus::COMPLETED]);

            // Send special notification for all reviews completed
            $this->sendAllReviewsCompletedNotification($proposal);
        }

        return [
            'success' => true,
            'message' => 'Review berhasil diserahkan.',
        ];
    }

    /**
     * Send notifications when a review is completed
     */
    protected function sendNotifications($proposal, User $reviewer, ProposalReviewer $review): void
    {
        $recipients = collect()
            ->push($proposal->submitter) // Submitter
            ->push(User::role('kepala lppm')->first()) // Kepala LPPM
            ->push(User::role('admin lppm')->first()) // Admin LPPM
            ->merge($proposal->teamMembers) // Team Members
            ->filter(fn ($user) => $user && $user->id !== $reviewer->id) // Exclude reviewer
            ->unique('id')
            ->values()
            ->toArray();

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
            ->values()
            ->toArray();

        $this->notificationService->notifyReviewCompleted(
            $proposal,
            Auth::user(),
            true, // All reviews complete
            $recipients
        );
    }
}
