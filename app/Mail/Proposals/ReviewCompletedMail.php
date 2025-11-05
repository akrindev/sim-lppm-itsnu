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

class ReviewCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Proposal $proposal,
        public User $reviewer,
        public User $recipient,
        public bool $allReviewsComplete = false
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->allReviewsComplete
            ? "[SIM LPPM] Semua Review Selesai - {$this->proposal->title} - Menunggu Keputusan Akhir"
            : "[SIM LPPM] Review Selesai - {$this->proposal->title}";

        return new Envelope(
            subject: $subject,
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
            markdown: 'mail.proposals.review-completed',
            with: [
                'recipientName' => $this->recipient->name,
                'proposalType' => $proposalType,
                'proposalTitle' => $this->proposal->title,
                'reviewerName' => $this->reviewer->name,
                'completedDate' => now()->format('d/m/Y H:i'),
                'allReviewsComplete' => $this->allReviewsComplete,
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
