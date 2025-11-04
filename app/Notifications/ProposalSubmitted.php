<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProposalSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $submitter
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'proposal_submitted',
            'title' => 'Proposal Baru Disubmit',
            'message' => "Proposal '{$this->proposal->title}' telah disubmit oleh {$this->submitter->name}",
            'proposal_id' => $this->proposal->id,
            'submitter_id' => $this->submitter->id,
            'submitter_name' => $this->submitter->name,
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route('research.proposal.show', $this->proposal),
            'icon' => 'file-text',
            'created_at' => now()->toISOString(),
        ];
    }
}
