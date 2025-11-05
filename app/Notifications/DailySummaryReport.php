<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailySummaryReport extends Notification implements ShouldQueue
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
            'type' => 'daily_summary_report',
            'title' => 'Laporan Ringkas Harian',
            'message' => "Laporan ringkas untuk Anda - {$this->data['date']}",
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
            ->subject('[SIM LPPM] Laporan Ringkas Harian')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line("Berikut adalah laporan ringkas harian Anda untuk tanggal **{$this->data['date']}**.");

        if ($this->role === 'dosen') {
            $message->line('📊 **Statistik Anda:**')
                ->line("- Proposal Aktif: {$this->data['active_proposals']}")
                ->line("- Proposal Pending: {$this->data['pending_proposals']}")
                ->line("- Review Selesai: {$this->data['completed_reviews']}");
        } elseif ($this->role === 'reviewer') {
            $message->line('📊 **Statistik Review:**')
                ->line("- Review Pending: {$this->data['pending_reviews']}")
                ->line("- Review Selesai: {$this->data['completed_reviews']}")
                ->line("- Review Mendekati Deadline: {$this->data['near_deadline']}");
        } elseif (in_array($this->role, ['dekan', 'kepala lppm', 'rektor'])) {
            $message->line('📊 **Statistik Sistem:**')
                ->line("- Total Proposal: {$this->data['total_proposals']}")
                ->line("- Menunggu Persetujuan: {$this->data['pending_approval']}")
                ->line("- Selesai: {$this->data['completed']}");
        }

        return $message->action('Lihat Dashboard', route('dashboard'))
            ->line('Silakan login ke sistem untuk melihat detail lengkap.');
    }

    private function generateBody(): string
    {
        return "Laporan ringkas harian untuk peran Anda pada {$this->data['date']}. Silakan login ke sistem untuk melihat detail lengkap.";
    }
}
