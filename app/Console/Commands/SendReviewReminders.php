<?php

namespace App\Console\Commands;

use App\Models\ProposalReviewer;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReviewReminders extends Command
{
    protected $signature = 'reviews:send-reminders';

    protected $description = 'Send review reminders to reviewers 3 days before deadline';

    public function handle(NotificationService $notificationService): int
    {
        // Find all reviews that are due in 3 days
        $threeDay = Carbon::now()->addDays(3)->startOfDay();
        $threeDayEnd = $threeDay->copy()->endOfDay();

        $reviewers = ProposalReviewer::query()
            ->where('status', 'pending')
            ->whereBetween('deadline', [$threeDay, $threeDayEnd])
            ->with(['proposal', 'reviewer'])
            ->get();

        foreach ($reviewers as $review) {
            $daysRemaining = $review->deadline->diffInDays(now());
            $notificationService->notifyReviewReminder(
                $review->proposal,
                $review->reviewer,
                $daysRemaining
            );

            $this->info("Reminder sent to {$review->reviewer->name} for proposal: {$review->proposal->title}");
        }

        $this->info("Sent {$reviewers->count()} review reminders");

        return 0;
    }
}
