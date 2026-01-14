<?php

namespace App\Livewire\Actions;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Models\User;
use App\Services\NotificationService;

class SubmitProposalAction
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Submit a proposal for review.
     * Only possible if all team members have accepted.
     */
    public function execute(Proposal $proposal): array
    {
        // Check if all team members accepted
        if (! $proposal->allTeamMembersAccepted()) {
            $pendingMembers = $proposal->getPendingTeamMembers();

            return [
                'success' => false,
                'message' => sprintf(
                    'Tidak dapat mengirim proposal. %d anggota masih belum menerima undangan.',
                    $pendingMembers->count()
                ),
            ];
        }

        // Check if proposal can be submitted
        $allowedStatuses = [
            ProposalStatus::DRAFT,
            ProposalStatus::NEED_ASSIGNMENT,
            ProposalStatus::REVISION_NEEDED,
        ];

        if (! in_array($proposal->status, $allowedStatuses)) {
            return [
                'success' => false,
                'message' => 'Proposal tidak dapat diajukan dari status saat ini.',
            ];
        }

        // Track if this is a resubmission after revision
        $isResubmissionAfterRevision = $proposal->status === ProposalStatus::REVISION_NEEDED;

        // Submit proposal (status changes to SUBMITTED)
        $proposal->update(['status' => ProposalStatus::SUBMITTED]);

        // Send notifications
        $this->sendNotifications($proposal);

        // If this is a resubmission after revision and has existing reviewers,
        // request re-review from them
        if ($isResubmissionAfterRevision && $proposal->reviewers()->exists()) {
            $this->triggerReReview($proposal);
        }

        return [
            'success' => true,
            'message' => 'Proposal berhasil diajukan untuk review.',
        ];
    }

    /**
     * Trigger re-review process for existing reviewers.
     */
    protected function triggerReReview(Proposal $proposal): void
    {
        // This will be called after proposal goes through approval flow again
        // For now, we just prepare the reviewers for re-review
        // The actual status change will happen via RequestReReviewAction
        // when proposal reaches UNDER_REVIEW status again

        // Note: The re-review workflow will be triggered when:
        // SUBMITTED -> APPROVED (Dekan) -> WAITING_REVIEWER -> UNDER_REVIEW
        // At UNDER_REVIEW stage, if reviewers exist, their status should be reset
    }

    /**
     * Send notifications to relevant stakeholders
     */
    protected function sendNotifications(Proposal $proposal): void
    {
        // Get recipients: Dekan, Team Members, Admin LPPM
        $dekan = User::role('dekan')->first();
        $teamMembers = $proposal->teamMembers()->where('user_id', '!=', $proposal->submitter_id)->get();
        $adminLppm = User::role('admin lppm')->first();

        $recipients = collect()
            ->push($dekan)
            ->merge($teamMembers)
            ->push($adminLppm)
            ->filter()
            ->unique('id')
            ->values();

        $this->notificationService->notifyProposalSubmitted(
            $proposal,
            $proposal->submitter,
            $recipients
        );
    }
}
