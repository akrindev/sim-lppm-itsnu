<?php

namespace App\Livewire\Actions;

use App\Models\ProposalReviewer;

class CompleteReviewAction
{
    /**
     * Complete a review submission.
     */
    public function execute(ProposalReviewer $review, string $comments, string $recommendation): array
    {
        if (! in_array($recommendation, ['approved', 'rejected', 'revision'])) {
            return [
                'success' => false,
                'message' => 'Rekomendasi harus "approved", "rejected", atau "revision".',
            ];
        }

        if ($review->isCompleted()) {
            return [
                'success' => false,
                'message' => 'Review sudah selesai dan tidak dapat diubah.',
            ];
        }

        $review->complete($comments, $recommendation);

        // Check if proposal can now be approved
        $proposal = $review->proposal;
        if ($proposal->allReviewersCompleted()) {
            $proposal->update(['status' => 'reviewed']);
        }

        return [
            'success' => true,
            'message' => 'Review berhasil diserahkan.',
        ];
    }
}
