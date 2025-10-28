<?php

namespace App\Livewire\Actions;

use App\Models\Proposal;

class SubmitProposalAction
{
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

        // Check if already submitted
        if (in_array($proposal->status, ['submitted', 'under_review', 'reviewed', 'approved', 'rejected', 'completed'])) {
            return [
                'success' => false,
                'message' => 'Proposal sudah diajukan atau dalam tahap review.',
            ];
        }

        // Submit proposal
        $proposal->update(['status' => 'submitted']);

        return [
            'success' => true,
            'message' => 'Proposal berhasil diajukan untuk review.',
        ];
    }
}
