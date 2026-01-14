<?php

namespace App\Livewire\Actions;

use App\Enums\ProposalStatus;
use App\Models\Proposal;

class ApproveProposalAction
{
    /**
     * Approve or reject a proposal.
     * Only possible if all reviewers have completed their reviews.
     */
    public function execute(Proposal $proposal, string $decision): array
    {
        if (! in_array($decision, [ProposalStatus::COMPLETED->value, ProposalStatus::REJECTED->value])) {
            return [
                'success' => false,
                'message' => 'Keputusan harus "completed" atau "rejected".',
            ];
        }

        // Check if all reviewers completed
        if (! $proposal->allReviewersCompleted()) {
            $pendingReviewers = $proposal->getPendingReviewers();

            return [
                'success' => false,
                'message' => sprintf(
                    'Tidak dapat memutuskan proposal. %d reviewer masih belum menyelesaikan review.',
                    $pendingReviewers->count()
                ),
            ];
        }

        // Update proposal status
        $proposal->update(['status' => $decision]);

        $message = $decision === 'completed'
            ? 'Proposal berhasil disetujui dan selesai.'
            : 'Proposal ditolak.';

        return [
            'success' => true,
            'message' => $message,
        ];
    }
}
