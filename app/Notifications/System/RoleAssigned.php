<?php

namespace App\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $roleName,
        public string $roleLabel
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'role_assigned',
            'title' => 'Role Baru Diberikan',
            'message' => "Anda telah ditugaskan sebagai {$this->roleLabel}",
            'body' => "Selamat! Anda sekarang memiliki peran {$this->roleLabel} di SIM LPPM. Anda sekarang dapat mengakses fitur dan informasi yang sesuai dengan peran ini.",
            'icon' => 'user-check',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $permissions = $this->getRolePermissions();

        $message = (new MailMessage)
            ->subject('[SIM LPPM] Role Baru Diberikan')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line("🎉 Selamat! Anda sekarang memiliki peran **{$this->roleLabel}** di SIM LPPM.")
            ->line('Dengan peran ini, Anda dapat mengakses fitur-fitur berikut:')
            ->line('');

        foreach ($permissions as $permission) {
            $message->line("- {$permission}");
        }

        return $message->line('')
            ->action('Buka Dashboard', route('dashboard'))
            ->line('Terima kasih telah menjadi bagian dari SIM LPPM ITSNU.');
    }

    private function getRolePermissions(): array
    {
        return match ($this->roleName) {
            'dosen' => [
                'Membuat dan mengelola proposal penelitian',
                'Membuat dan mengelola proposal pengabdian masyarakat',
                'Melihat status proposal',
                'Mengelola tim proposal',
            ],
            'dekan' => [
                'Menyetujui atau menolak proposal',
                'Melihat semua proposal di fakultas',
                'Memberikan catatan dan rekomendasi',
            ],
            'kepala lppm' => [
                'Mengelola semua proposal',
                'Menugaskan reviewer',
                'Memberikan keputusan final',
                'Melihat laporan lengkap',
            ],
            'reviewer' => [
                'Mereview proposal yang ditugaskan',
                'Memberikan evaluasi dan rekomendasi',
                'Melihat riwayat review',
            ],
            'admin lppm' => [
                'Mengelola pengguna',
                'Mengelola master data',
                'Menugaskan reviewer',
                'Melihat semua laporan',
            ],
            default => ['Akses dasar ke sistem'],
        };
    }
}
