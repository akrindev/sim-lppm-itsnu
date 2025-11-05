<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\Research;
use App\Models\User;
use App\Notifications\DailySummaryReport;
use App\Notifications\DekanApprovalDecision;
use App\Notifications\FinalDecisionMade;
use App\Notifications\ProposalSubmitted;
use App\Notifications\ReviewCompleted;
use App\Notifications\ReviewerAssigned;
use App\Notifications\ReviewOverdue;
use App\Notifications\ReviewReminder;
use App\Notifications\System\EmailVerification;
use App\Notifications\System\PasswordReset;
use App\Notifications\System\RoleAssigned;
use App\Notifications\System\TwoFactorAuthentication;
use App\Notifications\TeamInvitationAccepted;
use App\Notifications\TeamInvitationRejected;
use App\Notifications\TeamInvitationSent;
use App\Notifications\WeeklySummaryReport;
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

        if (! empty($recipients)) {
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
        $stakeholders->push($proposal->submitter);

        // Add all team members
        $stakeholders = $stakeholders->merge($proposal->teamMembers);

        // Get relevant admins based on proposal type
        if ($proposal->detailable instanceof Research) {
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

    /**
     * Send Team Invitation notification
     */
    public function notifyTeamInvitationSent($proposal, $inviter, $invitee): void
    {
        $notification = new TeamInvitationSent($proposal, $inviter, $invitee);
        $this->send($invitee, $notification);
    }

    /**
     * Send Team Invitation Accepted notification
     */
    public function notifyTeamInvitationAccepted($proposal, $acceptedMember, $acceptedBy): void
    {
        $recipients = [
            $proposal->submitter,
            ...$this->getUsersByRole(['admin lppm'])->toArray(),
        ];

        $notification = new TeamInvitationAccepted($proposal, $acceptedMember, $acceptedBy);
        $this->sendToMany($recipients, $notification);
    }

    /**
     * Send Team Invitation Rejected notification
     */
    public function notifyTeamInvitationRejected($proposal, $rejectedMember, $rejectedBy): void
    {
        $recipients = [
            $proposal->submitter,
            ...$this->getUsersByRole(['admin lppm'])->toArray(),
        ];

        $notification = new TeamInvitationRejected($proposal, $rejectedMember, $rejectedBy);
        $this->sendToMany($recipients, $notification);
    }

    /**
     * Send Review Reminder notification (3 days before deadline)
     */
    public function notifyReviewReminder($proposal, $reviewer, int $daysRemaining): void
    {
        $notification = new ReviewReminder($proposal, $reviewer, $daysRemaining);
        $this->send($reviewer, $notification);
    }

    /**
     * Send Review Overdue notification
     */
    public function notifyReviewOverdue($proposal, $reviewer, int $daysOverdue): void
    {
        $adminRecipients = $this->getUsersByRole(['admin lppm', 'kepala lppm'])->toArray();
        $recipients = array_merge([$reviewer], $adminRecipients);

        $notification = new ReviewOverdue($proposal, $reviewer, $daysOverdue);
        $this->sendToMany($recipients, $notification);
    }

    /**
     * Send Daily Summary Report notification
     */
    public function notifyDailySummaryReport(string $role, array $data): void
    {
        $recipients = $this->getUsersByRole($role)->toArray();
        $notification = new DailySummaryReport($role, $data);
        $this->sendToMany($recipients, $notification);
    }

    /**
     * Send Weekly Summary Report notification
     */
    public function notifyWeeklySummaryReport(string $role, array $data): void
    {
        $recipients = $this->getUsersByRole($role)->toArray();
        $notification = new WeeklySummaryReport($role, $data);
        $this->sendToMany($recipients, $notification);
    }

    /**
     * Send Password Reset notification
     */
    public function notifyPasswordReset(User $user, string $resetToken): void
    {
        $notification = new PasswordReset($resetToken);
        $this->send($user, $notification);
    }

    /**
     * Send Email Verification notification
     */
    public function notifyEmailVerification(User $user, string $verificationUrl): void
    {
        $notification = new EmailVerification($verificationUrl);
        $this->send($user, $notification);
    }

    /**
     * Send Two Factor Authentication notification
     */
    public function notifyTwoFactorAuthentication(User $user, string $code, int $expiresIn = 5): void
    {
        $notification = new TwoFactorAuthentication($code, $expiresIn);
        $this->send($user, $notification);
    }

    /**
     * Send Role Assigned notification
     */
    public function notifyRoleAssigned(User $user, string $roleName, string $roleLabel): void
    {
        $notification = new RoleAssigned($roleName, $roleLabel);
        $this->send($user, $notification);
    }
}
