<?php

namespace App\Notifications;

use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
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
        $isResearch = $this->proposal->detailable instanceof Research;
        $proposalType = $isResearch ? 'Penelitian' : 'Pengabdian Masyarakat';

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
            'proposal_type' => $isResearch ? 'research' : 'community_service',
            'link' => route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => $data['icon'],
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isResearch = $this->proposal->detailable instanceof Research;
        $proposalType = $isResearch ? 'Penelitian' : 'Pengabdian Masyarakat';
        $url = route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal);

        $subject = match ($this->decision) {
            'completed' => 'Proposal Disetujui - Selesai',
            'rejected' => 'Proposal Ditolak',
            'revision_needed' => 'Proposal Perlu Revisi',
            default => 'Keputusan Final untuk Proposal',
        };

        $message = (new MailMessage)
            ->subject('[SIM LPPM] '.$subject)
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line("Keputusan final telah dibuat untuk proposal **{$this->proposal->title}**.");

        if ($this->decision === 'completed') {
            $message->line('🎉 **Status:** Disetujui dan Selesai')
                ->line('Selamat! Proposal Anda telah disetujui dan dapat dilanjutkan ke tahap implementasi.');
        } elseif ($this->decision === 'rejected') {
            $message->line('❌ **Status:** Ditolak')
                ->line('Proposal Anda ditolak. Silakan hubungi admin untuk informasi lebih lanjut.');
        } elseif ($this->decision === 'revision_needed') {
            $message->line('📝 **Status:** Perlu Revisi')
                ->line('Proposal Anda memerlukan revisi. Silakan perbaiki sesuai dengan catatan reviewer.');
        }

        return $message->line("**Jenis Proposal:** {$proposalType}")
            ->line("**Diputuskan oleh:** {$this->kepalaLppm->name}")
            ->action('Lihat Detail Proposal', $url)
            ->line('Terima kasih atas partisipasi Anda dalam sistem LPPM ITSNU.');
    }
}
