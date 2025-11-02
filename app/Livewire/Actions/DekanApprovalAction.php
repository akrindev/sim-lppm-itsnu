<?php

namespace App\Livewire\Actions;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Support\Facades\Log;

class DekanApprovalAction
{
    /**
     * Execute the Dekan approval action
     *
     * @param  string  $decision  'approved' or 'need_assignment'
     * @return array{success: bool, message: string}
     */
    public function execute(Proposal $proposal, string $decision, ?string $notes = null): array
    {
        // Validate proposal status
        if ($proposal->status !== ProposalStatus::SUBMITTED) {
            return [
                'success' => false,
                'message' => 'Proposal tidak dalam status yang dapat diproses oleh Dekan.',
            ];
        }

        // Validate decision
        if (! in_array($decision, ['approved', 'need_assignment'])) {
            return [
                'success' => false,
                'message' => 'Keputusan tidak valid.',
            ];
        }

        try {
            $newStatus = $decision === 'approved'
                ? ProposalStatus::APPROVED
                : ProposalStatus::NEED_ASSIGNMENT;

            // Validate transition
            if (! $proposal->status->canTransitionTo($newStatus)) {
                return [
                    'success' => false,
                    'message' => 'Transisi status tidak diizinkan.',
                ];
            }

            // Update proposal status
            $proposal->update([
                'status' => $newStatus,
            ]);

            // Log the activity
            Log::info('Dekan approval action', [
                'proposal_id' => $proposal->id,
                'decision' => $decision,
                'new_status' => $newStatus->value,
                'notes' => $notes,
            ]);

            // TODO: Send notification based on decision
            // If approved: notify Kepala LPPM
            // If need_assignment: notify submitter (dosen)

            $message = $decision === 'approved'
                ? 'Proposal berhasil disetujui dan diteruskan ke Kepala LPPM.'
                : 'Proposal dikembalikan ke pengusul untuk memperbaiki persetujuan anggota.';

            return [
                'success' => true,
                'message' => $message,
            ];
        } catch (\Exception $e) {
            Log::error('Dekan approval action failed', [
                'proposal_id' => $proposal->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses persetujuan: ' . $e->getMessage(),
            ];
        }
    }
}
