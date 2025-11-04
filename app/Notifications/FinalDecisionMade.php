<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FinalDecisionMade extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public string $decision, // 'approved', 'rejected', 'revision_needed'
        public User $kepalaLppm
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        match ($this->decision) {
            'approved' => [
                'title' => 'Proposal Disetujui',
                'message' => "Proposal '{$this->proposal->title}' telah disetujui dan selesai",
                'icon' => 'check-circle-2',
            ],
            'rejected' => [
                'title' => 'Proposal Ditolak',
                'message' => "Proposal '{$this->proposal->title}' telah ditolak",
                'icon' => 'x-circle',
            ],
            'revision_needed' => [
                'title' => 'Proposal Memerlukan Revisi',
                'message' => "Proposal '{$this->proposal->title}' memerlukan perbaikan dan revisi",
                'icon' => 'edit',
            ],
            default => [
                'title' => 'Keputusan Final',
                'message' => "Keputusan final telah dibuat untuk proposal '{$this->proposal->title}'",
                'icon' => 'alert-circle',
            ],
        };

        $data = match ($this->decision) {
            'approved' => [
                'title' => 'Proposal Disetujui',
                'message' => "Proposal '{$this->proposal->title}' telah disetujui dan selesai",
                'icon' => 'check-circle-2',
            ],
            'rejected' => [
                'title' => 'Proposal Ditolak',
                'message' => "Proposal '{$this->proposal->title}' telah ditolak",
                'icon' => 'x-circle',
            ],
            'revision_needed' => [
                'title' => 'Proposal Memerlukan Revisi',
                'message' => "Proposal '{$this->proposal->title}' memerlukan perbaikan dan revisi",
                'icon' => 'edit',
            ],
            default => [
                'title' => 'Keputusan Final',
                'message' => "Keputusan final telah dibuat untuk proposal '{$this->proposal->title}'",
                'icon' => 'alert-circle',
            ],
        };

        return [
            'type' => 'final_decision_made',
            'decision' => $this->decision,
            'title' => $data['title'],
            'message' => $data['message'],
            'proposal_id' => $this->proposal->id,
            'kepala_lppm_id' => $this->kepalaLppm->id,
            'kepala_lppm_name' => $this->kepalaLppm->name,
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route('research.proposal.show', $this->proposal),
            'icon' => $data['icon'],
            'created_at' => now()->toISOString(),
        ];
    }
}
