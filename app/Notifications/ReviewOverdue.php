<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewOverdue extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $reviewer,
        public string $daysOverdue
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
            'type' => 'review_overdue',
            'title' => 'URGENT: Review Sudah Melewati Batas Waktu',
            'message' => "⚠️ URGENT: Review untuk proposal '{$this->proposal->title}' sudah {$this->daysOverdue} hari melewati batas waktu",
            'body' => "Review Anda untuk proposal {$proposalType} dengan judul '{$this->proposal->title}' sudah {$this->daysOverdue} hari melewati batas waktu yang ditentukan. Ini adalah prioritas tinggi dan perlu segera diselesaikan untuk tidak menghambat proses proposal. Silakan segera lakukan review dan submit hasilnya.",
            'proposal_id' => $this->proposal->id,
            'proposal_title' => $this->proposal->title,
            'proposal_type' => $isResearch ? 'research' : 'community_service',
            'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => $this->reviewer->name,
            'days_overdue' => $this->daysOverdue,
            'link' => route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => 'alert-triangle',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isResearch = $this->proposal->detailable instanceof Research;
        $proposalType = $isResearch ? 'Penelitian' : 'Pengabdian Masyarakat';
        $url = route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal);

        return (new MailMessage)
            ->subject('[SIM LPPM] ⚠️ Review Terlambat')
            ->error()
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line("⚠️ Review Anda untuk proposal **{$this->proposal->title}** sudah melewati batas waktu.")
            ->line("**Terlambat:** {$this->daysOverdue} hari")
            ->line("**Jenis Proposal:** {$proposalType}")
            ->line('Mohon segera menyelesaikan review Anda. Keterlambatan dapat menghambat proses evaluasi proposal.')
            ->action('Selesaikan Review Sekarang', $url)
            ->line('Jika ada kendala, segera hubungi admin LPPM.');
    }
}
