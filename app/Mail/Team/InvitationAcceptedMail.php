<?php

namespace App\Mail\Team;

use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationAcceptedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Proposal $proposal,
        public User $acceptedMember,
        public User $recipient
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[SIM LPPM] ✅ Anggota Tim Menerima Undangan - {$this->proposal->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $isResearch = $this->proposal->detailable instanceof Research;
        $proposalType = $isResearch ? 'Penelitian' : 'Pengabdian Masyarakat';
        $routeName = $isResearch ? 'research.proposal.show' : 'community-service.proposal.show';

        return new Content(
            markdown: 'mail.team.invitation-accepted',
            with: [
                'recipientName' => $this->recipient->name,
                'proposalType' => $proposalType,
                'proposalTitle' => $this->proposal->title,
                'memberName' => $this->acceptedMember->name,
                'acceptedDate' => now()->format('d/m/Y H:i'),
                'url' => route($routeName, $this->proposal),
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
}
