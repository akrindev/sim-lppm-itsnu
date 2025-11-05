<?php

namespace App\Notifications\System;

use App\Mail\System\RoleAssignedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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

    public function toMail(object $notifiable): RoleAssignedMail
    {
        return (new RoleAssignedMail(
            $this->roleName,
            $this->roleLabel,
            $notifiable->name
        ))->to($notifiable->email);
    }
}
