<?php

namespace App\Livewire\Actions;

use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;

class AssignReviewersAction
{
    /**
     * Assign reviewers to a proposal.
     */
    public function execute(Proposal $proposal, array $reviewerIds): array
    {
        if ($proposal->status !== 'submitted') {
            return [
                'success' => false,
                'message' => 'Proposal harus dalam status submitted untuk menugaskan reviewer.',
            ];
        }

        if (empty($reviewerIds)) {
            return [
                'success' => false,
                'message' => 'Minimal 1 reviewer harus ditugaskan.',
            ];
        }

        // Validate reviewers exist
        $reviewers = User::whereIn('id', $reviewerIds)->get();
        if ($reviewers->count() !== count($reviewerIds)) {
            return [
                'success' => false,
                'message' => 'Beberapa reviewer tidak ditemukan.',
            ];
        }

        // Clear existing reviewers if any
        $proposal->reviewers()->delete();

        // Assign new reviewers
        foreach ($reviewerIds as $reviewerId) {
            ProposalReviewer::create([
                'proposal_id' => $proposal->id,
                'user_id' => $reviewerId,
                'status' => 'pending',
            ]);
        }

        // Update proposal status to under_review
        $proposal->update(['status' => 'under_review']);

        return [
            'success' => true,
            'message' => sprintf('Berhasil menugaskan %d reviewer untuk proposal ini.', count($reviewerIds)),
        ];
    }
}
