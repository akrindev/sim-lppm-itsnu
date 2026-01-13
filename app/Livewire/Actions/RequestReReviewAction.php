<?php

namespace App\Livewire\Actions;

use App\Enums\ReviewStatus;
use App\Models\Proposal;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class RequestReReviewAction
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Request re-review from existing reviewers after proposal revision.
     *
     * This action is called when a proposal is resubmitted after REVISION_NEEDED status.
     * It resets all reviewer statuses and notifies them of the revision.
     */
    public function execute(Proposal $proposal, int $daysToReview = 14): array
    {
        // Validate proposal has reviewers
        if ($proposal->reviewers()->count() === 0) {
            return [
                'success' => false,
                'message' => 'Tidak ada reviewer yang ditugaskan untuk proposal ini.',
            ];
        }

        try {
            DB::transaction(function () use ($proposal, $daysToReview): void {
                // Get current max round
                $currentRound = $proposal->reviewers()->max('round') ?? 1;
                $newRound = $currentRound + 1;

                // Reset all reviewers for new round
                $proposal->reviewers()->update([
                    'status' => ReviewStatus::RE_REVIEW_REQUESTED,
                    'round' => $newRound,
                    'review_notes' => null,
                    'recommendation' => null,
                    'started_at' => null,
                    'completed_at' => null,
                    'assigned_at' => now(),
                    'deadline_at' => now()->addDays($daysToReview),
                ]);

                // Send notifications to all reviewers
                $this->sendNotifications($proposal, $newRound);
            });

            return [
                'success' => true,
                'message' => 'Permintaan review ulang berhasil dikirim ke semua reviewer.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal meminta review ulang: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Send notifications to reviewers about proposal revision.
     */
    protected function sendNotifications(Proposal $proposal, int $round): void
    {
        // Get all reviewers for this proposal
        $reviewers = $proposal->reviewers()->with('user')->get();

        foreach ($reviewers as $reviewerRecord) {
            if ($reviewerRecord->user) {
                $this->notificationService->notifyProposalRevised(
                    $proposal,
                    $reviewerRecord->user,
                    $round
                );
            }
        }

        // Also notify Admin LPPM and Kepala LPPM
        $admins = collect()
            ->merge(User::role('admin lppm')->get())
            ->merge(User::role('kepala lppm')->get())
            ->unique('id')
            ->values();

        foreach ($admins as $admin) {
            $this->notificationService->notifyProposalRevised(
                $proposal,
                $admin,
                $round,
                true // isAdmin flag
            );
        }
    }
}
