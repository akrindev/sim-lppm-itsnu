<?php

namespace App\Livewire\Actions;

use App\Enums\ProposalStatus;
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

        // Submit proposal (status changes to SUBMITTED)
        $proposal->update(['status' => ProposalStatus::SUBMITTED]);

        return [
            'success' => true,
            'message' => 'Proposal berhasil diajukan untuk review.',
        ];
    }
}
