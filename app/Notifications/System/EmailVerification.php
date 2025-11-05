<?php

namespace App\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $verificationUrl
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'email_verification',
            'title' => 'Verifikasi Email',
            'message' => 'Silakan verifikasi email Anda',
            'body' => 'Kami perlu memverifikasi email Anda sebelum Anda dapat menggunakan semua fitur aplikasi.',
            'icon' => 'mail',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[SIM LPPM] Verifikasi Email Anda')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Terima kasih telah mendaftar di SIM LPPM ITSNU.')
            ->line('Kami perlu memverifikasi email Anda sebelum Anda dapat menggunakan semua fitur aplikasi.')
            ->action('Verifikasi Email', $this->verificationUrl)
            ->line('Link verifikasi ini akan berlaku selama 60 menit.')
            ->line('Jika Anda tidak mendaftar di sistem kami, abaikan email ini.');
    }
}
