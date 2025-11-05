<?php

namespace App\Notifications\System;

use App\Mail\System\EmailVerificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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

    public function toMail(object $notifiable): EmailVerificationMail
    {
        return (new EmailVerificationMail(
            $this->verificationUrl,
            $notifiable->name
        ))->to($notifiable->email);
    }
}
