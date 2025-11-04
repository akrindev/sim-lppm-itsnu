<?php

namespace App\Livewire\Actions;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;

class AssignReviewersAction
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Assign a reviewer to a proposal.
     */
    public function execute(Proposal $proposal, int|string $reviewerId, int $daysToReview = 14): array
    {
        if ($proposal->status !== ProposalStatus::UNDER_REVIEW) {
            return [
                'success' => false,
                'message' => 'Proposal harus dalam status under review untuk menugaskan reviewer.',
            ];
        }

        // Validate already exists
        $existingReviewer = $proposal->reviewers()->where('user_id', $reviewerId)->first();
        if ($existingReviewer) {
            return [
                'success' => false,
                'message' => 'Reviewer sudah ditugaskan untuk proposal ini.',
            ];
        }

        // Validate reviewer exists
        $reviewer = User::find($reviewerId);
        if (! $reviewer) {
            return [
                'success' => false,
                'message' => 'Reviewer tidak ditemukan.',
            ];
        }

        // Assign reviewer
        ProposalReviewer::create([
            'proposal_id' => $proposal->id,
            'user_id' => $reviewerId,
            'status' => 'pending',
        ]);

        // Send notifications
        $this->sendNotifications($proposal, $reviewer, $daysToReview);

        // Update proposal status to reviewed if first reviewer
        if ($proposal->reviewers()->count() === 1) {
            $proposal->update(['status' => ProposalStatus::REVIEWED]);
        }

        return [
            'success' => true,
            'message' => 'Berhasil menugaskan reviewer untuk proposal ini.',
        ];
    }

    /**
     * Send notifications to reviewer and stakeholders
     */
    protected function sendNotifications(Proposal $proposal, User $reviewer, int $daysToReview): void
    {
        $deadline = Carbon::now()->addDays($daysToReview)->format('Y-m-d');

        // Get recipients
        $recipients = collect()
            ->push($reviewer) // The reviewer
            ->push($proposal->user) // Submitter
            ->push(User::role('kepala lppm')->first()) // Kepala LPPM
            ->filter()
            ->unique('id')
            ->values();

        $this->notificationService->notifyReviewerAssigned(
            $proposal,
            $reviewer,
            $deadline,
            $recipients
        );
    }
}
