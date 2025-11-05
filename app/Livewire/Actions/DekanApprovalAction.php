<?php

namespace App\Livewire\Actions;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class DekanApprovalAction
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Execute the Dekan approval action
     *
     * @param  string  $decision  'approved' or 'need_assignment'
     * @return array{success: bool, message: string}
     */
    public function execute(Proposal $proposal, string $decision, ?string $notes = null, ?User $dekan = null): array
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

            // Send notifications based on decision
            $this->sendNotifications($proposal, $decision, $dekan ?? auth()->user());

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
                'message' => 'Terjadi kesalahan saat memproses persetujuan: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Send notifications based on decision
     */
    protected function sendNotifications(Proposal $proposal, string $decision, User $dekan): void
    {
        $recipients = collect();

        if ($decision === 'approved') {
            // Notify: Submitter, Kepala LPPM, Team Members
            $recipients->push($proposal->submitter);
            $recipients->push(User::role('kepala lppm')->first());
            $recipients = $recipients->merge($proposal->teamMembers);
        } else {
            // Notify: Submitter, Team Members (for approval)
            $recipients->push($proposal->submitter);
            $recipients = $recipients->merge($proposal->teamMembers()->where('user_id', '!=', $proposal->submitter_id)->get());
        }

        $this->notificationService->notifyDekanApprovalDecision(
            $proposal,
            $decision,
            $dekan,
            $recipients->filter()->unique('id')->values()->toArray()
        );
    }
}
