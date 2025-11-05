<?php

namespace App\Notifications;

use App\Mail\Reports\WeeklySummaryMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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

    public function toMail(object $notifiable): WeeklySummaryMail
    {
        return (new WeeklySummaryMail(
            $this->role,
            $this->data,
            $notifiable->name
        ))->to($notifiable->email);
    }

    private function generateBody(): string
    {
        return "Laporan mingguan untuk minggu {$this->data['week']} {$this->data['year']}. Silakan login ke sistem untuk melihat detail lengkap dan analisis mendalam.";
    }
}
