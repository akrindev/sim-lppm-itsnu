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

class FinalDecisionMadeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Proposal $proposal,
        public string $decision,
        public User $kepalaLppm,
        public User $recipient
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = match ($this->decision) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision_needed' => 'Memerlukan Revisi',
            default => 'Keputusan Akhir',
        };

        return new Envelope(
            subject: "[SIM LPPM] Keputusan Akhir: {$this->proposal->title} - {$status}",
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
            markdown: 'mail.proposals.final-decision',
            with: [
                'recipientName' => $this->recipient->name,
                'proposalType' => $proposalType,
                'proposalTitle' => $this->proposal->title,
                'kepalaLppmName' => $this->kepalaLppm->name,
                'decisionDate' => now()->format('d/m/Y H:i'),
                'decision' => $this->decision,
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
