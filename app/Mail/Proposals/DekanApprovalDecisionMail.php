<?php

namespace App\Mail\Proposals;

use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DekanApprovalDecisionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Proposal $proposal,
        public string $decision,
        public User $dekan,
        public User $recipient
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = $this->decision === 'approved' ? 'Disetujui' : 'Memerlukan Persetujuan Tim';

        return new Envelope(
            subject: "[SIM LPPM] Keputusan Dekan: {$this->proposal->title} - {$status}",
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
        $isApproved = $this->decision === 'approved';

        return new Content(
            markdown: 'mail.proposals.dekan-decision',
            with: [
                'recipientName' => $this->recipient->name,
                'proposalType' => $proposalType,
                'proposalTitle' => $this->proposal->title,
                'dekanName' => $this->dekan->name,
                'decisionDate' => now()->format('d/m/Y H:i'),
                'isApproved' => $isApproved,
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
