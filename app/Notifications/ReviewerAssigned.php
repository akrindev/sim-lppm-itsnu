<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReviewerAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $reviewer,
        public string $reviewDeadline
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'reviewer_assigned',
            'title' => 'Anda Ditugaskan Sebagai Reviewer',
            'message' => "Anda ditugaskan untuk mereview proposal '{$this->proposal->title}'",
            'proposal_id' => $this->proposal->id,
            'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => $this->reviewer->name,
            'review_deadline' => $this->reviewDeadline,
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route('research.proposal.show', $this->proposal),
            'icon' => 'user-check',
            'created_at' => now()->toISOString(),
        ];
    }
}
