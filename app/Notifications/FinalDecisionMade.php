<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FinalDecisionMade extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public string $decision, // 'approved', 'rejected', 'revision_needed'
        public User $kepalaLppm
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';

        $data = match ($this->decision) {
            'approved' => [
                'title' => 'Proposal Disetujui',
                'message' => "Proposal '{$this->proposal->title}' telah disetujui dan selesai",
                'body' => "Kepala LPPM telah menyetujui proposal {$proposalType} dengan judul '{$this->proposal->title}'. Selamat! Proposal Anda telah melalui semua tahap review dan mendapat persetujuan final. Selanjutnya, mohon untuk mulai melaksanakan kegiatan sesuai dengan jadwal yang telah direncanakan. Jangan lupa untuk melaporkan kemajuan kegiatan melalui sistem pelaporan yang tersedia. Terima kasih atas kontribusi Anda dalam mengembangkan penelitian dan pengabdian masyarakat di ITSNU.",
                'icon' => 'award',
                'emailSubject' => "[SIM LPPM] Keputusan Akhir: {$this->proposal->title} - Disetujui",
            ],
            'rejected' => [
                'title' => 'Proposal Ditolak',
                'message' => "Proposal '{$this->proposal->title}' telah ditolak",
                'body' => "Kepala LPPM meninjau proposal {$proposalType} dengan judul '{$this->proposal->title}' dan mengambil keputusan untuk menolaknya. Keputusan ini mempertimbangkan hasil review dari para reviewer dan kesesuaian dengan criteria yang ditetapkan. Mohon jangan menyerah dan silakan memperbaiki proposal Anda berdasarkan feedback yang diberikan. Anda dapat mengajukan kembali proposal yang telah direvisi di periode mendatang.",
                'icon' => 'x-circle',
                'emailSubject' => "[SIM LPPM] Keputusan Akhir: {$this->proposal->title} - Ditolak",
            ],
            'revision_needed' => [
                'title' => 'Proposal Memerlukan Revisi',
                'message' => "Proposal '{$this->proposal->title}' memerlukan perbaikan dan revisi",
                'body' => "Kepala LPPM meninjau proposal {$proposalType} dengan judul '{$this->proposal->title}' dan meminta perbaikan/revisi sebelum dapat disetujui. Mohon untuk segera memperbaiki proposal sesuai dengan catatan dan rekomendasi yang diberikan. Setelah revisi selesai, silakan submit ulang proposal untuk direview kembali. Tim reviewer akan memeriksa apakah revisi telah sesuai dengan yang diharapkan.",
                'icon' => 'edit',
                'emailSubject' => "[SIM LPPM] Keputusan Akhir: {$this->proposal->title} - Memerlukan Revisi",
            ],
            default => [
                'title' => 'Keputusan Final',
                'message' => "Keputusan final telah dibuat untuk proposal '{$this->proposal->title}'",
                'body' => "Kepala LPPM telah mengambil keputusan final terhadap proposal {$proposalType} dengan judul '{$this->proposal->title}'. Mohon melihat detail keputusan dan catatan yang diberikan melalui sistem.",
                'icon' => 'alert-circle',
                'emailSubject' => "[SIM LPPM] Keputusan Akhir: {$this->proposal->title}",
            ],
        };

        return [
            'type' => 'final_decision_made',
            'decision' => $this->decision,
            'title' => $data['title'],
            'message' => $data['message'],
            'body' => $data['body'],
            'proposal_id' => $this->proposal->id,
            'kepala_lppm_id' => $this->kepalaLppm->id,
            'kepala_lppm_name' => $this->kepalaLppm->name,
            'proposal_type' => $this->proposal->getMorphClass(),
            'link' => route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => $data['icon'],
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $proposalType = $this->proposal->getMorphClass() === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';

        $data = match ($this->decision) {
            'approved' => [
                'title' => 'Proposal Disetujui',
                'subject' => "[SIM LPPM] Keputusan Akhir: {$this->proposal->title} - Disetujui",
                'line1' => "Kepala LPPM telah menyetujui proposal {$proposalType} berikut:",
                'line2' => 'Selamat! Proposal Anda telah melalui semua tahap review dan mendapat persetujuan final.',
                'icon' => 'award',
            ],
            'rejected' => [
                'title' => 'Proposal Ditolak',
                'subject' => "[SIM LPPM] Keputusan Akhir: {$this->proposal->title} - Ditolak",
                'line1' => "Kepala LPPM meninjau proposal {$proposalType} berikut dan mengambil keputusan untuk menolaknya:",
                'line2' => 'Mohon jangan menyerah dan silakan memperbaiki proposal Anda berdasarkan feedback yang diberikan.',
                'icon' => 'x-circle',
            ],
            'revision_needed' => [
                'title' => 'Proposal Memerlukan Revisi',
                'subject' => "[SIM LPPM] Keputusan Akhir: {$this->proposal->title} - Memerlukan Revisi",
                'line1' => "Kepala LPPM meninjau proposal {$proposalType} berikut dan meminta perbaikan/revisi:",
                'line2' => 'Mohon untuk segera memperbaiki proposal sesuai dengan catatan dan rekomendasi yang diberikan.',
                'icon' => 'edit',
            ],
            default => [
                'title' => 'Keputusan Final',
                'subject' => "[SIM LPPM] Keputusan Akhir: {$this->proposal->title}",
                'line1' => "Kepala LPPM telah mengambil keputusan final terhadap proposal {$proposalType} berikut:",
                'line2' => 'Mohon melihat detail keputusan dan catatan yang diberikan melalui sistem.',
                'icon' => 'alert-circle',
            ],
        };

        $mail = \Illuminate\Notifications\Messages\MailMessage::from('noreply@lppm-itsnu.ac.id', 'SIM LPPM ITSNU')
            ->subject($data['subject'])
            ->greeting("Halo {$notifiable->name},")
            ->line($data['line1'])
            ->line('Detail Proposal:')
            ->line("• Judul: {$this->proposal->title}")
            ->line("• Kepala LPPM: {$this->kepalaLppm->name}")
            ->line('• Tanggal Keputusan: '.now()->format('d/m/Y H:i'))
            ->line($data['line2'])
            ->action('Lihat Proposal', route($this->proposal->getMorphClass() === 'research' ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal))
            ->line('Terima kasih atas perhatian Anda.')
            ->salutation('Salam,<br>Tim SIM LPPM ITSNU');

        return $mail;
    }
}
