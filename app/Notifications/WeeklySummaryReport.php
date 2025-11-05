<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklySummaryReport extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $role,
        public array $data
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'weekly_summary_report',
            'title' => 'Laporan Mingguan',
            'message' => "Laporan mingguan untuk Anda - Minggu {$this->data['week']} {$this->data['year']}",
            'body' => $this->generateBody(),
            'role' => $this->role,
            'data' => $this->data,
            'link' => route('dashboard'),
            'icon' => 'trending-up',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('[SIM LPPM] Laporan Mingguan')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line("Berikut adalah laporan mingguan Anda untuk **Minggu {$this->data['week']} - {$this->data['year']}**.");

        if ($this->role === 'dekan') {
            $message->line('📊 **Ringkasan Minggu Ini:**')
                ->line("- Proposal Baru Masuk: {$this->data['new_proposals']}")
                ->line("- Proposal Disetujui: {$this->data['approved']}")
                ->line("- Proposal Ditolak: {$this->data['rejected']}")
                ->line("- Menunggu Persetujuan: {$this->data['pending']}");
        } elseif ($this->role === 'kepala lppm') {
            $message->line('📊 **Ringkasan Minggu Ini:**')
                ->line("- Total Proposal Aktif: {$this->data['total_active']}")
                ->line("- Proposal Dalam Review: {$this->data['under_review']}")
                ->line("- Review Selesai: {$this->data['reviewed']}")
                ->line("- Proposal Selesai: {$this->data['completed']}");
        } elseif ($this->role === 'rektor') {
            $message->line('📊 **Ringkasan Mingguan Sistem:**')
                ->line("- Total Proposal: {$this->data['total_proposals']}")
                ->line("- Proposal Selesai Minggu Ini: {$this->data['completed_this_week']}")
                ->line("- Rata-rata Waktu Proses: {$this->data['avg_process_time']} hari")
                ->line("- Tingkat Persetujuan: {$this->data['approval_rate']}%");
        }

        return $message->action('Lihat Dashboard', route('dashboard'))
            ->line('Silakan login ke sistem untuk melihat detail lengkap dan analisis mendalam.');
    }

    private function generateBody(): string
    {
        return "Laporan mingguan untuk minggu {$this->data['week']} {$this->data['year']}. Silakan login ke sistem untuk melihat detail lengkap dan analisis mendalam.";
    }
}
