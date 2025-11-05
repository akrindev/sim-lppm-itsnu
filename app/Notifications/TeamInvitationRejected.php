<?php

namespace App\Notifications;

use App\Mail\Team\InvitationRejectedMail;
use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeamInvitationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $rejectedMember
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
            'type' => 'team_invitation_rejected',
            'title' => 'Anggota Tim Menolak Undangan',
            'message' => "{$this->rejectedMember->name} telah menolak undangan menjadi anggota tim",
            'body' => "{$this->rejectedMember->name} telah menolak undangan untuk bergabung sebagai anggota tim dalam proposal {$proposalType} dengan judul \"{$this->proposal->title}\". Silakan cari pengganti atau hubungi anggota tim yang menolak untuk mengetahui alasannya.",
            'proposal_id' => $this->proposal->id,
            'proposal_title' => $this->proposal->title,
            'proposal_type' => $isResearch ? 'research' : 'community_service',
            'member_id' => $this->rejectedMember->id,
            'member_name' => $this->rejectedMember->name,
            'link' => route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => 'x-circle',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): InvitationRejectedMail
    {
        return (new InvitationRejectedMail(
            $this->proposal,
            $this->rejectedMember,
            $notifiable
        ))->to($notifiable->email);
    }
}
