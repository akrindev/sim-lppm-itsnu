<?php

namespace App\Mail\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoleAssignedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $roleName,
        public string $roleLabel,
        public string $userName
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[SIM LPPM] ✅ Role Baru: '.$this->roleLabel,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $permissions = $this->getRolePermissions();

        return new Content(
            markdown: 'mail.system.role-assigned',
            with: [
                'userName' => $this->userName,
                'roleLabel' => $this->roleLabel,
                'permissions' => $permissions,
                'url' => route('dashboard'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get role permissions list.
     */
    private function getRolePermissions(): array
    {
        return match ($this->roleName) {
            'dosen' => [
                'Membuat dan mengelola proposal penelitian',
                'Membuat dan mengelola proposal pengabdian masyarakat',
                'Mengundang anggota tim',
                'Melihat status proposal',
            ],
            'reviewer' => [
                'Melihat proposal yang ditugaskan untuk review',
                'Memberi penilaian dan feedback',
                'Membuat laporan review',
            ],
            'dekan' => [
                'Menyetujui atau menolak proposal dari dosen',
                'Melihat dashboard dekan dengan ringkasan proposal',
                'Mengelola data master program studi',
            ],
            'kepala_lppm' => [
                'Menyetujui assignment reviewer',
                'Melihat semua proposal di LPPM',
                'Membuat keputusan akhir tentang proposal',
                'Mengakses laporan komprehensif',
            ],
            'admin_lppm' => [
                'Mengelola reviewer',
                'Membuat assignment reviewer',
                'Melihat semua proposal dan review',
                'Mengakses laporan sistem',
            ],
            'rektor' => [
                'Melihat semua proposal di universitas',
                'Melihat laporan komprehensif',
                'Membuat keputusan akhir',
                'Mengakses analytics dashboard',
            ],
            default => [
                'Mengakses sistem SIM LPPM',
                'Melihat informasi terkait peran Anda',
            ],
        };
    }
}
