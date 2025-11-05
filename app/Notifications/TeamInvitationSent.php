<?php

namespace App\Notifications;

use App\Mail\Team\InvitationSentMail;
use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeamInvitationSent extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $inviter,
        public User $invitee
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
            'type' => 'team_invitation_sent',
            'title' => 'Undangan Menjadi Anggota Tim',
            'message' => "{$this->inviter->name} mengundang Anda menjadi anggota tim proposal {$proposalType}",
            'body' => "Anda telah diundang oleh {$this->inviter->name} untuk bergabung sebagai anggota tim dalam proposal {$proposalType} dengan judul \"{$this->proposal->title}\". Silakan terima atau tolak undangan ini. Proposal ini sedang dalam proses penyusunan dan membutuhkan persetujuan dari semua anggota tim sebelum dapat diajukan.",
            'proposal_id' => $this->proposal->id,
            'proposal_title' => $this->proposal->title,
            'proposal_type' => $isResearch ? 'research' : 'community_service',
            'inviter_id' => $this->inviter->id,
            'inviter_name' => $this->inviter->name,
            'link' => route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => 'user-plus',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): InvitationSentMail
    {
        return (new InvitationSentMail(
            $this->proposal,
            $this->inviter,
            $notifiable
        ))->to($notifiable->email);
    }
}
