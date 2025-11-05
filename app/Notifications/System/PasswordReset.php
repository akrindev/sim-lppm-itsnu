<?php

namespace App\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordReset extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $resetToken
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'password_reset',
            'title' => 'Reset Password',
            'message' => 'Permintaan reset password telah dikirim',
            'body' => 'Anda telah meminta reset password. Silakan klik link di bawah untuk membuat password baru. Link ini akan kadaluarsa dalam 60 menit.',
            'icon' => 'key',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->resetToken,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('[SIM LPPM] Reset Password')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
            ->action('Reset Password', $resetUrl)
            ->line('Link reset password ini akan berlaku selama 60 menit.')
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.');
    }
}
