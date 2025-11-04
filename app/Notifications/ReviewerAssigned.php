<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReviewerAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $reviewer,
        public string $reviewDeadline
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';

        return [
            'type' => 'reviewer_assigned',
            'title' => 'Anda Ditugaskan Sebagai Reviewer',
            'message' => "Anda ditugaskan untuk mereview proposal '{$this->proposal->title}'",
            'body' => "Anda telah ditugaskan sebagai reviewer untuk proposal {$proposalType} dengan judul '{$this->proposal->title}'. Mohon melakukan review sesuai dengan panduan reviewer yang tersedia di sistem. Batas waktu review adalah tanggal {$this->reviewDeadline}. Silakan akses proposal melalui link di bawah ini untuk memulai proses review. Pastikan untuk memberikan evaluasi yang objektif dan konstruktif sesuai dengan kriteria yang telah ditetapkan.",
            'proposal_id' => $this->proposal->id,
            'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => $this->reviewer->name,
            'review_deadline' => $this->reviewDeadline,
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => 'user-check',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';
        $mail = \Illuminate\Notifications\Messages\MailMessage::from('noreply@lppm-itsnu.ac.id', 'SIM LPPM ITSNU')
            ->subject("[SIM LPPM] Ditunjuk sebagai Reviewer - {$this->proposal->title}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Anda telah ditugaskan sebagai reviewer untuk proposal {$proposalType} berikut:")
            ->line('Detail Proposal:')
            ->line("• Judul: {$this->proposal->title}")
            ->line("• Batas Waktu Review: {$this->reviewDeadline}")
            ->line('Silakan lakukan review sesuai dengan panduan reviewer yang tersedia di sistem.')
            ->action('Lihat Proposal', route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal))
            ->line('Terima kasih atas kontribusi Anda sebagai reviewer.')
            ->salutation('Salam,<br>Tim SIM LPPM ITSNU');

        return $mail;
    }
}
