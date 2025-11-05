<?php

namespace App\Notifications\System;

use App\Mail\System\TwoFactorAuthenticationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TwoFactorAuthentication extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $code,
        public int $expiresIn = 5
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'two_factor_auth',
            'title' => 'Kode Autentikasi Dua Faktor',
            'message' => "Kode verifikasi Anda: {$this->code}",
            'body' => "Gunakan kode {$this->code} untuk menyelesaikan login Anda. Kode akan berlaku selama {$this->expiresIn} menit.",
            'icon' => 'shield-check',
            'urgent' => true,
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): TwoFactorAuthenticationMail
    {
        return (new TwoFactorAuthenticationMail(
            $this->code,
            $notifiable->name,
            $this->expiresIn
        ))->to($notifiable->email);
    }
}
