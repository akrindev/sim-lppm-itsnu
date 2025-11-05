<?php

namespace App\Notifications;

use App\Mail\Proposals\ProposalSubmittedMail;
use App\Models\Proposal;
use App\Models\Research;
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
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isResearch = $this->proposal->detailable instanceof Research;
        $proposalType = $isResearch ? 'Penelitian' : 'Pengabdian Masyarakat';
        $routeName = $isResearch
            ? 'research.proposal.show'
            : 'community-service.proposal.show';

        return [
            'type' => 'proposal_submitted',
            'title' => 'Proposal Baru Disubmit',
            'message' => "Proposal '{$this->proposal->title}' telah disubmit oleh {$this->submitter->name}",
            'body' => "Proposal {$proposalType} dengan judul '{$this->proposal->title}' telah berhasil disubmit oleh {$this->submitter->name} pada tanggal " . now()->format('d/m/Y H:i') . '. Proposal ini sedang menunggu review dan persetujuan dari Dekan. Tim reviewer akan ditentukan kemudian oleh Kepala LPPM. Mohon menunggu informasi selanjutnya melalui sistem notifikasi.',
            'proposal_id' => $this->proposal->id,
            'submitter_id' => $this->submitter->id,
            'submitter_name' => $this->submitter->name,
            'proposal_type' => $isResearch ? 'research' : 'community_service',
            'link' => route($routeName, $this->proposal),
            'icon' => 'file-text',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): ProposalSubmittedMail
    {
        return (new ProposalSubmittedMail(
            $this->proposal,
            $this->submitter,
            $notifiable
        ))->to($notifiable->email);
    }
}
