<?php

namespace App\Livewire\Actions;

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;

class ApproveProposalAction
{
    /**
     * Approve or reject a proposal.
     * Only possible if all reviewers have completed their reviews.
     */
    public function execute(Proposal $proposal, string $decision): array
    {
        if (! in_array($decision, ['approved', 'rejected'])) {
            return [
                'success' => false,
                'message' => 'Keputusan harus "approved" atau "rejected".',
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

        $message = $decision === 'approved'
            ? 'Proposal berhasil disetujui.'
            : 'Proposal ditolak.';

        return [
            'success' => true,
            'message' => $message,
        ];
    }
}
