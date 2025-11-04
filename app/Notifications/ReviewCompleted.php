<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReviewCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $reviewer,
        public bool $allReviewsComplete = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';

        if ($this->allReviewsComplete) {
            return [
                'type' => 'all_reviews_completed',
                'title' => 'Semua Review Selesai',
                'message' => "Semua reviewer telah menyelesaikan review untuk proposal '{$this->proposal->title}'",
                'body' => "Semua reviewer telah menyelesaikan proses review untuk proposal {$proposalType} dengan judul '{$this->proposal->title}'. Review dari setiap reviewer telah diterima dan dicatat dalam sistem. Proposal ini sekarang menunggu keputusan akhir dari Kepala LPPM. Keputusan final akan mempertimbangkan seluruh hasil review dan rekomendasi dari reviewer. Mohon menunggu informasi selanjutnya melalui sistem notifikasi.",
                'proposal_id' => $this->proposal->id,
                'proposal_type' => $this->proposal->getMorphClass(),
                'link' => route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
                'icon' => 'check-square',
                'created_at' => now()->toISOString(),
            ];
        }

        return [
            'type' => 'review_completed',
            'title' => 'Review Selesai',
            'message' => "Reviewer {$this->reviewer->name} telah menyelesaikan review untuk proposal '{$this->proposal->title}'",
            'body' => "Reviewer {$this->reviewer->name} telah menyelesaikan review untuk proposal {$proposalType} dengan judul '{$this->proposal->title}'. Hasil review telah diterima dan dicatat dalam sistem. Proposal ini masih menunggu review dari reviewer lainnya sebelum dapat memasuki tahap keputusan akhir. Mohon menunggu informasi selanjutnya.",
            'proposal_id' => $this->proposal->id,
            'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => $this->reviewer->name,
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => 'check-circle',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';

        if ($this->allReviewsComplete) {
            $mail = \Illuminate\Notifications\Messages\MailMessage::from('noreply@lppm-itsnu.ac.id', 'SIM LPPM ITSNU')
                ->subject("[SIM LPPM] Semua Review Selesai - {$this->proposal->title} - Menunggu Keputusan Akhir")
                ->greeting("Halo {$notifiable->name},")
                ->line("Semua reviewer telah menyelesaikan proses review untuk proposal {$proposalType} berikut:")
                ->line('Detail Proposal:')
                ->line("• Judul: {$this->proposal->title}")
                ->line("• Reviewer: {$this->reviewer->name}")
                ->line('• Tanggal Selesai: '.now()->format('d/m/Y H:i'))
                ->line('Proposal ini sekarang menunggu keputusan akhir dari Kepala LPPM.')
                ->action('Lihat Proposal', route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal))
                ->line('Terima kasih atas perhatian Anda.')
                ->salutation('Salam,<br>Tim SIM LPPM ITSNU');
        } else {
            $mail = \Illuminate\Notifications\Messages\MailMessage::from('noreply@lppm-itsnu.ac.id', 'SIM LPPM ITSNU')
                ->subject("[SIM LPPM] Review Selesai - {$this->proposal->title}")
                ->greeting("Halo {$notifiable->name},")
                ->line("Reviewer {$this->reviewer->name} telah menyelesaikan review untuk proposal {$proposalType} berikut:")
                ->line('Detail Proposal:')
                ->line("• Judul: {$this->proposal->title}")
                ->line("• Reviewer: {$this->reviewer->name}")
                ->line('• Tanggal Selesai: '.now()->format('d/m/Y H:i'))
                ->line('Proposal ini masih menunggu review dari reviewer lainnya.')
                ->action('Lihat Proposal', route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal))
                ->line('Terima kasih atas perhatian Anda.')
                ->salutation('Salam,<br>Tim SIM LPPM ITSNU');
        }

        return $mail;
    }
}
