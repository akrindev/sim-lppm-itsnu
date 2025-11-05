<?php

namespace App\Notifications;

use App\Mail\Team\InvitationAcceptedMail;
use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeamInvitationAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $acceptedMember
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isResearch = $this->proposal->detailable instanceof Research;
        $proposalType = $isResearch ? 'Penelitian' : 'Pengabdian Masyarakat';

        return [
            'type' => 'team_invitation_accepted',
            'title' => 'Anggota Tim Menerima Undangan',
            'message' => "{$this->acceptedMember->name} telah menerima undangan menjadi anggota tim",
            'body' => "{$this->acceptedMember->name} telah menerima undangan untuk bergabung sebagai anggota tim dalam proposal {$proposalType} dengan judul \"{$this->proposal->title}\". Tim proposal ini semakin lengkap. Pastikan semua anggota tim telah memberikan persetujuan sebelum proposal dapat diajukan.",
            'proposal_id' => $this->proposal->id,
            'proposal_title' => $this->proposal->title,
            'proposal_type' => $isResearch ? 'research' : 'community_service',
            'member_id' => $this->acceptedMember->id,
            'member_name' => $this->acceptedMember->name,
            'link' => route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => 'check-circle',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): InvitationAcceptedMail
    {
        return (new InvitationAcceptedMail(
            $this->proposal,
            $this->acceptedMember,
            $notifiable
        ))->to($notifiable->email);
    }
}
