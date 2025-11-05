<?php

namespace App\Notifications;

use App\Mail\Proposals\DekanApprovalDecisionMail;
use App\Models\Proposal;
use App\Models\Research;
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
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isApproved = $this->decision === 'approved';
        $isResearch = $this->proposal->detailable instanceof Research;
        $proposalType = $isResearch ? 'Penelitian' : 'Pengabdian Masyarakat';

        if ($isApproved) {
            $title = 'Proposal Disetujui Dekan';
            $message = "Proposal '{$this->proposal->title}' telah disetujui Dekan dan diteruskan ke Kepala LPPM";
            $body = "Dekan telah menyetujui proposal {$proposalType} dengan judul '{$this->proposal->title}'. Proposal ini telah diteruskan ke Kepala LPPM untuk tahap selanjutnya yaitu penentuan reviewer. Tim reviewer akan dipilih berdasarkan bidang keahlian yang sesuai dengan topik proposal. Proses review akan dimulai setelah reviewer ditetapkan.";
            $icon = 'check-circle';
            $emailSubject = "[SIM LPPM] Keputusan Dekan: {$this->proposal->title} - Disetujui";
        } else {
            $title = 'Proposal Memerlukan Persetujuan Anggota Tim';
            $message = "Proposal '{$this->proposal->title}' memerlukan persetujuan dari anggota tim yang tersisa";
            $body = "Dekan meninjau proposal {$proposalType} dengan judul '{$this->proposal->title}' dan meminta persetujuan dari anggota tim yang belum memberikan persetujuan. Mohon kepada seluruh anggota tim untuk memeriksa dan memberikan persetujuan melalui sistem. Proposal tidak dapat melanjutkan ke tahap selanjutnya hingga semua anggota tim memberikan persetujuan.";
            $icon = 'alert-circle';
            $emailSubject = "[SIM LPPM] Keputusan Dekan: {$this->proposal->title} - Memerlukan Persetujuan Tim";
        }

        return [
            'type' => 'dekan_approval_decision',
            'decision' => $this->decision,
            'title' => $title,
            'message' => $message,
            'body' => $body,
            'proposal_id' => $this->proposal->id,
            'dekan_id' => $this->dekan->id,
            'dekan_name' => $this->dekan->name,
            'proposal_type' => $isResearch ? 'research' : 'community_service',
            'link' => route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => $icon,
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): DekanApprovalDecisionMail
    {
        return (new DekanApprovalDecisionMail(
            $this->proposal,
            $this->decision,
            $this->dekan,
            $notifiable
        ))->to($notifiable->email);
    }
}
