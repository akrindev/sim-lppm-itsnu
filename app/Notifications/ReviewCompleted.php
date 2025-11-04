<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReviewCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $reviewer,
        public bool $allReviewsComplete = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        if ($this->allReviewsComplete) {
            return [
                'type' => 'all_reviews_completed',
                'title' => 'Semua Review Selesai',
                'message' => "Semua reviewer telah menyelesaikan review untuk proposal '{$this->proposal->title}'",
                'proposal_id' => $this->proposal->id,
                'proposal_type' => $this->proposal->getMorphClass(),
                'link' => route('research.proposal.show', $this->proposal),
                'icon' => 'check-square',
                'created_at' => now()->toISOString(),
            ];
        }

        return [
            'type' => 'review_completed',
            'title' => 'Review Selesai',
            'message' => "Reviewer {$this->reviewer->name} telah menyelesaikan review untuk proposal '{$this->proposal->title}'",
            'proposal_id' => $this->proposal->id,
            'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => $this->reviewer->name,
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route('research.proposal.show', $this->proposal),
            'icon' => 'check-circle',
            'created_at' => now()->toISOString(),
        ];
    }
}
