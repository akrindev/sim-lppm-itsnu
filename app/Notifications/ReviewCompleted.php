<?php

namespace App\Notifications;

use App\Mail\Proposals\ReviewCompletedMail;
use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReviewCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Proposal $proposal,
        public User $reviewer,
        public bool $allReviewsComplete = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isResearch = $this->proposal->detailable instanceof Research;
        $proposalType = $isResearch ? 'Penelitian' : 'Pengabdian Masyarakat';

        if ($this->allReviewsComplete) {
            return [
                'type' => 'all_reviews_completed',
                'title' => 'Semua Review Selesai',
                'message' => "Semua reviewer telah menyelesaikan review untuk proposal '{$this->proposal->title}'",
                'body' => "Semua reviewer telah menyelesaikan proses review untuk proposal {$proposalType} dengan judul '{$this->proposal->title}'. Review dari setiap reviewer telah diterima dan dicatat dalam sistem. Proposal ini sekarang menunggu keputusan akhir dari Kepala LPPM. Keputusan final akan mempertimbangkan seluruh hasil review dan rekomendasi dari reviewer. Mohon menunggu informasi selanjutnya melalui sistem notifikasi.",
                'proposal_id' => $this->proposal->id,
                'proposal_type' => $isResearch ? 'research' : 'community_service',
                'link' => route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
                'icon' => 'check-square',
                'created_at' => now()->toISOString(),
            ];
        }

        return [
            'type' => 'review_completed',
            'title' => 'Review Selesai',
            'message' => "Reviewer {$this->reviewer->name} telah menyelesaikan review untuk proposal '{$this->proposal->title}'",
            'body' => "Reviewer {$this->reviewer->name} telah menyelesaikan review untuk proposal {$proposalType} dengan judul '{$this->proposal->title}'. Hasil review telah diterima dan dicatat dalam sistem. Proposal ini masih menunggu review dari reviewer lainnya sebelum dapat memasuki tahap keputusan akhir. Mohon menunggu informasi selanjutnya.",
            'proposal_id' => $this->proposal->id,
            'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => $this->reviewer->name,
            'proposal_type' => $isResearch ? 'research' : 'community_service',
            'link' => route($isResearch ? 'research.proposal.show' : 'community-service.proposal.show', $this->proposal),
            'icon' => 'check-circle',
            'created_at' => now()->toISOString(),
        ];
    }

    public function toMail(object $notifiable): ReviewCompletedMail
    {
        return (new ReviewCompletedMail(
            $this->proposal,
            $this->reviewer,
            $notifiable,
            $this->allReviewsComplete
        ))->to($notifiable->email);
    }
}
