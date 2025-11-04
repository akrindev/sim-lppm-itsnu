<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DekanApprovalDecision extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public string $decision, // 'approved' or 'need_assignment'
        public User $dekan
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isApproved = $this->decision === 'approved';

        if ($isApproved) {
            $title = 'Proposal Disetujui Dekan';
            $message = "Proposal '{$this->proposal->title}' telah disetujui Dekan dan diteruskan ke Kepala LPPM";
        } else {
            $title = 'Proposal Memerlukan Persetujuan Anggota Tim';
            $message = "Proposal '{$this->proposal->title}' memerlukan persetujuan dari anggota tim yang tersisa";
        }

        return [
            'type' => 'dekan_approval_decision',
            'decision' => $this->decision,
            'title' => $title,
            'message' => $message,
            'proposal_id' => $this->proposal->id,
            'dekan_id' => $this->dekan->id,
            'dekan_name' => $this->dekan->name,
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route('research.proposal.show', $this->proposal),
            'icon' => $isApproved ? 'check-circle' : 'alert-circle',
            'created_at' => now()->toISOString(),
        ];
    }
}
