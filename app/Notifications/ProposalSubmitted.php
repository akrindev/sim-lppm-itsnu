<?php

namespace App\Notifications;

use App\Models\Proposal;
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
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';
        $routeName = $this->proposal->getMorphClass() === 'research'
            ? 'research.proposal.show'
            : 'community-service.proposal.show';

        return [
            'type' => 'proposal_submitted',
            'title' => 'Proposal Baru Disubmit',
            'message' => "Proposal '{$this->proposal->title}' telah disubmit oleh {$this->submitter->name}",
            'body' => "Proposal {$proposalType} dengan judul '{$this->proposal->title}' telah berhasil disubmit oleh {$this->submitter->name} pada tanggal ".now()->format('d/m/Y H:i').'. Proposal ini sedang menunggu review dan persetujuan dari Dekan. Tim reviewer akan ditentukan kemudian oleh Kepala LPPM. Mohon menunggu informasi selanjutnya melalui sistem notifikasi.',
            'proposal_id' => $this->proposal->id,
            'submitter_id' => $this->submitter->id,
            'submitter_name' => $this->submitter->name,
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route($routeName, $this->proposal),
            'icon' => 'file-text',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';
        $routeName = $this->proposal->getMorphClass() === 'research'
            ? 'research.proposal.show'
            : 'community-service.proposal.show';

        $mail = \Illuminate\Notifications\Messages\MailMessage::from('noreply@lppm-itsnu.ac.id', 'SIM LPPM ITSNU')
            ->subject("[SIM LPPM] Proposal Baru: {$this->proposal->title} - Menunggu Persetujuan")
            ->greeting("Halo {$notifiable->name},")
            ->line("Proposal {$proposalType} baru telah disubmit dan menunggu persetujuan.")
            ->line('Detail Proposal:')
            ->line("• Judul: {$this->proposal->title}")
            ->line("• Diajukan oleh: {$this->submitter->name}")
            ->line('• Tanggal Submit: '.now()->format('d/m/Y H:i'))
            ->line('Proposal ini sedang menunggu review dan persetujuan dari Dekan. Tim reviewer akan ditentukan kemudian.')
            ->action('Lihat Proposal', route($routeName, $this->proposal))
            ->line('Terima kasih atas perhatian Anda.')
            ->salutation('Salam,<br>Tim SIM LPPM ITSNU');

        return $mail;
    }
}
