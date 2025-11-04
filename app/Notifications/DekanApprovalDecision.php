<?php

namespace App\Notifications;

use App\Models\Proposal;
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
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';

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
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => $icon,
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $isApproved = $this->decision === 'approved';
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';

        if ($isApproved) {
            $emailSubject = "[SIM LPPM] Keputusan Dekan: {$this->proposal->title} - Disetujui";
            $mail = \Illuminate\Notifications\Messages\MailMessage::from('noreply@lppm-itsnu.ac.id', 'SIM LPPM ITSNU')
                ->subject($emailSubject)
                ->greeting("Halo {$notifiable->name},")
                ->line("Dekan telah menyetujui proposal {$proposalType} berikut:")
                ->line('Detail Proposal:')
                ->line("• Judul: {$this->proposal->title}")
                ->line("• Diajukan oleh: {$this->dekan->name}")
                ->line('• Tanggal Keputusan: '.now()->format('d/m/Y H:i'))
                ->line('Proposal ini telah diteruskan ke Kepala LPPM untuk tahap selanjutnya yaitu penentuan reviewer.')
                ->action('Lihat Proposal', route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal))
                ->line('Terima kasih atas perhatian Anda.')
                ->salutation('Salam,<br>Tim SIM LPPM ITSNU');
        } else {
            $emailSubject = "[SIM LPPM] Keputusan Dekan: {$this->proposal->title} - Memerlukan Persetujuan Tim";
            $mail = \Illuminate\Notifications\Messages\MailMessage::from('noreply@lppm-itsnu.ac.id', 'SIM LPPM ITSNU')
                ->subject($emailSubject)
                ->greeting("Halo {$notifiable->name},")
                ->line("Dekan meninjau proposal {$proposalType} berikut dan meminta persetujuan dari anggota tim:")
                ->line('Detail Proposal:')
                ->line("• Judul: {$this->proposal->title}")
                ->line("• Diajukan oleh: {$this->dekan->name}")
                ->line('• Tanggal Keputusan: '.now()->format('d/m/Y H:i'))
                ->line('Mohon kepada seluruh anggota tim untuk memeriksa dan memberikan persetujuan melalui sistem.')
                ->action('Lihat Proposal', route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal))
                ->line('Terima kasih atas perhatian Anda.')
                ->salutation('Salam,<br>Tim SIM LPPM ITSNU');
        }

        return $mail;
    }
}
