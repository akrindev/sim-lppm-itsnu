<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $reviewer,
        public string $daysRemaining
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
            'type' => 'review_reminder',
            'title' => 'Pengingat: Review Akan Jatuh Tempo',
            'message' => "Pengingat: Review untuk proposal '{$this->proposal->title}' akan jatuh tempo dalam {$this->daysRemaining} hari",
            'body' => "Anda memiliki review yang akan jatuh tempo dalam {$this->daysRemaining} hari untuk proposal {$proposalType} dengan judul '{$this->proposal->title}'. Silakan segera lakukan review sesuai dengan panduan reviewer yang tersedia. Pastikan untuk memberikan evaluasi yang komprehensif dan konstruktif.",
            'proposal_id' => $this->proposal->id,
            'proposal_title' => $this->proposal->title,
            'proposal_type' => $isResearch ? 'research' : 'community_service',
            'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => $this->reviewer->name,
            'days_remaining' => $this->daysRemaining,
            'link' => route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => 'clock',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isResearch = $this->proposal->detailable instanceof Research;
        $proposalType = $isResearch ? 'Penelitian' : 'Pengabdian Masyarakat';
        $url = route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal);

        return (new MailMessage)
            ->subject('[SIM LPPM] Pengingat Review Proposal')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line("Ini adalah pengingat untuk review proposal **{$this->proposal->title}**.")
            ->line("⏰ **Sisa waktu:** {$this->daysRemaining} hari")
            ->line("**Jenis Proposal:** {$proposalType}")
            ->line('Mohon segera menyelesaikan review Anda sebelum batas waktu yang ditentukan.')
            ->action('Lanjutkan Review', $url)
            ->line('Jika Anda mengalami kesulitan, silakan hubungi admin LPPM.');
    }
}
