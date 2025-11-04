<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\DekanApprovalDecision;
use App\Notifications\FinalDecisionMade;
use App\Notifications\ProposalSubmitted;
use App\Notifications\ReviewCompleted;
use App\Notifications\ReviewerAssigned;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send notification to a single user
     */
    public function send(User $user, mixed $notification): void
    {
        $user->notify($notification);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMany(Collection|array $users, mixed $notification): void
    {
        foreach ($users as $user) {
            $this->send($user, $notification);
        }
    }

    /**
     * Send Proposal Submitted notification
     */
    public function notifyProposalSubmitted($proposal, $submitter, array $recipients): void
    {
        $notification = new ProposalSubmitted($proposal, $submitter);
        $this->sendToMany($recipients, $notification);
    }

    /**
     * Send Dekan Approval Decision notification
     */
    public function notifyDekanApprovalDecision($proposal, string $decision, $dekan, array $recipients): void
    {
        $notification = new DekanApprovalDecision($proposal, $decision, $dekan);
        $this->sendToMany($recipients, $notification);
    }

    /**
     * Send Reviewer Assignment notification
     */
    public function notifyReviewerAssigned($proposal, $reviewer, string $deadline, array $recipients): void
    {
        $notification = new ReviewerAssigned($proposal, $reviewer, $deadline);
        $this->sendToMany($recipients, $notification);
    }

    /**
     * Send Review Completion notification
     */
    public function notifyReviewCompleted($proposal, $reviewer, bool $allComplete = false, array $recipients = []): void
    {
        $notification = new ReviewCompleted($proposal, $reviewer, $allComplete);

        if (!empty($recipients)) {
            $this->sendToMany($recipients, $notification);
        } else {
            // If no specific recipients, send to all proposal stakeholders
            $stakeholders = $this->getProposalStakeholders($proposal);
            $this->sendToMany($stakeholders, $notification);
        }
    }

    /**
     * Send Final Decision Made notification
     */
    public function notifyFinalDecision($proposal, string $decision, $kepalaLppm, array $recipients): void
    {
        $notification = new FinalDecisionMade($proposal, $decision, $kepalaLppm);
        $this->sendToMany($recipients, $notification);
    }

    /**
     * Get all stakeholders of a proposal
     */
    private function getProposalStakeholders($proposal): Collection
    {
        $stakeholders = collect();

        // Add submitter
        $stakeholders->push($proposal->user);

        // Add all team members
        $stakeholders = $stakeholders->merge($proposal->team->pluck('user'));

        // Get relevant admins based on proposal type
        if ($proposal->getMorphClass() === 'research') {
            $stakeholders = $stakeholders->merge(
                User::role(['admin lppm', 'kepala lppm'])->get()
            );
        }

        // Remove duplicates
        return $stakeholders->unique('id')->values();
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(array|string $roles): Collection
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return User::role($roles)->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(User $user, string $notificationId): void
    {
        $user->notifications()->where('id', $notificationId)->update([
            'read_at' => now(),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications()->update([
            'read_at' => now(),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }
}
