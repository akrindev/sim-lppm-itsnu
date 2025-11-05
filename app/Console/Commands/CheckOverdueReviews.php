<?php

namespace App\Console\Commands;

use App\Models\ProposalReviewer;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueReviews extends Command
{
    protected $signature = 'reviews:check-overdue';

    protected $description = 'Send notifications for overdue reviews';

    public function handle(NotificationService $notificationService): int
    {
        // Find all reviews that are overdue
        $now = Carbon::now()->startOfDay();

        $reviewers = ProposalReviewer::query()
            ->where('status', 'pending')
            ->where('deadline', '<', $now)
            ->with(['proposal', 'reviewer'])
            ->get();

        foreach ($reviewers as $review) {
            $daysOverdue = $review->deadline->diffInDays(now());
            $notificationService->notifyReviewOverdue(
                $review->proposal,
                $review->reviewer,
                $daysOverdue
            );

            $this->info("Overdue notification sent to {$review->reviewer->name} for proposal: {$review->proposal->title}");
        }

        $this->info("Sent {$reviewers->count()} overdue review notifications");

        return 0;
    }
}
