<?php

namespace App\Livewire\Actions;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;

class AssignReviewersAction
{
    /**
     * Assign a reviewer to a proposal.
     */
    public function execute(Proposal $proposal, int|string $reviewerId): array
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

        // Update proposal status to reviewed if first reviewer
        if ($proposal->reviewers()->count() === 1) {
            $proposal->update(['status' => ProposalStatus::REVIEWED]);
        }

        return [
            'success' => true,
            'message' => 'Berhasil menugaskan reviewer untuk proposal ini.',
        ];
    }
}
